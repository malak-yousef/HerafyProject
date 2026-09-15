<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\City;
use App\Models\Order;
use Database\Factories\AreaFactory;
use Database\Factories\CityFactory;
use Database\Seeders\AreaSeeder;
use Database\Seeders\CitySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GazaLocationSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_deterministic_seeders_create_only_the_five_gaza_location_groups(): void
    {
        $this->seedGazaLocations();

        $this->assertSame(CityFactory::GAZA_LOCATIONS, City::orderBy('id')->pluck('name')->all());
        $this->assertSame(48, Area::count());
    }

    public function test_every_seeded_area_has_the_expected_gaza_parent(): void
    {
        $this->seedGazaLocations();

        foreach (AreaFactory::GAZA_AREAS as $cityName => $expectedAreas) {
            $city = City::where('name', $cityName)->firstOrFail();
            $this->assertEqualsCanonicalizing($expectedAreas, $city->areas()->pluck('name')->all());
        }
    }

    public function test_khan_younis_rafah_and_middle_areas_are_separated(): void
    {
        $this->seedGazaLocations();

        $khanYounis = City::where('name', 'خانيونس')->firstOrFail();
        $rafah = City::where('name', 'رفح')->firstOrFail();
        $middle = City::where('name', 'الوسطى')->firstOrFail();

        $this->assertEqualsCanonicalizing(AreaFactory::GAZA_AREAS['خانيونس'], $khanYounis->areas()->pluck('name')->all());
        $this->assertEqualsCanonicalizing(AreaFactory::GAZA_AREAS['الوسطى'], $middle->areas()->pluck('name')->all());
        $this->assertEmpty($khanYounis->areas()->whereIn('name', AreaFactory::GAZA_AREAS['رفح'])->pluck('name')->all());
        $this->assertNotEmpty($rafah->areas);
    }

    public function test_repeated_seeding_restores_matches_without_duplicates_or_deleting_old_records(): void
    {
        $legacy = City::create(['name' => 'رام الله']);
        $this->seedGazaLocations();
        $deletedArea = Area::where('name', 'خانيونس البلد')->firstOrFail();
        $deletedArea->delete();
        City::where('name', 'رفح')->firstOrFail()->delete();

        $this->seedGazaLocations();

        $this->assertSame(6, City::count());
        $this->assertSame(48, Area::count());
        $this->assertTrue(City::whereKey($legacy->id)->exists());
        $this->assertTrue(Area::whereKey($deletedArea->id)->exists());
        $this->assertSame(1, City::where('name', 'خانيونس')->count());
        $this->assertSame(1, Area::where('name', 'خانيونس البلد')->count());
    }

    public function test_location_factories_and_order_factory_generate_only_matching_gaza_locations(): void
    {
        foreach (range(1, 20) as $iteration) {
            $order = Order::factory()->create();
            $this->assertContains($order->city->name, CityFactory::GAZA_LOCATIONS);
            $this->assertContains($order->area->name, AreaFactory::GAZA_AREAS[$order->city->name]);
            $this->assertSame($order->city_id, $order->area->city_id);
        }
    }

    private function seedGazaLocations(): void
    {
        $this->seed(CitySeeder::class);
        $this->seed(AreaSeeder::class);
    }
}
