<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstName = fake()->firstName();
        $middleName = fake()->firstName();
        $lastName = fake()->lastName();
        
        return [
            'name' => $firstName . ' ' . $middleName . ' ' . $lastName,
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'user_type' => fake()->numberBetween(1, 3), // 1=Student, 2=Staff, 3=Doctor
            'f_name' => $firstName,
            'm_name' => $middleName,
            'l_name' => $lastName,
            'dob' => fake()->date('Y-m-d', '2000-01-01'),
            'address' => fake()->address(),
            'gender' => fake()->randomElement(['Male', 'Female']),
            'contact_no' => fake()->phoneNumber(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
