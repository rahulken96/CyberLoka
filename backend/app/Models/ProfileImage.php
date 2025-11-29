<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProfileImage extends Model
{
    use SoftDeletes, UUID;

    protected $fillable = [
        'profile_image_code',
        'profile_code',
        'image',
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class, 'profile_code', 'profile_code');
    }
}
