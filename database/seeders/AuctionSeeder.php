<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AuctionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dealers = \App\Models\User::where('role', 'dealer')->get();
        if ($dealers->isEmpty()) {
            $dealers = \App\Models\User::factory(5)->create(['role' => 'dealer']);
        }

        $appraisals = \App\Models\AppraisalRequest::whereIn('status', ['in_auction', 'acquired'])->get();
        if ($appraisals->isEmpty()) {
            return;
        }

        foreach ($appraisals as $appraisal) {
            $auction = \App\Models\Auction::factory()->create([
                'appraisal_request_id' => $appraisal->id,
            ]);

            // Generate some bids
            $bidders = $dealers->random(min(3, $dealers->count()));
            foreach ($bidders as $dealer) {
                \App\Models\AuctionBid::factory()->create([
                    'auction_id' => $auction->id,
                    'dealer_id' => $dealer->id,
                ]);
            }
        }
    }
}
