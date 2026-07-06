<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SocialAssistanceRecipient extends Model
{
    use SoftDeletes;

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->social_recipient_code)) {
                $model->social_recipient_code = (string) Str::uuid()->toString();
            }
        });
    }

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

    //================== SCOPE ==================
    public function scopeSearch($q, $search)
    {
        $search = "%{$search}%";
        return $q->where('social_recipient_code', 'like', $search)
            ->orWhereHas('socialAssistance', function ($q) use ($search) {
                $q->where('social_code', 'like', $search)
                    ->orWhere('name', 'like', $search)
                    ->orWhere('category', 'like', $search)
                    ->orWhereRaw('CAST(amount AS CHAR) LIKE ?', [$search])
                    ->orWhere('provider', 'like', $search);
            })
            ->orWhereHas('headOfFamily', function ($q) use ($search) {
                $q->where('family_code', 'like', $search)
                    ->orWhere('occupation', 'like', "$search")
                    ->orWhere('nik', 'like', "$search");
            })
            ->orWhere('bank', 'like', $search)
            ->orWhere('account_bank', 'like', $search)
            ->orWhereRaw('CAST(amount AS CHAR) LIKE ?', [$search])
            ->orWhere('reason', 'like', $search)
            ->orWhere('status', 'like', $search)
        ;
    }
}
