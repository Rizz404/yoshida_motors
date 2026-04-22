<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionReportItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function inspectionReport()
    {
        return $this->belongsTo(InspectionReport::class);
    }
}
