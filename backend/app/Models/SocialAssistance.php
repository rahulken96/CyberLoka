<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialAssistance extends Model
{
    use SoftDeletes, UUID;

    protected $fillable = [
        'social_code',
        'image',
        'name',
        'category',
        'amount',
        'provider',
        'description',
        'is_available',
    ];

    public function socialRecipient()
    {
        return $this->hasMany(SocialAssistanceRecipient::class, 'social_code', 'social_code');
    }
}
