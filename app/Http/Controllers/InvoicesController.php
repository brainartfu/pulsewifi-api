<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use App\Models\OrderProducts;
use App\Models\Invoices;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Razorpay\Api\Api;
use Validator;

class InvoicesController extends Controller
{
    public function __construct()
    {
        
    }

    public function get_all_invoices()
    {
      $invoices = Invoices::with(['user', 'order', 'order.pdoa', 'products'])->get();

        return response()->json([
            'success' => true,
            'message' => 'Invoices successfully fetched',
            'data' => $invoices,
        ]);
    }

    

}
