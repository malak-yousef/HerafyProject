<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $resources = [
            'admins', 'customers', 'craftmen', 'categories', 'professions',
            'portfolios', 'portfolio-images', 'areas', 'cities', 'orders',
            'reviews', 'order-images', 'offers', 'service-jobs', 'reports',
        ];
        $permissionNames = ['dashboard.view'];

        foreach ($resources as $resource) {
            foreach (['view', 'create', 'update', 'delete'] as $action) {
                $permissionNames[] = "{$resource}.{$action}";
            }
        }

        array_push(
            $permissionNames,
            'verifications.view',
            'verifications.update',
            'roles.manage',
            'permissions.manage',
            'settings.manage',
            'verifications.delete',
        );

        $permissions = collect($permissionNames)->map(fn (string $name) => Permission::firstOrCreate([
            'name' => $name,
            'guard_name' => 'admin',
        ]));

        $role = Role::firstOrCreate([
            'name' => 'super-admin',
            'guard_name' => 'admin',
        ]);

        $admin = User::updateOrCreate(['email' => 'admin1@example.com'], [
            'name' => 'Super Admin',
            'user_name' => 'admin',
            'email' => 'admin1@example.com',
            'password' => Hash::make('123123'),
            'mobile' => '0599999999',
            'gender' => 'male',
            'status' => 'active',
            'address' => 'Gaza',
            'date' => now(),
            'actor_type' => null,
            'actor_id' => null,
        ]);

        $admin->roles()->syncWithoutDetaching([$role->id]);
        $role->syncPermissions($permissions);
    }
}
