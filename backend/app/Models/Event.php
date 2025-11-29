<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes, UUID;

    protected $fillable = [
        'event_code',
        'image',
        'name',
        'description',
        'price',
        'date_event',
        'is_active',
    ];

    public function eventParticipants()
    {
        return $this->hasMany(EventParticipant::class, 'event_code', 'event_code');
    }
}
