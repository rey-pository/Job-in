<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EducationHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'institution',
        'degree',
        'start_year',
        'end_year',
        'gpa',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
