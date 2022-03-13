<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\InternetPlansController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MailServerController;
use App\Http\Controllers\NetworkSettingController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\InvoicesController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\PaymentSettingController;
use App\Http\Controllers\PayoutsController;
use App\Http\Controllers\PayoutSettingController;
use App\Http\Controllers\PDOAController;
use App\Http\Controllers\PdoaPlanController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\SmsGatewayController;
use App\Http\Controllers\SmsTemplateController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\WifiRouterController;
use App\Http\Controllers\WifiRouterModelController;
use App\Http\Controllers\WiFiUsersController;
use App\Http\Controllers\WiFiOrderController;
use App\Http\Controllers\UserIpLogsController;
use App\Http\Controllers\SmsLogsController;
use App\Http\Controllers\EmailLogsController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\InventoryController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['api']], function ($router) {
    Route::group(['prefix' => '/auth'], function () {
        Route::post('/register/{pdoa_id}', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::get('/logout', [AuthController::class, 'logout']);
        Route::get('/refresh', [AuthController::class, 'refresh']);
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::post('/updateuser/{user_id}', [AuthController::class, 'updateuser']);
    });

    Route::group(['prefix' => '/users'], function () {
        Route::post('/register/{pdoa_id}', [AuthController::class, 'register']);
        Route::get('/get_user_fees/{pdoa_id}', [UsersController::class, 'get_user_fees']);
        Route::post('/request_lead/{pdoa_id}', [UsersController::class, 'request_lead']);
        Route::post('/add_lead/{pdoa_id}', [UsersController::class, 'add_lead']);
        Route::post('/add_franchise/{pdoa_id}', [UsersController::class, 'add_franchise']);
        Route::post('/add_distributor/{pdoa_id}', [UsersController::class, 'add_distributor']);
        Route::get('/get_leads/{pdoa_id}', [UsersController::class, 'get_leads']);
        Route::get('/process_lead/{user_id}', [UsersController::class, 'process_lead']);
        Route::get('/delete/{user_id}', [UsersController::class, 'delete']);
        Route::get('/getStaffs/{pdoa_id}', [UsersController::class, 'getStaffs']);
        Route::get('/getDistributors/{pdoa_id}', [UsersController::class, 'getDistributors']);
        Route::get('/getPdoaDistributors/{pdoa_id}', [UsersController::class, 'getPdoaDistributors']);
        Route::get('/getFranchises/{distributor_id}/{pdoa_id}', [UsersController::class, 'getFranchises']);
        Route::get('/getAllFranchises/{distributor_id}/{pdoa_id}', [UsersController::class, 'getAllFranchises']);
        Route::post('/updateProfile/{user_id}', [AuthController::class, 'updateProfile']);
        Route::post('/updateuser/{user_id}', [AuthController::class, 'updateuser']);
        Route::post('/verifyEmail', [UsersController::class, 'verifyEmail']);
        Route::post('/forgotPassword', [UsersController::class, 'forgotPassword']);
        Route::post('/resetPassword', [UsersController::class, 'resetPassword']);
    });

    Route::group(['prefix' => '/internet_plans'], function () {
        Route::post('/add/{pdoa_id}', [InternetPlansController::class, 'add']);
        Route::get('/delete/{plan_id}', [InternetPlansController::class, 'delete']);
        Route::get('/get_plans/{pdoa_id}', [InternetPlansController::class, 'get_plans']);
        Route::post('/updatePlan/{plan_id}', [InternetPlansController::class, 'updatePlan']);
        Route::get('/plan_list/{pdoa_id}', [InternetPlansController::class, 'get_plan_list']);
        Route::get('/user_active_session/{pdoa_id}', [WiFiUsersController::class, 'user_active_session']);
        
    });


    Route::group(['prefix' => '/location'], function () {
        Route::post('/add/{pdoa_id}', [LocationController::class, 'add']);
        Route::get('/delete/{location_id}', [LocationController::class, 'delete']);
        Route::get('/get_locations/{franchise_id}/{pdoa_id}', [LocationController::class, 'get_locations']);
        Route::get('/get_all_locations', [LocationController::class, 'get_all_locations']);
        Route::post('/update/{location_id}', [LocationController::class, 'update']);
        Route::get('/get_best_locations/{pdoa_id}/{period}', [WiFiOrderController::class, 'get_best_locations']);
    });

    Route::group(['prefix' => '/subscriber'], function () {
        Route::get('/get_all_subscribers', [SubscriberController::class, 'get_all_subscribers']);
        Route::post('/add', [SubscriberController::class, 'add']);
        Route::post('/update/{subscriber_id}', [SubscriberController::class, 'update']);
    });

    Route::group(['prefix' => '/wifi_router'], function () {
        Route::get('/delete/{router_id}', [WifiRouterController::class, 'delete']);
        Route::get('/get_location_router/{location_id}', [WifiRouterController::class, 'get_location_router']);
        Route::get('/get_no_location_router/{user_id}', [WifiRouterController::class, 'get_no_location_router']);
        Route::get('/update_router_with_location/{location_id}/{router_id}', [WifiRouterController::class, 'update_router_with_location']);
        Route::get('/get_router/{user_id}/{pdoa_id}', [WifiRouterController::class, 'get_router']);
        Route::get('/get_router_status/{user_id}/{pdoa_id}', [WifiRouterController::class, 'get_router_status']);
        Route::post('/update/{router_id}', [WifiRouterController::class, 'update']);
        Route::get('/heartbeat/{pdoa_id}/{secret}', [WifiRouterController::class, 'heartbeat']);
        Route::get('/verify/{verification_code}/{mac}', [WifiRouterController::class, 'verify_router']);
        Route::get('/settings/{pdoa_id}/{secret}', [WifiRouterController::class, 'settings']);
        Route::get('/get_best_sell/{pdoa_id}/{period}', [WifiRouterController::class, 'get_best_sell']);
        Route::get('/get_year_top/{pdoa_id}', [WifiRouterController::class, 'get_year_top']);
    });

    Route::group(['prefix' => '/payment_setting'], function () {
        Route::post('/add/{pdoa_id}', [PaymentSettingController::class, 'add']);
        Route::get('/delete/{id}', [PaymentSettingController::class, 'delete']);
        Route::get('/get/{pdoa_id}', [PaymentSettingController::class, 'get']);
        Route::post('/update/{id}', [PaymentSettingController::class, 'update']);
    });

    Route::group(['prefix' => '/payments'], function () {
        Route::get('/get/{pdoa_id}', [PaymentsController::class, 'get']);
    });

    Route::group(['prefix' => '/payout_setting'], function () {
        Route::get('/get/{pdoa_id}', [PayoutSettingController::class, 'get']);
        Route::post('/update', [PayoutSettingController::class, 'update']);
    });

    Route::group(['prefix' => '/payouts'], function () {
        Route::get('/get/{pdoa_id}', [PayoutsController::class, 'get']);
        Route::get('/log_payout/{wifi_user_id}/{internet_plan_id}/{location_id}/{payment_method}', [PayoutsController::class, 'log_payout']);
        Route::get('/update_process/{pdoa_id}', [PayoutsController::class, 'update_process']);
    });

    Route::group(['prefix' => '/sms_gateway'], function () {
        Route::post('/add/{pdoa_id}', [SmsGatewayController::class, 'add']);
        Route::get('/delete/{id}', [SmsGatewayController::class, 'delete']);
        Route::get('/get/{pdoa_id}', [SmsGatewayController::class, 'get']);
        Route::post('/update/{id}', [SmsGatewayController::class, 'update']);
    });

    Route::group(['prefix' => '/sms_template'], function () {
        Route::post('/add/{pdoa_id}', [SmsTemplateController::class, 'add']);
        Route::get('/delete/{id}', [SmsTemplateController::class, 'delete']);
        Route::get('/get/{pdoa_id}', [SmsTemplateController::class, 'get']);
        Route::post('/update/{id}', [SmsTemplateController::class, 'update']);
    });

    Route::group(['prefix' => '/network_setting'], function () {
        Route::get('/get/{pdoa_id}', [NetworkSettingController::class, 'get']);
        Route::post('/update/{id}', [NetworkSettingController::class, 'update']);
    });

    Route::group(['prefix' => '/mail_server'], function () {
        Route::post('/add/{pdoa_id}', [MailServerController::class, 'add']);
        Route::get('/delete/{id}', [MailServerController::class, 'delete']);
        Route::get('/get/{pdoa_id}', [MailServerController::class, 'get']);
        Route::post('/update/{id}', [MailServerController::class, 'update']);
    });

    Route::group(['prefix' => '/email_template'], function () {
        Route::post('/add/{pdoa_id}', [EmailTemplateController::class, 'add']);
        Route::get('/delete/{template_id}', [EmailTemplateController::class, 'delete']);
        Route::get('/get/{pdoa_id}', [EmailTemplateController::class, 'get']);
        Route::get('/get_template/{template_id}', [EmailTemplateController::class, 'get_template']);
        Route::post('/update/{id}', [EmailTemplateController::class, 'update']);
    });

    Route::group(['prefix' => '/wifiusers'], function () {
        Route::post('/register/{pdoa_id}', [AuthController::class, 'wifiuser_register']);
        Route::post('/login', [AuthController::class, 'wifiuser_login']);
        Route::get('/logout', [AuthController::class, 'wifiuser_logout']);
        Route::get('/refresh', [AuthController::class, 'wifiuser_refresh']);
        Route::get('/profile/{user_id}', [AuthController::class, 'wifiuser_profile']);
        Route::get('/infp', [AuthController::class, 'wifiuser_info']);
        Route::get('/get_best_users/{pdoa_id}/{period}', [WiFiOrderController::class, 'get_best_users']);
    });

    Route::group(['prefix' => '/role'], function () {
        Route::post('/add', [RolesController::class, 'add']);
        Route::get('/get_role/{id}', [RolesController::class, 'get_role']);
        Route::get('/get', [RolesController::class, 'get']);
        Route::get('/get_staff_role', [RolesController::class, 'get_staff_role']);
        Route::get('/delete/{id}', [RolesController::class, 'delete']);
        Route::post('/update/{id}', [RolesController::class, 'update']);
    });

    Route::group(['prefix' => '/wifiRouterModel'], function () {
        Route::post('/add', [WifiRouterModelController::class, 'add']);
        Route::get('/get_routerModel/{id}', [WifiRouterModelController::class, 'get_routerModel']);
        Route::get('/get', [WifiRouterModelController::class, 'get']);
        Route::get('/delete/{id}', [WifiRouterModelController::class, 'delete']);
        Route::post('/update/{id}', [WifiRouterModelController::class, 'update']);
    });

    //device model
    Route::group(['prefix' => '/inventory'], function() {
        Route::post('/add_device', [InventoryController::class, 'create']);
        Route::post('/get_items', [InventoryController::class, 'getItems']);
        Route::post('/get_item_by_id', [InventoryController::class, 'get_item_by_id']);
        Route::post('/delete-item', [InventoryController::class, 'delete_item']);
        Route::post('/new-category', [InventoryController::class, 'new_category']);
        Route::post('/get_category', [InventoryController::class, 'get_category']);
        Route::post('/get_category_model', [InventoryController::class, 'get_category_model']);
        Route::post('/delete-category', [InventoryController::class, 'delete_category']);
        Route::post('/new-model', [InventoryController::class, 'new_model']);
    });

    Route::group(['prefix' => '/products'], function () {
        Route::get('/get_products/{pdoa_id}', [WifiRouterController::class, 'get_products']);
        Route::post('/add/{pdoa_id}', [WifiRouterController::class, 'add']);
        Route::post('/update_product/{id}', [WifiRouterController::class, 'update_product']);
        Route::get('/delete/{id}', [WifiRouterController::class, 'delete']);
        Route::get('/get_inventory/{pdoa_id}', [WifiRouterController::class, 'get_inventory']);
    });

    Route::group(['prefix' => '/cart'], function () {
        Route::get('/get_cart/{user_id}', [CartController::class, 'get_cart']);
        Route::get('/get_cart_amount/{user_id}', [CartController::class, 'get_cart_amount']);
        Route::get('/add_cart/{user_id}/{model_id}/{amount}', [CartController::class, 'add_cart']);
        Route::get('/cancel_cart/{cart_id}', [CartController::class, 'cancel_cart']);
        Route::get('/update_cart/{cart_id}/{amount}', [CartController::class, 'update_cart']);
    });

    Route::group(['prefix' => '/order'], function () {
        Route::post('/make_order', [OrdersController::class, 'make_order']);
        Route::get('/paid/{order_id}', [OrdersController::class, 'paid']);
        Route::get('/get_order/{order_id}', [OrdersController::class, 'get_order']);
        Route::get('/get_order/{order_id}/summary', [OrdersController::class, 'get_order_summary']);
        Route::get('/get_all_orders/{pdoa_id}', [OrdersController::class, 'get_all_orders']);
        Route::get('/get_new_orders/{pdoa_id}', [OrdersController::class, 'get_new_orders']);
        Route::get('/get_processed_orders/{pdoa_id}', [OrdersController::class, 'get_processed_orders']);
        Route::get('/get_incompleted_orders/{pdoa_id}', [OrdersController::class, 'get_incompleted_orders']);
        Route::get('/get_unpaid_orders/{pdoa_id}', [OrdersController::class, 'get_unpaid_orders']);
        Route::post('/process_order/{order_id}', [OrdersController::class, 'process_order']);
        Route::post('/get_order/{order_id}/initiate-payment', [OrdersController::class, 'initiatePayment']);
        Route::post('/payments/{razor_order_id}/status', [OrdersController::class, 'paymentStatus']);
    });

    Route::group(['prefix' => '/invoice'], function () {
        Route::get('/get_all_invoices', [InvoicesController::class, 'get_all_invoices']);
    });

    Route::group(['prefix' => '/pdoa'], function () {
        Route::get('/get', [PDOAController::class, 'get']);
        Route::get('/get_pdoa/{domain_name}', [PDOAController::class, 'get_pdoa']);
        Route::post('/add', [PDOAController::class, 'add']);
        Route::post('/update/{pdoa_id}', [PDOAController::class, 'update']);
        Route::get('/delete/{pdoa_id}', [PDOAController::class, 'delete']);
        Route::get('/get_wifi_users_status/{pdoa_id}', [PDOAController::class, 'get_wifi_users_status']);
    });
    Route::group(['prefix' => '/pdoa_plan'], function () {
        Route::get('/get_pdoa_plans', [PdoaPlanController::class, 'get_pdoa_plans']);
        Route::post('/add', [PdoaPlanController::class, 'add']);
        Route::post('/update/{plan_id}', [PdoaPlanController::class, 'update']);
        Route::get('/delete/{plan_id}', [PdoaPlanController::class, 'delete']);
    });

    Route::group(['prefix' => '/user_ip_log'], function () {
        Route::post('/add/{pdoa_id}', [UserIpLogsController::class, 'add']);
        Route::get('/get/{pdoa_id}/{filter}', [UserIpLogsController::class, 'get']);
    });

    Route::group(['prefix' => '/sms_log'], function () {
        Route::get('/get/{pdoa_id}', [SmsLogsController::class, 'get']);
    });

    Route::group(['prefix' => '/email_log'], function () {
        Route::get('/get/{pdoa_id}', [EmailLogsController::class, 'get']);
    });
});

Route::group([
    'middleware' => 'wifiapi',
    'prefix' => 'wifiuser/'
], function ($router) {
    Route::post('/login', [WiFiUsersController::class, 'login']);
    Route::post('/all', [WiFiUsersController::class, 'all_users']);
    Route::post('/direct-login', [WiFiUsersController::class, 'direct_login']);
    Route::get('/register', [WiFiUsersController::class, 'register']);
    Route::post('/register_user', [WiFiUsersController::class, 'register_user_pmwani']);
    Route::post('/register-pmwani-user', [WiFiUsersController::class, 'register_user_pmwani']);
    Route::post('/verify_email', [WiFiUsersController::class, 'verify_email']);
    Route::get('/verify-otp', [WiFiUsersController::class, 'verify_otp']);
    Route::get('/verify-url-code', [WiFiUsersController::class, 'verify_url_code']);
    Route::post('/generate-otp', [WiFiUsersController::class, 'send_otp']);
    Route::post('/info', [WiFiUsersController::class, 'info']);
    Route::get('/profile', [WiFiUsersController::class, 'profile']);
    Route::post('/get-login-url', [WiFiUsersController::class, 'get_login_url']);

    
    Route::post('/update_profile/{wifi_user_id}', [WiFiUsersController::class, 'update_profile']);
    Route::get('/get_profile/{wifi_user_id}', [WiFiUsersController::class, 'get_profile']);
    Route::get('/get_payment_log/{wifi_user_id}', [WiFiUsersController::class, 'get_payment_log']);
    Route::get('/get_session_log/{wifi_user_id}', [WiFiUsersController::class, 'get_session_log']);
});

Route::group([
    'middleware' => 'wifiapi',
    'prefix' => 'wifi/order/'
], function ($router) {
    Route::post('/all', [WiFiOrderController::class, 'all_orders']);
    Route::post('/create', [WiFiOrderController::class, 'create']);
    Route::get('/info/{id}', [WiFiOrderController::class, 'info']);
    Route::post('/process-payment', [WiFiOrderController::class, 'process_payment']);
});

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */
