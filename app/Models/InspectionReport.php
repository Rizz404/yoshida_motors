<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionReport extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
        ];
    }

    public function appraisalRequest()
    {
        return $this->belongsTo(AppraisalRequest::class);
    }

    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    public function items()
    {
        return $this->hasMany(InspectionReportItem::class);
    }

    public function photos()
    {
        return $this->hasMany(InspectionPhoto::class);
    }
}
