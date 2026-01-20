<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppraisalRequest extends Model
{
    use HasFactory;

    // Biar gak ribet ngetik fillable satu-satu
    protected $guarded = ['id'];

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
}
