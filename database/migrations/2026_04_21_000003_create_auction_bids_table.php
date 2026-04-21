<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auction_bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auction_id')->constrained('auctions')->onDelete('cascade');
            $table->foreignId('dealer_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->timestamps();

            // One bid per dealer per auction
            $table->unique(['auction_id', 'dealer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auction_bids');
    }
};
