<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialAssistanceRecipient extends Model
{
    use SoftDeletes, UUID;

    protected $fillable = [
        'social_recipient_code',
        'social_code',
        'family_code',
        'bank',
        'account_bank',
        'amount',
        'image',
        'reason',
        'status',
    ];

    public function socialAssistance()
    {
        return $this->belongsTo(SocialAssistance::class, 'social_code', 'social_code');
    }

    public function headOfFamily()
    {
        return $this->belongsTo(HeadOfFamily::class, 'family_code', 'family_code');
    }
}
