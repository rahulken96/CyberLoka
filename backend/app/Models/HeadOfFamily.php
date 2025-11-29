<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HeadOfFamily extends Model
{
    use SoftDeletes, UUID;

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
}
