<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Auction>
 */
class AuctionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['open', 'closed', 'awarded']);
        return [
            'appraisal_request_id' => \App\Models\AppraisalRequest::factory(),
            'start_time' => fake()->dateTimeBetween('-7 days', 'now'),
            'end_time' => fake()->dateTimeBetween('now', '+7 days'),
            'reserve_price' => fake()->numberBetween(150, 300) * 10000,
            'status' => $status,
            'winner_id' => null,
            'winning_bid_id' => null,
        ];
    }
}
