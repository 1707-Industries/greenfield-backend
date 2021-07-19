<?php

namespace App;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes, LogsActivity;

    /**
     * @var string[]
     */
    protected static $logAttributes = ['*'];

    /**
     * @var string[]
     */
    protected $guarded = [];


    /**
     * @var string[]
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var string[]
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @return string
     */
    public function getNameAttribute()
    {
        return trim(sprintf('%s %s (%s)', $this->first_name, $this->surname, $this->email));
    }

    /**
     * @param $password
     * @property string password
     */
    public function setPasswordAttribute($password) :void
    {
        $this->attributes['password'] = Hash::make($password);
    }

    /**
     * @param string $token
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new Notifications\VerifyEmail);
    }

    public function getAvatarUrlAttribute()
    {
        return sprintf(
            'https://www.gravatar.com/avatar/%s?rating=x&d=identicon',
            md5(trim(strtolower($this->email)))
        );
    }

    public function activityLog()
    {
        return $this->morphMany(Activity::class, 'causer');
    }
}
