<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuctionBid extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'amount' => 'float',
        ];
    }

    public function auction()
    {
        return $this->belongsTo(Auction::class);
    }

    public function dealer()
    {
        return $this->belongsTo(User::class, 'dealer_id');
    }
}
