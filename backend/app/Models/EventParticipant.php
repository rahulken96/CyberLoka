<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventParticipant extends Model
{
    use SoftDeletes, UUID;

    protected $fillable = [
        'event_participant_code',
        'event_code',
        'family_code',
        'qty',
        'total_price',
        'payment_status',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_code', 'event_code');
    }

    public function headOfFamily()
    {
        return $this->belongsTo(HeadOfFamily::class, 'family_code', 'family_code');
    }
}
