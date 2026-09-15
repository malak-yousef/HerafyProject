<?php

namespace Tests\Feature;

use App\Models\Craftman;
use App\Models\Customer;
use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CraftmanPortfolioManagementTest extends TestCase
{
    use RefreshDatabase;

    private string $coverDirectory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->coverDirectory = storage_path('framework/testing/craftman-portfolios-'.uniqid());
        File::ensureDirectoryExists($this->coverDirectory);
        config(['filesystems.portfolio_cover_path' => $this->coverDirectory]);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->coverDirectory);
        parent::tearDown();
    }

    public function test_craftman_sees_portfolio_navigation_and_empty_state(): void
    {
        $craftman = Craftman::factory()->create();

        $this->actingAs($craftman->user, 'craftman')
            ->get(route('herafy.craftman.dashboard'))
            ->assertOk()
            ->assertSee('معرض أعمالي')
            ->assertSee(route('herafy.craftman.portfolios.index'), false);

        $this->get(route('herafy.craftman.portfolios.index'))
            ->assertOk()
            ->assertSee('معرضك فارغ حاليًا')
            ->assertSee('إضافة عمل جديد');
    }

    public function test_craftman_creates_portfolio_owned_by_their_profile(): void
    {
        $craftman = Craftman::factory()->create();

        $this->actingAs($craftman->user, 'craftman')
            ->post(route('herafy.craftman.portfolios.store'), $this->payload())
            ->assertRedirect(route('herafy.craftman.portfolios.index'))
            ->assertSessionHas('success');

        $portfolio = Portfolio::sole();
        $this->assertSame($craftman->id, $portfolio->craftman_id);
        $this->assertTrue($portfolio->status);
        $this->assertFileExists($this->coverDirectory.DIRECTORY_SEPARATOR.$portfolio->portfolioCover_image);
    }

    public function test_craftman_can_only_update_and_soft_delete_their_own_portfolio(): void
    {
        $owner = Craftman::factory()->create();
        $other = Craftman::factory()->create();
        $portfolio = Portfolio::factory()->create(['craftman_id' => $owner->id]);

        $this->actingAs($other->user, 'craftman')
            ->get(route('herafy.craftman.portfolios.edit', $portfolio))
            ->assertNotFound();
        $this->delete(route('herafy.craftman.portfolios.destroy', $portfolio))->assertNotFound();
        $this->assertNotSoftDeleted($portfolio);

        auth('craftman')->logout();
        $this->actingAs($owner->user, 'craftman')
            ->put(route('herafy.craftman.portfolios.update', $portfolio), [
                'title' => 'مطبخ خشبي محدث',
                'description' => 'وصف محدث للعمل المنفذ.',
                'location' => 'غزة',
                'project_date' => now()->subDay()->toDateString(),
            ])->assertRedirect(route('herafy.craftman.portfolios.index'));

        $this->assertSame('مطبخ خشبي محدث', $portfolio->fresh()->title);
        $this->delete(route('herafy.craftman.portfolios.destroy', $portfolio))
            ->assertRedirect(route('herafy.craftman.portfolios.index'));
        $this->assertSoftDeleted($portfolio);
    }

    public function test_customer_and_guest_cannot_manage_craftman_portfolios(): void
    {
        $this->get(route('herafy.craftman.portfolios.index'))->assertRedirect(route('login'));

        $customer = Customer::factory()->create();
        $this->actingAs($customer->user, 'customer')
            ->get(route('herafy.craftman.portfolios.index'))
            ->assertRedirect(route('login'));
    }

    public function test_craftman_created_portfolio_is_visible_in_admin_management(): void
    {
        $craftman = Craftman::factory()->create();

        $this->actingAs($craftman->user, 'craftman')
            ->post(route('herafy.craftman.portfolios.store'), $this->payload())
            ->assertRedirect(route('herafy.craftman.portfolios.index'));

        auth('craftman')->logout();

        $this->actingAs($this->adminWith('portfolios.view'), 'admin')
            ->get(route('portfolios.index'))
            ->assertOk()
            ->assertSee('مطبخ خشبي حديث')
            ->assertSee($craftman->user->name);
    }

    private function payload(): array
    {
        return [
            'title' => 'مطبخ خشبي حديث',
            'description' => 'تنفيذ مطبخ كامل مع خزائن خشبية.',
            'location' => 'غزة',
            'project_date' => now()->subWeek()->toDateString(),
            'portfolioCover_image' => $this->fakePng('cover.png'),
        ];
    }

    private function fakePng(string $name): UploadedFile
    {
        return UploadedFile::fake()->createWithContent(
            $name,
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=', true)
        );
    }

    private function adminWith(string $permission): User
    {
        $admin = User::factory()->create([
            'status' => 'active',
            'actor_type' => null,
            'actor_id' => null,
        ]);
        $role = Role::create(['name' => 'craftman-portfolio-admin', 'guard_name' => 'admin']);
        $admin->roles()->attach($role);
        $role->givePermissionTo(Permission::firstOrCreate([
            'name' => $permission,
            'guard_name' => 'admin',
        ]));

        return $admin;
    }
}
