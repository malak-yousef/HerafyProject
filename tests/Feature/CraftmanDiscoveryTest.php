<?php

namespace Tests\Feature;

use App\Models\Craftman;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Portfolio;
use App\Models\Profession;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CraftmanDiscoveryTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_active_valid_non_deleted_craftmen_are_publicly_visible(): void
    {
        $visible = $this->craftman(['name' => 'Visible Craftman']);
        $inactive = $this->craftman(['name' => 'Inactive Craftman', 'status' => 'inactive']);
        $blocked = $this->craftman(['name' => 'Blocked Craftman', 'status' => 'blocked']);
        $deletedCraftman = $this->craftman(['name' => 'Deleted Craftman']);
        $deletedCraftman->delete();
        $deletedUser = $this->craftman(['name' => 'Deleted User Craftman']);
        $deletedUser->user->delete();
        $invalidMapping = $this->craftman(['name' => 'Invalid Mapping Craftman']);
        $invalidMapping->user->update(['actor_id' => 999999]);

        $this->get(route('showCraftsmen'))
            ->assertOk()
            ->assertSee($visible->user->name)
            ->assertDontSee($inactive->user->name)
            ->assertDontSee($blocked->user->name)
            ->assertDontSee('Deleted Craftman')
            ->assertDontSee('Deleted User Craftman')
            ->assertDontSee('Invalid Mapping Craftman');
    }

    public function test_search_matches_name_and_username_and_excludes_unrelated_craftmen(): void
    {
        $byName = $this->craftman(['name' => 'Ahmad Carpenter', 'user_name' => 'wood-master']);
        $byUsername = $this->craftman(['name' => 'Mohammed Worker', 'user_name' => 'ahmad-tools']);
        $unrelated = $this->craftman(['name' => 'Khaled Plumber', 'user_name' => 'pipes-only']);

        $response = $this->get(route('showCraftsmen', ['search' => '  ahmad  ']));
        $response->assertOk()
            ->assertSee($byName->user->name)
            ->assertSee($byUsername->user->name)
            ->assertDontSee($unrelated->user->name)
            ->assertSee('value="ahmad"', false);
    }

    public function test_active_profession_filter_is_database_backed(): void
    {
        $profession = Profession::factory()->create(['name' => 'Carpentry', 'status' => 'active']);
        $otherProfession = Profession::factory()->create(['name' => 'Plumbing', 'status' => 'active']);
        $matching = $this->craftman(['name' => 'Matching Craftman'], ['profession_id' => $profession->id]);
        $other = $this->craftman(['name' => 'Other Craftman'], ['profession_id' => $otherProfession->id]);

        $this->get(route('showCraftsmen', ['profession_id' => $profession->id]))
            ->assertOk()
            ->assertSee($matching->user->name)
            ->assertDontSee($other->user->name)
            ->assertSee('Carpentry')
            ->assertSee('Plumbing');

        $inactiveProfession = Profession::factory()->create(['status' => 'in_active']);
        $this->get(route('showCraftsmen', ['profession_id' => $inactiveProfession->id]))
            ->assertSessionHasErrors('profession_id');
    }

    public function test_sort_whitelist_orders_by_newest_name_and_experience(): void
    {
        $alpha = $this->craftman(['name' => 'Alpha'], ['experience_years' => 8]);
        $beta = $this->craftman(['name' => 'Beta'], ['experience_years' => 2]);
        $gamma = $this->craftman(['name' => 'Gamma'], ['experience_years' => 15]);
        $alpha->forceFill(['created_at' => now()->subDays(3)])->save();
        $beta->forceFill(['created_at' => now()->subDays(2)])->save();
        $gamma->forceFill(['created_at' => now()->subDay()])->save();

        $this->get(route('showCraftsmen'))->assertSeeInOrder(['Gamma', 'Beta', 'Alpha']);
        $this->get(route('showCraftsmen', ['sort' => 'name_asc']))->assertSeeInOrder(['Alpha', 'Beta', 'Gamma']);
        $this->get(route('showCraftsmen', ['sort' => 'name_desc']))->assertSeeInOrder(['Gamma', 'Beta', 'Alpha']);
        $this->get(route('showCraftsmen', ['sort' => 'experience_desc']))->assertSeeInOrder(['Gamma', 'Alpha', 'Beta']);
        $this->get(route('showCraftsmen', ['sort' => 'experience_asc']))->assertSeeInOrder(['Beta', 'Alpha', 'Gamma']);

        $this->get(route('showCraftsmen', ['sort' => 'users.password desc']))
            ->assertSessionHasErrors('sort');
    }

    public function test_rating_uses_only_approved_non_deleted_reviews_and_ignores_stale_columns(): void
    {
        $craftman = $this->craftman(['name' => 'Rated Craftman'], ['rating' => 5, 'total_reviews' => 999]);
        $this->review($craftman, 5, 'approved');
        $this->review($craftman, 3, 'approved');
        $this->review($craftman, 1, 'pending');
        $this->review($craftman, 1, 'rejected');
        $deleted = $this->review($craftman, 1, 'approved');
        $deleted->delete();

        $this->get(route('showCraftsmen'))
            ->assertOk()
            ->assertSee('4.0')
            ->assertSee('(2 تقييم)')
            ->assertDontSee('(999 تقييم)');
    }

    public function test_no_approved_reviews_has_a_real_empty_rating_state(): void
    {
        $craftman = $this->craftman(['name' => 'Unrated Craftman'], ['rating' => 5, 'total_reviews' => 100]);
        $this->review($craftman, 5, 'pending');

        $this->get(route('showCraftsmen'))
            ->assertOk()
            ->assertSee('لا توجد تقييمات بعد')
            ->assertDontSee('(100 تقييم)');
    }

    public function test_pagination_is_bounded_and_preserves_active_query_parameters(): void
    {
        $profession = Profession::factory()->create(['status' => 'active']);
        Craftman::factory()->count(13)->create(['profession_id' => $profession->id])
            ->each(fn (Craftman $craftman, int $index) => $craftman->user->update([
                'name' => "Searchable Craftman {$index}",
            ]));

        $response = $this->get(route('showCraftsmen', [
            'search' => 'Searchable',
            'profession_id' => $profession->id,
            'sort' => 'experience_desc',
        ]));

        $response->assertOk();
        $this->assertCount(12, $response->viewData('craftmen')->items());
        $response->assertSee('page=2', false)
            ->assertSee('search=Searchable', false)
            ->assertSee('profession_id='.$profession->id, false)
            ->assertSee('sort=experience_desc', false);
    }

    public function test_listing_is_public_but_does_not_render_private_data_or_prototype_records(): void
    {
        $craftman = $this->craftman([
            'name' => 'Public Safe Name',
            'email' => 'private-discovery@example.test',
            'mobile' => '0591234567',
            'address' => 'Private exact address',
        ]);

        $this->get(route('showCraftsmen'))
            ->assertOk()
            ->assertSee($craftman->user->name)
            ->assertDontSee($craftman->user->email)
            ->assertDontSee($craftman->user->mobile)
            ->assertDontSee($craftman->user->address)
            ->assertDontSee('localStorage')
            ->assertDontSee('craftsman-profile.html')
            ->assertDontSee('ahmed@example.com');

        $customer = Customer::factory()->create();
        $this->actingAs($customer->user, 'customer')->get(route('showCraftsmen'))->assertOk();
    }

    public function test_listing_links_to_public_craftman_profile(): void
    {
        $craftman = $this->craftman(['name' => 'Public Portfolio Owner']);

        $this->get(route('showCraftsmen'))
            ->assertOk()
            ->assertSee(route('herafy.craftmen.show', $craftman), false)
            ->assertSee('عرض الملف ومعرض الأعمال');
    }

    public function test_public_profile_shows_only_active_portfolios_and_approved_reviews(): void
    {
        $craftman = $this->craftman([
            'name' => 'Visible Public Profile',
            'email' => 'private-profile@example.test',
            'mobile' => '0599999999',
            'address' => 'Private profile address',
        ]);
        $visible = Portfolio::factory()->create(['craftman_id' => $craftman->id, 'status' => true, 'title' => 'Visible Portfolio']);
        Portfolio::factory()->create(['craftman_id' => $craftman->id, 'status' => false, 'title' => 'Hidden Portfolio']);
        $deleted = Portfolio::factory()->create(['craftman_id' => $craftman->id, 'status' => true, 'title' => 'Deleted Portfolio']);
        $deleted->delete();
        $this->review($craftman, 5, 'approved')->update(['comment' => 'Approved public review']);
        $this->review($craftman, 1, 'pending')->update(['comment' => 'Private pending review']);

        $this->get(route('herafy.craftmen.show', $craftman))
            ->assertOk()
            ->assertSee($visible->title)
            ->assertSee('Approved public review')
            ->assertSee(route('request', ['profession_id' => $craftman->profession_id]), false)
            ->assertDontSee('Hidden Portfolio')
            ->assertDontSee('Deleted Portfolio')
            ->assertDontSee('Private pending review')
            ->assertDontSee($craftman->user->email)
            ->assertDontSee($craftman->user->mobile)
            ->assertDontSee($craftman->user->address);
    }

    public function test_public_portfolio_detail_is_owned_active_and_counts_views(): void
    {
        $craftman = $this->craftman();
        $other = $this->craftman();
        $portfolio = Portfolio::factory()->create([
            'craftman_id' => $craftman->id,
            'status' => true,
            'title' => 'Public Work Detail',
            'views_count' => 4,
        ]);
        $hidden = Portfolio::factory()->create(['craftman_id' => $craftman->id, 'status' => false]);

        $this->get(route('herafy.craftmen.portfolios.show', [$craftman, $portfolio]))
            ->assertOk()
            ->assertSee('Public Work Detail');
        $this->assertSame(5, $portfolio->fresh()->views_count);

        $this->get(route('herafy.craftmen.portfolios.show', [$other, $portfolio]))->assertNotFound();
        $this->get(route('herafy.craftmen.portfolios.show', [$craftman, $hidden]))->assertNotFound();
    }

    public function test_non_discoverable_craftman_has_no_public_profile(): void
    {
        $blocked = $this->craftman(['status' => 'blocked']);

        $this->get(route('herafy.craftmen.show', $blocked))->assertNotFound();
    }

    private function craftman(array $userAttributes = [], array $craftmanAttributes = []): Craftman
    {
        $craftman = Craftman::factory()->create($craftmanAttributes);
        $craftman->user->update(array_merge(['status' => 'active'], $userAttributes));

        return $craftman;
    }

    private function review(Craftman $craftman, int $rating, string $status): Review
    {
        $order = Order::factory()->create();

        return Review::factory()->create([
            'craftsman_id' => $craftman->id,
            'customer_id' => $order->customer_id,
            'request_id' => $order->id,
            'rating' => $rating,
            'status' => $status,
        ]);
    }
}
