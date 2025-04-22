<?php

namespace Database\Factories;

use App\Models\User;
use App\Enum\AvailabilityStatus;
use App\Enum\UserType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Facility>
 */
class FacilityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $admin = UserType::Admin->value;
        $users = collect(User::Where('usertype',$admin)->pluck('id'));
        return [
            'name' => fake()->unique()->company(),
            'description' => fake()->sentence(20),
            'capacity' =>  $this->faker->numberBetween(10,100),
            'status' => AvailabilityStatus::Free->value,
            'specialnote'=> fake()->sentence(20),
            'price' =>  $this->faker->numberBetween(100.99,1000),
            'user_id' =>  $users->random(),
        ];
    }
}