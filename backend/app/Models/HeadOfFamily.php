<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class HeadOfFamily extends Model
{
    use SoftDeletes;

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->family_code)) {
                $model->family_code = (string) Str::uuid()->toString();
            }
        });
    }

    protected $fillable = [
        'family_code',
        'user_code',
        'date_of_birth',
        'image',
        'occupation',
        'nik',
        'gender',
        'martial_status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_code', 'user_code');
    }

    public function familyMembers()
    {
        return $this->hasMany(FamilyMember::class, 'family_code', 'family_code');
    }

    public function socialRecepients()
    {
        return $this->hasMany(SocialAssistanceRecipient::class, 'family_code', 'family_code');
    }

    public function eventParticipants()
    {
        return $this->hasMany(EventParticipant::class, 'family_code', 'family_code');
    }

    //================== SCOPE ==================
    public function scopeSearch($q, $search)
    {
        $search = "%{$search}%";
        return $q->whereHas('user', function ($qq) use ($search) {
            $qq->where('user_code', 'like', "$search")
                ->orWhere('name', 'like', "$search")
                ->orWhere('email', 'like', "$search")
                ->orWhere('phone', 'like', "$search")
            ;
        })
        ->orWhere('family_code', 'like', "$search")
        ->orWhere('occupation', 'like', "$search")
        ->orWhere('nik', 'like', "$search")
        ->orWhere('gender', 'like', "$search")
        ->orWhere('martial_status', 'like', "$search")
        ;
    }
}
