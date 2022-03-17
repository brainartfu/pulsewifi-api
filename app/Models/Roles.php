<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'name',
        'display_name',
        'Wifi_Users',
        'Wifi_Router',
        'Location',
        'Distributor',
        'Franchise',
        'Internet_Plan_Setting',
        'Internet_Plan_View',
        'Payout_Setting',
        'Payout_Log',
        'Payment_Setting',
        'Payment_Log',
        'Payout_Log_Process',
        'Leads',
        'Add_Leads',
        'SMS_Gateway',
        'SMS_Template',
        'Mail_Server',
        'Email_Template',
        'Network_Setting',
        'role_management',
        'Add_PDOA',
        'PDOA_Management',
        'PDOA_Plan',
        'Staff_Management',
        'WiFi_Router_Models',
        'Products',
        'Product_Management',
        'Process_Product',
        'Cart',
        'User_IP_Logs',
        'SMS_Logs',
        'Email_Logs',
        'required',
    ];
}