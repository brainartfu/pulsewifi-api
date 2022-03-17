<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Users extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use HasFactory, Notifiable;

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

// namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
    // use Illuminate\Database\Eloquent\Factories\HasFactory;
    // use Illuminate\Foundation\Auth\User as Authenticatable;
    // use Illuminate\Notifications\Notifiable;
    // use Laravel\Sanctum\HasApiTokens;

// class Users extends Authenticatable
    // {
    // use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    public $incrementing = false;
    protected $fillable = [
        'id',
        'role',
        'pdoa_id',
        'username',
        'firstname',
        'lastname',
        'email',
        'email_verified',
        'email_verification_code',
        'profile_img_path',
        'password',
        'enabled',
        'belongs_to',
        'lead_process',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'latitude',
        'longitude',
        'phone_no',
        'company_name',
        'designation',
        'id_proof',
        'id_proof_no',
        'upload_id_proof',
        'identity_verification',
        'gst_no',
        'revenue_model',
        'revenue_sharing_ratio',
        'beneficiary_name',
        'ifsc_code',
        'ac_no',
        'passbook_cheque',
        'payment_status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}