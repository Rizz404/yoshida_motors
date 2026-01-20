<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppraisalPhoto extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Relasi balik ke Request
    public function appraisalRequest()
    {
        return $this->belongsTo(AppraisalRequest::class);
    }
}
