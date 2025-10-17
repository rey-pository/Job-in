<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationExperience extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'organization_name',
        'role',
        'start_year',
        'end_year',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
