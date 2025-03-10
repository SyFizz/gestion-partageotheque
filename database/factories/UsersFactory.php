<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class UsersFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    //Create a factory for users. The factory will create a user and then assign a random role to the user.
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'is_validated' => $this->faker->boolean,
            'password' => Hash::make('password'),
        ];
    }

    //Assign a random role to the user
    public function configure()
    {
        return $this->afterCreating(function (\App\Models\User $user) {
            $user->roles()->attach(\App\Models\Role::all()->random());
        });
    }
}
