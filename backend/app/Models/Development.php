<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Development extends Model
{
    use SoftDeletes, UUID;

    protected $fillable = [
        'dev_code',
        'image',
        'name',
        'description',
        'person_in_charge',
        'start_date',
        'end_date',
        'amount',
        'is_completed',
    ];

    public function developmentApplicant()
    {
        return $this->hasMany(DevelopmentApplicant::class, 'dev_code', 'dev_code');
    }
}
