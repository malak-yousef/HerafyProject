<?php

namespace Database\Factories;

use App\Models\Craftman;
use App\Models\Customer;
use App\Models\Profession;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

/** @extends Factory<User> */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        $names = ['أحمد محمد', 'محمد خالد', 'سارة محمود', 'ليان يوسف', 'عمر سمير', 'نور علي'];

        return [
            'name' => fake()->randomElement($names),
            'user_name' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => null,
            'mobile' => fake()->unique()->numerify('059#######'),
            'gender' => fake()->randomElement(['male', 'female']),
            'status' => 'active',
            'image' => null,
            'address' => fake()->randomElement(['غزة', 'رام الله', 'الخليل', 'نابلس', 'جنين']),
            'date' => fake()->dateTimeBetween('-55 years', '-20 years')->format('Y-m-d'),
            'actor_type' => null,
            'actor_id' => null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => ['status' => 'active']);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['status' => 'inactive']);
    }

    public function blocked(): static
    {
        return $this->state(fn () => ['status' => 'blocked']);
    }

    public function admin(string $roleName = 'cms-admin'): static
    {
        return $this->state(fn () => ['actor_type' => null, 'actor_id' => null])
            ->afterCreating(function (User $user) use ($roleName): void {
                $user->assignRole(Role::findOrCreate($roleName, 'admin'));
            });
    }

    public function customer(): static
    {
        return $this->afterCreating(function (User $user): void {
            $customer = Customer::query()->create([
                'id_number' => fake()->unique()->numerify('##########'),
            ]);
            $user->forceFill([
                'actor_type' => Customer::class,
                'actor_id' => $customer->id,
            ])->save();
        });
    }

    public function craftman(?Profession $profession = null): static
    {
        return $this->afterCreating(function (User $user) use ($profession): void {
            $craftman = Craftman::query()->create([
                'profession_id' => $profession?->id ?? Profession::factory()->create()->id,
                'id_number' => fake()->unique()->numerify('##########'),
                'experience_years' => fake()->numberBetween(1, 25),
                'description' => 'حرفي متخصص وملتزم بمواعيد العمل وجودة التنفيذ.',
                'rating' => 0,
                'total_reviews' => 0,
                'hourly_rate' => fake()->numberBetween(30, 150),
                'daily_rate' => fake()->numberBetween(150, 600),
            ]);
            $user->forceFill([
                'actor_type' => Craftman::class,
                'actor_id' => $craftman->id,
            ])->save();
        });
    }

    public function unverified(): static
    {
        return $this->state(fn () => ['email_verified_at' => null]);
    }
}
