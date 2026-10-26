<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Craftman;
use App\Models\Customer;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Profession;
use App\Models\Report;
use App\Models\Review;
use App\Models\ServiceJob;
use App\Models\User;
use App\Models\Verification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class HerafyDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            ProfessionSeeder::class,
            CitySeeder::class,
            AreaSeeder::class,
            AdminSeeder::class,
            SettingSeeder::class,
        ]);

        $this->seedOrdinaryAdmin();

        $customers = collect(range(0, 19))->map(function (int $index) {
            $factory = Customer::factory();
            $factory = match (true) {
                $index >= 18 => $factory->blocked(),
                $index >= 16 => $factory->inactive(),
                default => $factory->active(),
            };

            return $factory->create();
        });

        $professions = Profession::where('status', 'active')->orderBy('id')->get();
        $craftmen = collect(range(0, 23))->map(function (int $index) use ($professions) {
            $factory = Craftman::factory()->state([
                'profession_id' => $professions[$index % $professions->count()]->id,
            ]);
            $factory = match (true) {
                $index >= 22 => $factory->blocked(),
                $index >= 20 => $factory->inactive(),
                default => $factory->active(),
            };

            return $factory->create();
        });

        foreach ($craftmen as $index => $craftman) {
            $state = ['pending', 'approved', 'rejected'][$index % 3];
            Verification::factory()->{$state}()->create(['craftman_id' => $craftman->id]);
        }

        $jobs = collect();
        $jobStates = [
            ...array_fill(0, 3, 'pending'),
            ...array_fill(0, 3, 'inProgress'),
            ...array_fill(0, 10, 'completed'),
            ...array_fill(0, 4, 'cancelled'),
        ];

        foreach ($jobStates as $index => $state) {
            $craftman = $craftmen[$index % $craftmen->count()];
            $area = Area::whereHas('city')->inRandomOrder()->firstOrFail();
            $order = Order::factory()->create([
                'customer_id' => $customers[$index % $customers->count()]->id,
                'profession_id' => $craftman->profession_id,
                'city_id' => $area->city_id,
                'area_id' => $area->id,
            ]);
            $offer = Offer::factory()->create([
                'order_id' => $order->id,
                'craftman_id' => $craftman->id,
            ]);
            $jobs->push(ServiceJob::factory()->{$state}()->create([
                'order_id' => $order->id,
                'offer_id' => $offer->id,
                'customer_id' => $order->customer_id,
                'craftman_id' => $craftman->id,
            ]));
        }

        $standaloneStatuses = ['pending', 'accepted', 'in_progress', 'completed', 'cancelled'];
        foreach (range(0, 29) as $index) {
            $profession = $professions[$index % $professions->count()];
            $area = Area::inRandomOrder()->firstOrFail();
            $status = $standaloneStatuses[$index % count($standaloneStatuses)];
            $order = Order::factory()->create([
                'customer_id' => $customers[$index % $customers->count()]->id,
                'profession_id' => $profession->id,
                'city_id' => $area->city_id,
                'area_id' => $area->id,
                'status' => $status,
            ]);
            $matchingCraftmen = $craftmen->where('profession_id', $profession->id)->values();
            foreach ($matchingCraftmen->take($index < 20 ? 2 : 1) as $craftman) {
                Offer::factory()->create([
                    'order_id' => $order->id,
                    'craftman_id' => $craftman->id,
                    'status' => $status,
                ]);
            }
        }

        foreach ($jobs->where('status', 'completed')->values() as $index => $job) {
            $state = ['approved', 'pending', 'rejected'][$index % 3];
            Review::factory()->{$state}()->create([
                'request_id' => $job->order_id,
                'customer_id' => $job->customer_id,
                'craftsman_id' => $job->craftman_id,
            ]);
        }

        foreach (range(0, 11) as $index) {
            $job = $jobs[$index % $jobs->count()];
            $state = ['pending', 'reviewing', 'resolved', 'rejected'][$index % 4];
            Report::factory()->{$state}()->create(['job_id' => $job->id]);
        }
    }

    private function seedOrdinaryAdmin(): void
    {
        $role = Role::firstOrCreate(['name' => 'cms-admin', 'guard_name' => 'admin']);
        $permissions = Permission::where('guard_name', 'admin')
            ->where(fn ($query) => $query->where('name', 'dashboard.view')->orWhere('name', 'like', '%.view'))
            ->get();
        $role->syncPermissions($permissions);

        $admin = User::updateOrCreate(['email' => 'admin2@example.com'], [
            'name' => 'مسؤول المحتوى',
            'user_name' => 'cms_admin',
            'password' => Hash::make(env('HERAFY_CMS_ADMIN_PASSWORD', '12312378')),
            'mobile' => '0599999998',
            'gender' => 'male',
            'status' => 'active',
            'address' => 'رام الله',
            'date' => '1990-01-01',
            'actor_type' => null,
            'actor_id' => null,
        ]);
        $admin->syncRoles([$role]);
    }
}
