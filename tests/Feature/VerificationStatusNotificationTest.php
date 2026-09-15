<?php

namespace Tests\Feature;

use App\Models\Craftman;
use App\Models\Customer;
use App\Models\User;
use App\Models\Verification;
use App\Notifications\VerificationStatusChanged;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use RuntimeException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class VerificationStatusNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_approval_notifies_only_verification_owner_with_safe_payload(): void
    {
        Notification::fake();
        $admin = $this->adminWithVerificationPermission();
        $verification = Verification::factory()->create(['status' => 'pending']);
        $craftman = $verification->craftman;
        $otherCraftman = Craftman::factory()->create();
        $customer = Customer::factory()->create();

        $this->actingAs($admin, 'admin')->put(route('verifications.update', $verification), [
            'status' => 'approved',
            'craftman_id' => $otherCraftman->id,
        ])->assertRedirect(route('verifications.index'));

        $this->assertSame('approved', $verification->fresh()->status);
        Notification::assertSentToTimes($craftman->user, VerificationStatusChanged::class, 1);
        Notification::assertNotSentTo($otherCraftman->user, VerificationStatusChanged::class);
        Notification::assertNotSentTo($customer->user, VerificationStatusChanged::class);

        $sent = Notification::sent($craftman->user, VerificationStatusChanged::class)->sole();
        $data = $sent->toDatabase($craftman->user);
        $this->assertSame([
            'event' => 'verification_status_changed',
            'title' => 'تم قبول طلب التوثيق',
            'message' => 'تمت الموافقة على طلب توثيق حسابك بنجاح.',
            'verification_id' => $verification->id,
            'status' => 'approved',
            'destination' => 'herafy.verification.show',
        ], $data);
    }

    public function test_rejection_notifies_owner_without_inventing_a_reason_field(): void
    {
        Notification::fake();
        $admin = $this->adminWithVerificationPermission();
        $verification = Verification::factory()->create(['status' => 'pending']);

        $this->actingAs($admin, 'admin')->put(route('verifications.update', $verification), [
            'status' => 'rejected',
            'reason' => '<script>browser supplied</script>',
        ])->assertRedirect(route('verifications.index'));

        $sent = Notification::sent($verification->craftman->user, VerificationStatusChanged::class)->sole();
        $data = $sent->toDatabase($verification->craftman->user);
        $this->assertSame('rejected', $data['status']);
        $this->assertSame('تم رفض طلب التوثيق', $data['title']);
        $this->assertSame('تم رفض طلب التوثيق. يمكنك مراجعة التفاصيل وتحديث بياناتك.', $data['message']);
        $this->assertArrayNotHasKey('reason', $data);
        $this->assertStringNotContainsString('browser supplied', json_encode($data));
    }

    public function test_database_notification_begins_unread_and_targets_craftman_user(): void
    {
        $admin = $this->adminWithVerificationPermission();
        $verification = Verification::factory()->create(['status' => 'pending']);
        $this->changeStatus($admin, $verification, 'approved');

        $notification = $verification->craftman->user->notifications()->sole();
        $this->assertNull($notification->read_at);
        $this->assertSame($verification->craftman->user->id, $notification->notifiable_id);
        $this->assertSame(User::class, $notification->notifiable_type);
    }

    public function test_same_status_is_a_no_op_and_does_not_create_duplicate_notification(): void
    {
        $admin = $this->adminWithVerificationPermission();
        $verification = Verification::factory()->create(['status' => 'pending']);
        $this->changeStatus($admin, $verification, 'approved');
        $this->changeStatus($admin, $verification, 'approved');

        $this->assertSame(1, $verification->craftman->user->notifications()
            ->where('type', VerificationStatusChanged::class)->count());
    }

    public function test_failed_and_unauthorized_updates_create_no_notification(): void
    {
        Notification::fake();
        $verification = Verification::factory()->create(['status' => 'pending']);
        $unauthorizedAdmin = $this->adminWithVerificationPermission(false);
        $this->actingAs($unauthorizedAdmin, 'admin')
            ->put(route('verifications.update', $verification), ['status' => 'approved'])
            ->assertForbidden();
        Notification::assertNothingSent();

        $admin = $this->adminWithVerificationPermission();
        DB::listen(function (QueryExecuted $query): void {
            if (str_starts_with(strtolower($query->sql), 'update "verifications"')) {
                throw new RuntimeException('Simulated verification update failure.');
            }
        });
        $this->withoutExceptionHandling();

        try {
            $this->changeStatus($admin, $verification, 'rejected');
            $this->fail('Expected verification update failure.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Simulated verification update failure.', $exception->getMessage());
        }

        Notification::assertNothingSent();
        $this->assertSame('pending', $verification->fresh()->status);
    }

    public function test_invalid_owner_mapping_blocks_status_change_and_notification(): void
    {
        Notification::fake();
        $admin = $this->adminWithVerificationPermission();
        $verification = Verification::factory()->create(['status' => 'pending']);
        $verification->craftman->user->update(['actor_id' => 999999]);

        $this->changeStatus($admin, $verification, 'approved')->assertConflict();

        Notification::assertNothingSent();
        $this->assertSame('pending', $verification->fresh()->status);
    }

    public function test_craftman_ui_displays_and_reads_notification_with_safe_redirect(): void
    {
        $admin = $this->adminWithVerificationPermission();
        $verification = Verification::factory()->create(['status' => 'pending']);
        $craftman = $verification->craftman;
        $this->changeStatus($admin, $verification, 'rejected');
        auth('admin')->logout();
        $notification = $craftman->user->notifications()->sole();

        $this->actingAs($craftman->user, 'craftman')->get(route('herafy.craftman.dashboard'))
            ->assertOk()
            ->assertSee('data-testid="notification-unread-count">1<', false)
            ->assertSee('تم رفض طلب التوثيق');
        $this->get(route('herafy.craftman.notifications.index'))
            ->assertOk()
            ->assertSee('تم رفض طلب التوثيق')
            ->assertSee('يمكنك مراجعة التفاصيل');
        $this->patch(route('herafy.craftman.notifications.read', $notification->id))
            ->assertRedirect(route('herafy.verification.show'));
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_another_craftman_cannot_read_notification_and_deleted_verification_is_not_exposed(): void
    {
        $admin = $this->adminWithVerificationPermission();
        $verification = Verification::factory()->create(['status' => 'pending']);
        $craftman = $verification->craftman;
        $this->changeStatus($admin, $verification, 'approved');
        auth('admin')->logout();
        $notification = $craftman->user->notifications()->sole();
        $otherCraftman = Craftman::factory()->create();

        $this->actingAs($otherCraftman->user, 'craftman')
            ->patch(route('herafy.craftman.notifications.read', $notification->id))
            ->assertNotFound();
        $this->assertNull($notification->fresh()->read_at);

        $verification->delete();
        $this->actingAs($craftman->user, 'craftman')
            ->patch(route('herafy.craftman.notifications.read', $notification->id))
            ->assertRedirect(route('herafy.craftman.notifications.index'));
        $this->assertNotNull($notification->fresh()->read_at);
    }

    private function changeStatus(User $admin, Verification $verification, string $status)
    {
        return $this->actingAs($admin, 'admin')->put(
            route('verifications.update', $verification),
            compact('status')
        );
    }

    private function adminWithVerificationPermission(bool $authorized = true): User
    {
        $admin = User::factory()->create([
            'status' => 'active',
            'actor_type' => null,
            'actor_id' => null,
        ]);
        $role = Role::firstOrCreate(['name' => 'verification-admin', 'guard_name' => 'admin']);
        $admin->assignRole($role);

        if ($authorized) {
            $permission = Permission::firstOrCreate([
                'name' => 'verifications.update',
                'guard_name' => 'admin',
            ]);
            $role->givePermissionTo($permission);
        }

        return $admin;
    }
}
