<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DevelopmentApplicant extends Model
{
    use SoftDeletes, UUID;

    protected $fillable = [
        'dev_app_code',
        'dev_code',
        'user_code',
        'status',
    ];

    public function development()
    {
        return $this->belongsTo(Development::class, 'dev_code', 'dev_code');
    }
}
