<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    //================== SCOPE ==================
    public function scopeSearch($q, $search)
    {
        return $q->where('user_code', 'like', "%{$search}%")
            ->orWhere('name', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->orWhere('phone', 'like', "%{$search}%")
        ;
    }

    //================== RELATION ==================
    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_code', 'user_code');
    }

    public function headOfFamily()
    {
        return $this->hasOne(HeadOfFamily::class, 'user_code', 'user_code');
    }

    public function familyMember()
    {
        return $this->hasOne(FamilyMember::class, 'user_code', 'user_code');
    }

    public function developmentApplicant()
    {
        return $this->hasMany(DevelopmentApplicant::class, 'user_code', 'user_code');
    }
}
