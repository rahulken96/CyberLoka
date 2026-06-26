<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class FamilyMember extends Model
{
    use SoftDeletes;

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->family_member_code)) {
                $model->family_member_code = (string) Str::uuid()->toString();
            }
        });
    }

    protected $fillable = [
        'family_member_code',
        'family_code',
        'user_code',
        'date_of_birth',
        'image',
        'occupation',
        'nik',
        'gender',
        'martial_status',
        'relation',
        'role',
    ];

    public function headOfFamily()
    {
        return $this->belongsTo(HeadOfFamily::class, 'family_code', 'family_code');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_code', 'user_code');
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
        ->orWhere('family_member_code', 'like', "$search")
        ->orWhere('family_code', 'like', "$search")
        ->orWhere('occupation', 'like', "$search")
        ->orWhere('nik', 'like', "$search")
        ->orWhere('gender', 'like', "$search")
        ->orWhere('martial_status', 'like', "$search")
        ->orWhere('relation', 'like', "$search")
        ->orWhere('role', 'like', "$search")
        ;
    }

    //================== CUSTOM FUNCTION ==================
    public static function  findByCode(string $search)
    {
        return self::where('family_member_code', $search)->first() 
            ?? self::where('family_code', $search)->first() 
            ?? self::where('user_code', $search)->first();
    }
}
