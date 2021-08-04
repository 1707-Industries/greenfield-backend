<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class ThirdPartyAuthConnection extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected static $logAttributes = ['*'];

    protected $guarded = [];

    protected $casts = [
        'provider_user' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
