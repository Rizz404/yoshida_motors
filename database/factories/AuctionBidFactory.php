<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AuctionBid>
 */
class AuctionBidFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'auction_id' => \App\Models\Auction::factory(),
            'dealer_id' => \App\Models\User::factory()->state(['role' => 'dealer']),
            'amount' => fake()->numberBetween(160, 500) * 10000,
        ];
    }
}
