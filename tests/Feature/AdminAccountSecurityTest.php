<?php

namespace Tests\Feature;

use App\Models\Craftman;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AdminAccountSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_targets_are_scoped_to_role_backed_actor_null_users(): void
    {
        $actor = $this->adminWithPermissions(['admins.view', 'admins.update', 'admins.delete']);
        $target = $this->admin();
        $customer = Customer::factory()->create();
        $craftman = Craftman::factory()->create();

        $this->actingAs($actor, 'admin');
        $this->get(route('admins.show', $target))->assertOk();
        $this->get(route('admins.edit', $target))->assertOk();

        foreach ([$customer->user, $craftman->user] as $marketplaceUser) {
            $originalType = $marketplaceUser->actor_type;
            $originalId = $marketplaceUser->actor_id;

            $this->get(route('admins.edit', $marketplaceUser))->assertNotFound();
            $this->postJson(route('admins_update', $marketplaceUser), $this->updatePayload())
                ->assertNotFound();
            $this->deleteJson(route('admins.destroy', $marketplaceUser))->assertNotFound();

            $marketplaceUser->refresh();
            $this->assertSame($originalType, $marketplaceUser->actor_type);
            $this->assertSame($originalId, $marketplaceUser->actor_id);

            $marketplaceUser->delete();
            $this->patch(route('admins_restore', $marketplaceUser))->assertNotFound();
            $this->delete(route('admins_force', $marketplaceUser))->assertNotFound();
        }
    }

    public function test_edit_never_renders_password_hash_and_blank_password_preserves_it(): void
    {
        $actor = $this->adminWithPermissions(['admins.update']);
        $target = $this->admin();
        $hash = $target->password;

        $response = $this->actingAs($actor, 'admin')->get(route('admins.edit', $target));
        $response->assertOk()->assertDontSee($hash, false);
        $this->assertSame(1, substr_count($response->getContent(), 'id="admin-password"'));
        $this->assertStringContainsString('id="admin-password" type="password" name="password"', $response->getContent());

        $this->postJson(route('admins_update', $target), $this->updatePayload([
            'name' => 'Changed Name',
            'password' => '',
            'password_confirmation' => '',
        ]))->assertOk();

        $this->assertSame($hash, $target->refresh()->password);
    }

    public function test_edit_hashes_a_confirmed_new_password_and_keeps_actor_invariant(): void
    {
        $actor = $this->adminWithPermissions(['admins.update']);
        $target = $this->admin();

        $this->actingAs($actor, 'admin')->postJson(route('admins_update', $target), $this->updatePayload([
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
            'actor_type' => Customer::class,
            'actor_id' => 999,
        ]))->assertOk();

        $target->refresh();
        $this->assertTrue(Hash::check('NewPassword123', $target->password));
        $this->assertNull($target->actor_type);
        $this->assertNull($target->actor_id);
    }

    public function test_create_password_is_required_confirmed_and_never_flashed(): void
    {
        $actor = $this->adminWithPermissions(['admins.create']);
        $this->actingAs($actor, 'admin');

        $this->postJson(route('admins.store'), $this->createPayload(['password' => null, 'password_confirmation' => null]))
            ->assertUnprocessable()->assertJsonValidationErrors('password');
        $this->postJson(route('admins.store'), $this->createPayload(['password_confirmation' => 'Different123']))
            ->assertUnprocessable()->assertJsonValidationErrors('password');

        $this->from(route('admins.create'))->post(route('admins.store'), $this->createPayload([
            'password_confirmation' => 'Different123',
        ]))->assertRedirect(route('admins.create'));
        $this->assertNull(session()->getOldInput('password'));
        $this->assertNull(session()->getOldInput('password_confirmation'));
    }

    public function test_ordinary_admin_can_assign_allowed_role_but_not_super_admin(): void
    {
        $actor = $this->adminWithPermissions(['admins.create']);
        $allowed = Role::findOrCreate('content-admin', 'admin');
        Role::findOrCreate('super-admin', 'admin');

        $this->actingAs($actor, 'admin')->get(route('admins.create'))
            ->assertOk()->assertSee($allowed->name)->assertDontSee('super-admin');

        $this->postJson(route('admins.store'), $this->createPayload())->assertOk();
        $this->assertTrue(User::where('email', 'new-admin@example.com')->firstOrFail()->hasRole($allowed));

        $this->postJson(route('admins.store'), $this->createPayload([
            'email' => 'forged@example.com',
            'user_name' => 'forged_admin',
            'role_name' => 'super-admin',
        ]))->assertForbidden();
    }

    public function test_only_super_admin_can_manage_or_create_super_admins(): void
    {
        $ordinary = $this->adminWithPermissions(['admins.update', 'admins.delete']);
        $super = $this->admin('super-admin');

        $this->actingAs($ordinary, 'admin')->get(route('admins.edit', $super))->assertForbidden();
        $this->postJson(route('admins_update', $super), $this->updatePayload([
            'status' => 'blocked',
            'password' => 'Takeover123',
            'password_confirmation' => 'Takeover123',
        ]))->assertForbidden();
        $this->deleteJson(route('admins.destroy', $super))->assertForbidden();

        $superHash = $super->password;
        $this->assertSame('active', $super->refresh()->status);
        $this->assertSame($superHash, $super->password);

        $this->actingAs($super, 'admin')->postJson(route('admins.store'), $this->createPayload([
            'email' => 'second-super@example.com',
            'user_name' => 'second_super',
            'role_name' => 'super-admin',
        ]))->assertOk();
        $this->assertTrue(User::where('email', 'second-super@example.com')->firstOrFail()->hasRole('super-admin'));
    }

    public function test_non_super_admin_cannot_force_delete_a_trashed_super_admin(): void
    {
        $ordinary = $this->adminWithPermissions(['admins.delete']);
        $super = $this->admin('super-admin');
        $super->delete();

        $this->actingAs($ordinary, 'admin')->delete(route('admins_force', $super))->assertForbidden();
        $this->assertNotNull(User::withTrashed()->find($super->id));
    }

    public function test_status_and_profile_validation_use_canonical_values(): void
    {
        $actor = $this->adminWithPermissions(['admins.update']);
        $target = $this->admin();
        $this->actingAs($actor, 'admin');

        foreach (['inactive', 'pending', 'blocked'] as $status) {
            $this->postJson(route('admins_update', $target), $this->updatePayload(['status' => $status]))->assertOk();
        }

        $this->postJson(route('admins_update', $target), $this->updatePayload(['status' => 'in_active']))
            ->assertUnprocessable()->assertJsonValidationErrors('status');
        $this->postJson(route('admins_update', $target), $this->updatePayload(['date' => 'not-a-date']))
            ->assertUnprocessable()->assertJsonValidationErrors('date');
        $this->postJson(route('admins_update', $target), $this->updatePayload(['address' => str_repeat('x', 256)]))
            ->assertUnprocessable()->assertJsonValidationErrors('address');
    }

    public function test_admin_cannot_delete_or_deactivate_their_current_account(): void
    {
        $actor = $this->adminWithPermissions(['admins.update', 'admins.delete']);
        $this->actingAs($actor, 'admin');

        $this->postJson(route('admins_update', $actor), $this->updatePayload(['status' => 'blocked']))
            ->assertForbidden();
        $this->deleteJson(route('admins.destroy', $actor))->assertForbidden();
        $this->assertSame('active', $actor->refresh()->status);
        $this->assertNull($actor->deleted_at);
    }

    private function adminWithPermissions(array $permissions): User
    {
        $role = Role::findOrCreate('operator-'.count($permissions).'-'.uniqid(), 'admin');
        foreach ($permissions as $permission) {
            $role->givePermissionTo(Permission::findOrCreate($permission, 'admin'));
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $this->admin($role->name);
    }

    private function admin(string $roleName = 'content-admin'): User
    {
        $role = Role::findOrCreate($roleName, 'admin');
        $admin = User::factory()->create([
            'actor_type' => null,
            'actor_id' => null,
            'status' => 'active',
            'password' => Hash::make('CurrentPassword123'),
        ]);
        $admin->assignRole($role);

        return $admin;
    }

    private function createPayload(array $overrides = []): array
    {
        Role::findOrCreate('content-admin', 'admin');

        return array_merge([
            'name' => 'New Admin',
            'user_name' => 'new_admin',
            'email' => 'new-admin@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'mobile' => '0599999999',
            'gender' => 'male',
            'address' => 'Gaza',
            'date' => '2000-01-01',
            'status' => 'active',
            'role_name' => 'content-admin',
        ], $overrides);
    }

    private function updatePayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Managed Admin',
            'user_name' => 'managed_'.uniqid(),
            'email' => uniqid().'@example.com',
            'mobile' => '0599999999',
            'gender' => 'male',
            'address' => 'Gaza',
            'date' => '2000-01-01',
            'status' => 'active',
        ], $overrides);
    }
}
