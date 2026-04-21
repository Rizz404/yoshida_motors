<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auction extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'start_time'    => 'datetime',
            'end_time'      => 'datetime',
            'reserve_price' => 'float',
        ];
    }

    public function appraisalRequest()
    {
        return $this->belongsTo(AppraisalRequest::class);
    }

    public function bids()
    {
        return $this->hasMany(AuctionBid::class);
    }

    public function winner()
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    public function winningBid()
    {
        return $this->belongsTo(AuctionBid::class, 'winning_bid_id');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open' && now()->lessThan($this->end_time);
    }

    public function isExpired(): bool
    {
        return $this->status === 'open' && now()->greaterThanOrEqualTo($this->end_time);
    }

    const STATUS_OPEN    = 'open';
    const STATUS_CLOSED  = 'closed';
    const STATUS_AWARDED = 'awarded';
}
