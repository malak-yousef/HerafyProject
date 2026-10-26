<?php

namespace Tests\Feature;

use App\Models\Craftman;
use App\Models\Portfolio;
use App\Models\Profession;
use App\Models\Review;
use App\Models\ServiceJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeaturedCraftmenTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_shows_only_active_featured_craftmen_with_real_profile_links(): void
    {
        $profession = Profession::factory()->active()->create(['name' => 'نجارة']);
        $featured = Craftman::factory()->active()->create([
            'profession_id' => $profession->id,
            'is_featured' => true,
        ]);
        $featured->user->update(['name' => 'حرفي مميز']);

        $ordinary = Craftman::factory()->active()->create(['is_featured' => false]);
        $ordinary->user->update(['name' => 'حرفي عادي']);

        $inactive = Craftman::factory()->inactive()->create(['is_featured' => true]);
        $inactive->user->update(['name' => 'حرفي غير نشط']);

        $this->get(route('index'))
            ->assertOk()
            ->assertSee('حرفي مميز')
            ->assertSee('نجارة')
            ->assertSee(route('herafy.craftmen.show', $featured), false)
            ->assertDontSee('حرفي عادي')
            ->assertDontSee('حرفي غير نشط')
            ->assertDontSee('craftsman-profile.html');
    }

    public function test_homepage_limits_featured_cards_to_four(): void
    {
        Craftman::factory()->active()->count(5)->create(['is_featured' => true]);

        $response = $this->get(route('index'))->assertOk();

        $this->assertCount(4, $response->viewData('featuredCraftmen'));
    }

    public function test_homepage_shows_only_active_featured_portfolios_with_real_detail_links(): void
    {
        $craftman = Craftman::factory()->active()->create();
        $featured = Portfolio::factory()->create([
            'craftman_id' => $craftman->id,
            'title' => 'عمل مميز حقيقي',
            'portfolioCover_image' => 'featured-work.jpg',
            'status' => true,
            'is_featured' => true,
        ]);
        Portfolio::factory()->create([
            'craftman_id' => $craftman->id,
            'title' => 'عمل غير مختار',
            'status' => true,
            'is_featured' => false,
        ]);
        Portfolio::factory()->create([
            'craftman_id' => $craftman->id,
            'title' => 'عمل مختار غير نشط',
            'status' => false,
            'is_featured' => true,
        ]);

        $this->get(route('index'))
            ->assertOk()
            ->assertSee('عمل مميز حقيقي')
            ->assertSee(asset('storage/images/portfolio/featured-work.jpg'), false)
            ->assertSee(route('herafy.craftmen.portfolios.show', [$craftman, $featured]), false)
            ->assertDontSee('عمل غير مختار')
            ->assertDontSee('عمل مختار غير نشط');
    }

    public function test_homepage_limits_featured_portfolios_to_six(): void
    {
        $craftman = Craftman::factory()->active()->create();
        Portfolio::factory()->count(7)->create([
            'craftman_id' => $craftman->id,
            'status' => true,
            'is_featured' => true,
        ]);

        $response = $this->get(route('index'))->assertOk();

        $this->assertCount(6, $response->viewData('featuredPortfolios'));
    }

    public function test_homepage_testimonials_and_statistics_use_approved_database_records(): void
    {
        Review::factory()->approved()->create(['rating' => 5, 'comment' => 'رأي معتمد أول']);
        Review::factory()->approved()->create(['rating' => 4, 'comment' => 'رأي معتمد ثان']);
        Review::factory()->approved()->create(['rating' => 2, 'comment' => 'رأي معتمد ثالث']);
        Review::factory()->pending()->create(['rating' => 5, 'comment' => 'رأي معلق مخفي']);
        Review::factory()->rejected()->create(['rating' => 1, 'comment' => 'رأي مرفوض مخفي']);

        $response = $this->get(route('index'))->assertOk();

        $response->assertSee('رأي معتمد أول')
            ->assertSee('رأي معتمد ثان')
            ->assertSee('رأي معتمد ثالث')
            ->assertDontSee('رأي معلق مخفي')
            ->assertDontSee('رأي مرفوض مخفي');

        $stats = $response->viewData('homepageStats');
        $this->assertSame(67, $stats['satisfaction_rate']);
        $this->assertSame(3.7, $stats['average_rating']);
        $this->assertSame(ServiceJob::where('status', 'completed')->count(), $stats['completed_jobs']);
        $this->assertSame(Craftman::query()->publiclyDiscoverable()->count(), $stats['active_craftmen']);
    }
}
