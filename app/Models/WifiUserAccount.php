<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class WifiUserAccount extends Authenticatable implements JWTSubject
{
    use HasFactory;
    protected $fillable = [
        'id',
        'phone',
        'first_name',
        'last_name',
        'email',
        'password',
        'account_verified',
        'otp_code',
        'email_verified',
        'email_verify_code',
        'state',
        'district',
        'pin_code',
        'pdoa_id'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
