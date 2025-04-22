<?php

namespace Database\Factories;

use App\Models\Facility;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {      
    
     $users = collect(User::all()->modelKeys());
     $facility = collect(Facility::Where('status','free')->pluck('id'));
        return [
            'user_id' => $users->random(),
            'facility_id' => $facility->random(),
            'check_in' =>  now()->addDays(2),
            'check_out' => now()->addDays(3),  
            'attendants' =>  $this->faker->numberBetween(10,100),
            'purpose' => 'meeting'
        ];
    }
}

