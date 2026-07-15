<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Event extends Model
{
    use SoftDeletes;

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->event_code)) {
                $model->event_code = (string) Str::uuid()->toString();
            }
        });
    }

    protected $fillable = [
        'event_code',
        'image',
        'name',
        'description',
        'price',
        'date_event',
        'is_active',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
    ];

    public function eventParticipants()
    {
        return $this->hasMany(EventParticipant::class, 'event_code', 'event_code');
    }

    //================== SCOPE ==================
    public function scopeSearch($q, $search)
    {
        $search = "%{$search}%";
        return $q->where('event_code', 'like', $search)
            ->orWhere('name', 'like', $search)
            ->orWhereRaw('CAST(description AS CHAR) LIKE ?', [$search])
            ->orWhereRaw('CAST(price AS CHAR) LIKE ?', [$search])
            ->orWhereRaw('CAST(date_event AS CHAR) LIKE ?', [$search]);
    }

    public function scopeFilter($q, array $filters)
    {
        $q->when($filters['search'] ?? null, function ($q, $search) {
            $q->search($search);
        });

        $q->when(isset($filters['is_active']), function ($q) use ($filters) {
            $q->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN));
        });
    }
}
