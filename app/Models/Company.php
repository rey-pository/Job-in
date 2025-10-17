<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'phone_number',
        'email',
        'logo',
        'website',
        'address',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
       //sync
        static::saving(function ($company) {
            if ($company->user) {
                $company->name = $company->user->name;
                $company->email = $company->user->email;
                $company->phone_number = $company->user->phone_number;
            }
        });
        
    }
    public function jobs()
    {
        return $this->hasMany(Job::class);
    }
}
