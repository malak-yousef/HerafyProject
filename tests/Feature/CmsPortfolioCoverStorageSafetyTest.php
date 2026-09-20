<?php

namespace Tests\Feature;

use App\Models\Craftman;
use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CmsPortfolioCoverStorageSafetyTest extends TestCase
{
    use RefreshDatabase;

    private string $coverDirectory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->coverDirectory = storage_path('framework/testing/portfolio-covers-'.uniqid());
        File::ensureDirectoryExists($this->coverDirectory);
        config(['filesystems.portfolio_cover_path' => $this->coverDirectory]);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->coverDirectory);

        parent::tearDown();
    }

    public function test_create_persists_cover_with_collision_resistant_filename(): void
    {
        $this->actingAs($this->adminWith('portfolios.create'), 'admin')
            ->post(route('portfolios.store'), $this->payload([
                'portfolioCover_image' => $this->fakePng('cover.png'),
            ]))
            ->assertRedirect(route('portfolios.index'));

        $portfolio = Portfolio::sole();
        $this->assertMatchesRegularExpression('/^\d{14}_[A-Za-z0-9]{20}\.(?:png|webp)$/', $portfolio->portfolioCover_image);
        $this->assertFileExists($this->coverPath($portfolio->portfolioCover_image));
    }

    public function test_create_database_failure_removes_uploaded_cover_and_record(): void
    {
        $this->actingAs($this->adminWith('portfolios.create'), 'admin');

        try {
            $this->withoutExceptionHandling()->post(route('portfolios.store'), $this->payload([
                'craftman_id' => 999999,
                'portfolioCover_image' => $this->fakePng('cover.png'),
            ]));
            $this->fail('Expected the database write to fail.');
        } catch (QueryException) {
            $this->assertDatabaseCount('portfolios', 0);
            $this->assertSame([], File::files($this->coverDirectory));
        }
    }

    public function test_successful_replacement_keeps_new_cover_and_deletes_old_cover(): void
    {
        $portfolio = $this->portfolioWithCover('old-cover.jpg');

        $this->actingAs($this->adminWith('portfolios.update'), 'admin')
            ->call('POST', route('portfolios.update', $portfolio), $this->payload([
                '_method' => 'PUT',
            ], $portfolio->craftman_id), [], ['portfolioCover_image' => $this->fakePng('replacement.png')])
            ->assertRedirect(route('portfolios.index'));

        $newCover = $portfolio->fresh()->portfolioCover_image;
        $this->assertNotSame('old-cover.jpg', $newCover);
        $this->assertFileExists($this->coverPath($newCover));
        $this->assertFileDoesNotExist($this->coverPath('old-cover.jpg'));
    }

    public function test_update_without_replacement_keeps_existing_cover_and_file(): void
    {
        $portfolio = $this->portfolioWithCover('old-cover.jpg');

        $this->actingAs($this->adminWith('portfolios.update'), 'admin')
            ->put(route('portfolios.update', $portfolio), $this->payload([], $portfolio->craftman_id))
            ->assertRedirect(route('portfolios.index'));

        $this->assertSame('old-cover.jpg', $portfolio->fresh()->portfolioCover_image);
        $this->assertFileExists($this->coverPath('old-cover.jpg'));
    }

    public function test_failed_replacement_removes_new_cover_and_preserves_old_cover(): void
    {
        $portfolio = $this->portfolioWithCover('old-cover.jpg');
        $this->actingAs($this->adminWith('portfolios.update'), 'admin');

        try {
            $this->withoutExceptionHandling()->call('POST', route('portfolios.update', $portfolio), $this->payload([
                '_method' => 'PUT',
                'craftman_id' => 999999,
            ], $portfolio->craftman_id), [], ['portfolioCover_image' => $this->fakePng('replacement.png')]);
            $this->fail('Expected the database write to fail.');
        } catch (QueryException) {
            $this->assertSame('old-cover.jpg', $portfolio->fresh()->portfolioCover_image);
            $this->assertFileExists($this->coverPath('old-cover.jpg'));
            $this->assertCount(1, File::files($this->coverDirectory));
        }
    }

    public function test_soft_delete_and_restore_preserve_cover(): void
    {
        $portfolio = $this->portfolioWithCover('restorable-cover.jpg');
        $admin = $this->adminWith('portfolios.delete');

        $this->actingAs($admin, 'admin')->delete(route('portfolios.destroy', $portfolio));
        $this->assertSoftDeleted($portfolio);
        $this->assertFileExists($this->coverPath('restorable-cover.jpg'));

        $this->patch(route('portfolios_restore', $portfolio->id))->assertRedirect(route('portfolios_trashed'));
        $this->assertNotSoftDeleted($portfolio->fresh());
        $this->assertSame('restorable-cover.jpg', $portfolio->fresh()->portfolioCover_image);
        $this->assertFileExists($this->coverPath('restorable-cover.jpg'));
    }

    public function test_force_delete_removes_cover_and_handles_missing_or_suspicious_files_safely(): void
    {
        $admin = $this->adminWith('portfolios.delete');
        $portfolio = $this->portfolioWithCover('delete-me.jpg');
        $portfolio->delete();
        $unrelated = $this->writeCover('unrelated.jpg');

        $this->actingAs($admin, 'admin')
            ->delete(route('portfolios_force_destroy', $portfolio->id))
            ->assertRedirect(route('portfolios_trashed'));

        $this->assertDatabaseMissing('portfolios', ['id' => $portfolio->id]);
        $this->assertFileDoesNotExist($this->coverPath('delete-me.jpg'));
        $this->assertFileExists($unrelated);

        foreach (['already-missing.jpg', '../unrelated.jpg'] as $cover) {
            $missing = Portfolio::factory()->create([
                'craftman_id' => Craftman::factory()->create()->id,
                'portfolioCover_image' => $cover,
            ]);
            $missing->delete();
            $this->delete(route('portfolios_force_destroy', $missing->id))->assertRedirect();
            $this->assertDatabaseMissing('portfolios', ['id' => $missing->id]);
            $this->assertFileExists($unrelated);
        }
    }

    public function test_bulk_force_delete_removes_only_trashed_portfolio_covers(): void
    {
        $first = $this->portfolioWithCover('first.jpg');
        $second = $this->portfolioWithCover('missing.jpg', false);
        $active = $this->portfolioWithCover('active.jpg');
        $first->delete();
        $second->delete();

        $this->actingAs($this->adminWith('portfolios.delete'), 'admin')
            ->delete(route('portfolios_destroy_all'), ['ids' => [$first->id, $second->id]])
            ->assertRedirect(route('portfolios_trashed'));

        $this->assertDatabaseMissing('portfolios', ['id' => $first->id]);
        $this->assertDatabaseMissing('portfolios', ['id' => $second->id]);
        $this->assertDatabaseHas('portfolios', ['id' => $active->id, 'deleted_at' => null]);
        $this->assertFileDoesNotExist($this->coverPath('first.jpg'));
        $this->assertFileExists($this->coverPath('active.jpg'));
    }

    private function payload(array $overrides = [], ?int $craftmanId = null): array
    {
        return array_merge([
            'title' => 'Portfolio title',
            'description' => 'Portfolio description',
            'location' => 'Gaza',
            'status' => '1',
            'craftman_id' => $craftmanId ?? Craftman::factory()->create()->id,
        ], $overrides);
    }

    private function portfolioWithCover(string $filename, bool $writeFile = true): Portfolio
    {
        if ($writeFile) {
            $this->writeCover($filename);
        }

        return Portfolio::factory()->create([
            'craftman_id' => Craftman::factory()->create()->id,
            'portfolioCover_image' => $filename,
        ]);
    }

    private function writeCover(string $filename): string
    {
        $path = $this->coverPath($filename);
        File::put($path, 'cover');

        return $path;
    }

    private function coverPath(string $filename): string
    {
        return $this->coverDirectory.DIRECTORY_SEPARATOR.$filename;
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
        $admin = User::factory()->create(['status' => 'active', 'actor_type' => null, 'actor_id' => null]);
        $role = Role::create(['name' => 'portfolio-storage-'.fake()->unique()->word(), 'guard_name' => 'admin']);
        $admin->roles()->attach($role);
        $role->givePermissionTo(Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin']));

        return $admin;
    }
}
