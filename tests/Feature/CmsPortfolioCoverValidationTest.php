<?php

namespace Tests\Feature;

use App\Models\Craftman;
use App\Models\Portfolio;
use App\Models\PortfolioImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CmsPortfolioCoverValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_without_cover_is_rejected_before_insert(): void
    {
        $this->actingAs($this->adminWith('portfolios.create'), 'admin')
            ->from(route('portfolios.create'))
            ->post(route('portfolios.store'), $this->payload())
            ->assertRedirect(route('portfolios.create'))
            ->assertSessionHasErrors('portfolioCover_image');

        $this->assertDatabaseCount('portfolios', 0);
    }

    public function test_create_with_valid_cover_succeeds(): void
    {
        $response = $this->actingAs($this->adminWith('portfolios.create'), 'admin')
            ->post(route('portfolios.store'), $this->payload([
                'portfolioCover_image' => $this->fakePng('cover.png'),
            ]));

        $response->assertSessionHasNoErrors()->assertRedirect(route('portfolios.index'));
        $portfolio = Portfolio::sole();
        $this->assertNotEmpty($portfolio->portfolioCover_image);
        $this->removeCreatedCover($portfolio->portfolioCover_image);
    }

    public function test_edit_without_replacement_preserves_existing_cover(): void
    {
        $portfolio = $this->portfolio();

        $this->actingAs($this->adminWith('portfolios.update'), 'admin')
            ->put(route('portfolios.update', $portfolio), $this->payload(['title' => 'عنوان محدث'], $portfolio->craftman_id))
            ->assertSessionHasNoErrors();

        $portfolio->refresh();
        $this->assertSame('existing-cover.jpg', $portfolio->portfolioCover_image);
        $this->assertSame('عنوان محدث', $portfolio->title);
    }

    public function test_edit_with_valid_replacement_persists_new_filename(): void
    {
        $portfolio = $this->portfolio();

        $this->actingAs($this->adminWith('portfolios.update'), 'admin')
            ->call('POST', route('portfolios.update', $portfolio), $this->payload([
                '_method' => 'PUT',
            ], $portfolio->craftman_id), [], ['portfolioCover_image' => $this->fakePng('replacement.jpg')])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('portfolios.index'));

        $newCover = $portfolio->fresh()->portfolioCover_image;
        $this->assertNotSame('existing-cover.jpg', $newCover);
        $this->assertMatchesRegularExpression('/\.(?:jpg|webp)$/', $newCover);
        $this->removeCreatedCover($newCover);
    }

    public function test_invalid_replacement_mime_is_rejected_and_existing_cover_remains(): void
    {
        $portfolio = $this->portfolio();

        $this->actingAs($this->adminWith('portfolios.update'), 'admin')
            ->from(route('portfolios.edit', $portfolio))
            ->call('POST', route('portfolios.update', $portfolio), $this->payload([
                '_method' => 'PUT',
                'title' => 'عنوان غير محفوظ',
            ], $portfolio->craftman_id), [], ['portfolioCover_image' => UploadedFile::fake()->create('cover.txt', 10, 'text/plain')])
            ->assertRedirect(route('portfolios.edit', $portfolio))
            ->assertSessionHasErrors('portfolioCover_image');

        $portfolio->refresh();
        $this->assertSame('existing-cover.jpg', $portfolio->portfolioCover_image);
        $this->assertNotSame('عنوان غير محفوظ', $portfolio->title);
    }

    public function test_oversized_replacement_is_rejected_and_existing_cover_remains(): void
    {
        $portfolio = $this->portfolio();

        $this->actingAs($this->adminWith('portfolios.update'), 'admin')
            ->call('POST', route('portfolios.update', $portfolio), $this->payload([
                '_method' => 'PUT',
            ], $portfolio->craftman_id), [], ['portfolioCover_image' => UploadedFile::fake()->create('large.jpg', 2049, 'image/jpeg')])
            ->assertSessionHasErrors('portfolioCover_image');

        $this->assertSame('existing-cover.jpg', $portfolio->fresh()->portfolioCover_image);
    }

    public function test_portfolio_images_relationship_is_unchanged_when_cover_is_updated(): void
    {
        $portfolio = $this->portfolio();
        $image = PortfolioImage::create([
            'portfolio_id' => $portfolio->id,
            'image_path' => 'gallery-image.jpg',
            'image_name' => 'صورة إضافية',
            'image_description' => null,
        ]);

        $this->actingAs($this->adminWith('portfolios.update'), 'admin')
            ->put(route('portfolios.update', $portfolio), $this->payload([], $portfolio->craftman_id))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('portfolio_images', ['id' => $image->id, 'portfolio_id' => $portfolio->id]);
        $this->assertTrue($portfolio->fresh()->portfolio_images->contains($image));
    }

    private function payload(array $overrides = [], ?int $craftmanId = null): array
    {
        return array_merge([
            'title' => 'معرض اختبار',
            'description' => 'وصف معرض الاختبار',
            'location' => 'غزة',
            'status' => '1',
            'craftman_id' => $craftmanId ?? Craftman::factory()->create()->id,
        ], $overrides);
    }

    private function portfolio(): Portfolio
    {
        $craftman = Craftman::factory()->create();

        return Portfolio::factory()->create([
            'craftman_id' => $craftman->id,
            'portfolioCover_image' => 'existing-cover.jpg',
        ]);
    }

    private function adminWith(string $permission): User
    {
        $admin = User::factory()->create(['status' => 'active', 'actor_type' => null, 'actor_id' => null]);
        $role = Role::create(['name' => 'portfolio-cover-'.fake()->unique()->word(), 'guard_name' => 'admin']);
        $admin->roles()->attach($role);
        $role->givePermissionTo(Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin']));

        return $admin;
    }

    private function removeCreatedCover(string $filename): void
    {
        $path = config('filesystems.portfolio_cover_path').DIRECTORY_SEPARATOR.$filename;
        if (is_file($path)) {
            unlink($path);
        }
    }

    private function fakePng(string $name): UploadedFile
    {
        return UploadedFile::fake()->createWithContent(
            $name,
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=', true)
        );
    }
}
