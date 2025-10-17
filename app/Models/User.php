<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Company;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'role_id',
        'name',
        'phone_number',
        'email',
        'password',
        'avatar',
        'status_verifikasi',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'status_verifikasi' => 'string',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function company()
    {
        return $this->hasOne(Company::class);
    }

    public function educationHistories()
    {
        return $this->hasMany(EducationHistory::class);
    }

    public function workExperiences()
    {
        return $this->hasMany(WorkExperience::class);
    }

    public function organizationExperiences()
    {
        return $this->hasMany(OrganizationExperience::class);
    }

    public function portfolioHistories()
    {
        return $this->hasMany(PortfolioHistory::class);
    }

    public function applications()
    {
        return $this->hasMany(\App\Models\Application::class);
    }

    public function isApproved(): bool
    {
        return $this->status_verifikasi === 'approved';
    }

    protected static function booted()
    {
        static::created(function ($user) {
            if ($user->role_id === 2) {
                if (!Company::where('user_id', $user->id)->exists()) {
                    Company::create([
                        'user_id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone_number' => $user->phone_number,
                    ]);
                }
            }
        });
    }
}
