<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppraisalRequest extends Model
{
    use HasFactory;

    // Biar gak ribet ngetik fillable satu-satu
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'final_price' => 'float', // Biar jadi angka, bukan string "100000.00"
            'price_valid_until' => 'datetime', // Biar jadi object Carbon
            'year_manufacture' => 'integer',
        ];
    }

    // Relasi ke User (Request ini milik satu User)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Photos (Request ini punya banyak Foto)
    public function photos()
    {
        return $this->hasMany(AppraisalPhoto::class);
    }

    // * Anggap aja enum
    const STATUS_DRAFT = 'draft';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_REVIEW = 'under_review';
    const STATUS_COMPLETED = 'completed';
    const STATUS_REJECTED = 'rejected';
}
