<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PDOA extends Model
{
    use HasFactory;
    public $table = "pdoas";
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'brand_name',
        'favicon',
        'brand_logo',
        'platform_bg',
        'pdoa_status',
        'distributor_fee',
        'franchise_fee',
        'pdoa_plan_id',
        'domain_name',
        'username',
        'firstname',
        'lastname',
        'email',
        'phone_no',
        'cin_no',
        'incorporation_cert',
        'company_name',
        'designation',
        'id_proof',
        'id_proof_no',
        'upload_id_proof',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'gst_no',
        'payment_status',
    ];
}