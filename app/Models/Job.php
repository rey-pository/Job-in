<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'title',
        'department',
        'description',
        'province',
        'city',
        'work_type',
        'published_date',
        'expired_date',
        'status',
    ];

    protected $casts = [
        'published_date' => 'date',
        'expired_date' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class)
                     ->select('id', 'name', 'logo', 'address');
    }

    public function getPublishedDateAttribute($value)
    {
        return $value ? date('Y-m-d', strtotime($value)) : null;
    }

    public function getExpiredDateAttribute($value)
    {
        return $value ? date('Y-m-d', strtotime($value)) : null;
    }
}