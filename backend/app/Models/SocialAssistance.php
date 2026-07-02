<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SocialAssistance extends Model
{
    use SoftDeletes;

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->social_code)) {
                $model->social_code = (string) Str::uuid()->toString();
            }
        });
    }

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

    protected $casts = [
        'is_available'  => 'boolean',
    ];

    public function socialRecipient()
    {
        return $this->hasMany(SocialAssistanceRecipient::class, 'social_code', 'social_code');
    }

    //================== SCOPE ==================
    public function scopeSearch($q, $search)
    {
        $search = "%{$search}%";
        return $q->where('name', 'like', $search)
            ->orWhere('category', 'like', $search)
            ->orWhereRaw('CAST(amount AS CHAR) LIKE ?', [$search])
            ->orWhere('provider', 'like', $search)
        ;
    }
}
