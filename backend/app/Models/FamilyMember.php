<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FamilyMember extends Model
{
    use SoftDeletes, UUID;

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
    ];

    public function headOfFamily()
    {
        return $this->belongsTo(HeadOfFamily::class, 'family_code', 'family_code');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_code', 'user_code');
    }
}
