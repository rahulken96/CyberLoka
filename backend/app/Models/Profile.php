<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    use SoftDeletes, UUID;

    protected $fillable = [
        'profile_code',
        'user_code',
        'image',
        'name',
        'about',
        'headman',
        'people',
        'agricultural_area',
        'total_area',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_code', 'user_code');
    }

    public function images()
    {
        return $this->hasMany(ProfileImage::class, 'profile_code', 'profile_code');
    }
}
