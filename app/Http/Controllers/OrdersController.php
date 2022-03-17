<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Payment_setting;
use App\Models\PaymentReference;
use App\Models\Users;
use App\Models\PDOA;
use App\Models\Mail_server;
use App\Models\Orders;
use App\Models\OrderProducts;
use App\Models\Invoices;
use App\Models\Wifi_router;
use App\Models\WifiRouterModel;
use App\Models\Email_logs;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Razorpay\Api\Api;
use Validator;

class OrdersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => [
            'make_order', 'paid', 'get_order_summary', 'initiatePayment', 'paymentStatus', 'get_all_orders'
        ]]);
    }

    public function paid($order_id)
    {
        $order = Orders::find($order_id);

        if ($order["model_ids"] == "") $order->update(["status" => 3]);
        else $order->update(["status" => 1]);

        return response()->json([
            'success' => true,
            'message' => 'Order successfully marked as payed!',
        ]);
    }

    public function make_order(Request $request)
    {
        $user_id = $request->input("user_id");
        $pdoa_id = $request->input("pdoa_id");
        $carts = Cart::select("id", "description", "model_id", "request_amount")
            ->where("owner_id", "=", $user_id)
            ->where("status", "=", 0)
            ->get();
        if (count($carts)) {
            $id = $user_id . "_" . date("Y.m.d_h.i.s");
            $total = 0;
            $model_ids = "";
            $fee_description = "";
            $total_fee_description = "";
            $fee_amount = 0;
            $model_ids = "";
            $details = "";
            $processed = "";
            $total_price = 0;
            $subcontent = "";
            $products = [
              'pdoa_license_price' => 'PDOA License Price',
              'pdoa_setup_fee' => 'PDOA Setup Fee',
              'distributor_fee' => 'Distributor Fee',
              'franchise_fee' => 'Franchise Fee'
            ];
            for ($i = 0; $i < count($carts); $i++) {
                if ($carts[$i]["model_id"] == null || $carts[$i]["model_id"] == "") {
                    $fee_description = $carts[$i]["description"];
                    if($total_fee_description){
                      $total_fee_description = $total_fee_description . ', ' . $carts[$i]["description"];
                    } else {
                      $total_fee_description = $total_fee_description . $carts[$i]["description"];
                    }
                    $fee_amount = $carts[$i]["request_amount"];
                    $total_price = $total_price + $fee_amount;
                    $subcontent = $subcontent . '<div style="margin-top: 15px; display: flex;">
                                        <div style="font-weight: bold; width: 10%;">01</div>
                                        <div style="font-weight: bold; width: 40%;">' . $fee_description . '</div>
                                        <div style="width: 20%; color: #444444;""></div>
                                        <div style="width: 10%; color: #444444;""></div>
                                        <div style="width: 20%; text-align: right; color: #444444;"">₹ ' . $fee_amount . '</div>
                                    </div>
                                    <hr style="margin-top: 15px; border: 1px solid #EEEEEE;">';
                    OrderProducts::create([
                      "order_id" => $id,
                      "product_slug" => $fee_description,
                      "product_name" => $products[$fee_description],
                      "fee_amount" => $fee_amount
                    ]);
                } else {
                    $total = $total + $carts[$i]["request_amount"];
                    if ($model_ids != "") {
                        $model_ids = $model_ids . "," . $carts[$i]["model_id"];
                        $details = $details . "," . $carts[$i]["request_amount"];
                        $processed = $processed . ",0";
                    } else {
                        $model_ids = $carts[$i]["model_id"];
                        $details = $carts[$i]["request_amount"];
                        $processed = "0";
                    }
                    $model_id = explode(",", $model_ids);
                    $amount = explode(",", $details);
                    foreach ($model_id as $index => $m_id) {
                        $model = WifiRouterModel::find($m_id);
                        $index_no = $index + 1;
                        if ($fee_amount != 0) $index_no = $index + 2;
                        $subtotal = $model["price"] * $amount[$index];
                        $total_price = $total_price + $subtotal;
                        $subcontent = $subcontent . '<div style="margin-top: 15px; display: flex;">
                                <div style="font-weight: bold; width: 10%;">' . $index_no . '</div>
                                <div style="font-weight: bold; width: 40%;">' . $model["name"] . '</div>
                                <div style="width: 20%; color: #444444;"">₹ ' . $model["price"] . '</div>
                                <div style="width: 10%; color: #444444;"">' . $amount[$index] . '</div>
                                <div style="width: 20%; text-align: right; color: #444444;"">₹ ' . $subtotal . '</div>
                            </div>
                            <hr style="margin-top: 15px; border: 1px solid #EEEEEE;">';
                    }
                }
                Cart::find($carts[$i]['id'])->delete();
            }

            $order = Orders::create([
                "id" => $id,
                "owner_id" => $user_id,
                "fee_description" => $total_fee_description,
                "fee_amount" => $total_price,
                "model_ids" => $model_ids,
                "total_amount" => $total,
                "details" => $details,
                "processed" => $processed,
                "non_processed" => $details,
                "pdoa_id" => $pdoa_id,
                "status" => 0,
            ]);
            $order_date = $order["updated_at"];
            $server = PDOA::where(["id" => $pdoa_id])->get()->first();
            $admin = Users::where(["pdoa_id" => $pdoa_id])->where("role", "<", 3)->get()->first();
            $user = Users::find($user_id);

            Invoices::create([
              'slug' => 'INV_' . $user_id . "_" . date("Y.m.d_h.i.s"),
              'order_id' => $id,
              'user_id' => $user_id,
              'first_name' => $user["firstname"],
              'last_name' => $user["lastname"],
              'email' => $user["email"],
              'phone' => $user["phone_no"],
              'total_amount' => $total_price,
              'status' => 0
            ]);

            $mail_server = Mail_server::where(["pdoa_id" => $pdoa_id])->get()->first();

            $logo = "https://api.pulsewifi.net/default_logo.png";
            if ($server["brand_logo"] != null && $server["brand_logo"]) {
                $server["brand_logo"] = str_replace("public", "storage", $server["brand_logo"]);
                $logo = "https://api.pulsewifi.net/" . $server["brand_logo"];
            }
            $content = '<!DOCTYPE html>
            <html lang="en">
            <head>
              <meta charset="UTF-8">
              <meta http-equiv="X-UA-Compatible" content="IE=edge">
              <meta name="viewport" content="width=device-width, initial-scale=1.0">
              <title>Invoice</title>
            </head>
            <body>
              <div style="margin: 20px;">
                <div>
                  <img src="' . $logo . '" alt="logo" width="auto" height="40" />
                  <div style="margin-top: 15px; color: #888888;">
                   ' . $admin["address"] . ' ' . $admin["city"] . ' ' . $admin["state"] . ' ' . $admin["country"] . ', ' . $admin["postal_code"] . '
                  </div>
                  <div style="margin-top: 10px; color: #888888; display: flex;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope"
                      viewBox="0 0 16 16">
                      <path
                        d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z" />
                    </svg>&nbsp;&nbsp;
                    <div>' . $mail_server["sender_email"] . '</div>
                  </div>
                  <div style="margin-top: 10px; color: #888888; display: flex;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-telephone"
                      viewBox="0 0 16 16">
                      <path
                        d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122l-2.19.547a1.745 1.745 0 0 1-1.657-.459L5.482 8.062a1.745 1.745 0 0 1-.46-1.657l.548-2.19a.678.678 0 0 0-.122-.58L3.654 1.328zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z" />
                    </svg>&nbsp;&nbsp;
                    <div>' . $admin["phone_no"] . '</div>
                  </div>
                </div>
                <br>
                <hr style="border: 1px solid #EEEEEE;">
                <br>
                <div>
                  <div style="display: flex; justify-content: space-between;">
                    <div>
                      <div style="font-weight: bold; font-size: 18px;">Billed To:</div>
                      <div style="font-weight: bold; font-size: 18px; margin-top: 10px;">' . $user["firstname"] . ' ' . $user["lastname"] . '</div>
                      <div style="margin-top: 15px; color: #888888;">' . $user["address"] . ' ' . $user["city"] . ' ' . $user["state"] . ' ' . $user["country"] . ', ' . $user["postal_code"] . '
                      </div>
                      <div style="margin-top: 10px; color: #888888;">' . $user["email"] . '</div>
                      <div style="margin-top: 10px; color: #888888;">' . $user["phone_no"] . '</div>
                    </div>
                    <div>
                      <div style="margin-top: 20px;">
                        <div style="font-weight: bold; font-size: 18px; text-align: right;">Invoice Date:</div>
                        <div style="margin-top: 5px; color: #888888; text-align: right;">' . $order_date . '</div>
                      </div>
                      <div style="margin-top: 20px;">
                        <div style="font-weight: bold; font-size: 18px; text-align: right;">Order No:</div>
                        <div style="margin-top: 5px; color: #888888; text-align: right;">#' . $id . '</div>
                      </div>
                    </div>
                  </div>
                </div>
                <div style="margin-top: 40px">
                  <div style="font-weight: bold; font-size: 18px;">Order Summary</div>
                  <div style="margin-top: 15px; display: flex;">
                    <div style="font-weight: bold; font-size: 18px; width: 10%;">No.</div>
                    <div style="font-weight: bold; font-size: 18px; width: 40%;">Item</div>
                    <div style="font-weight: bold; font-size: 18px; width: 20%;">Price</div>
                    <div style="font-weight: bold; font-size: 18px; width: 10%;">Quantity</div>
                    <div style="font-weight: bold; font-size: 18px; width: 20%; text-align: right;">Total</div>
                  </div>
                  <hr style="margin-top: 15px; border: 1px solid #EEEEEE;">';

            $content = $content . $subcontent;
            $content = $content . '<div style="margin-top: 20px; text-align: right; font-weight: bold; font-size: 20px; display: flex; justify-content: space-between;">
                    <div></div>
                    <div style="display: flex; justify-content: space-between;">
                      <div style="margin-right: 50px;">Total</div>
                      <div>₹ ' . $total_price . '</div>
                    </div>
                  </div>
                  <div style="margin-top: 30px; text-align: right;">
                    <button style="cursor: pointer; padding: 10px 30px; background: #2d94ef; color: white; border: none; border-radius: 5px; font-size: 16px;">
                      <a href="https://' . $server["domain_name"] . '/order-details/' . $id . '?email=' . $user->email . '&email_verification_code=' . $user->email_verification_code . '" target="_blank" style="text-decoration: none; color: white;">Send</a>
                    </button>
                  </div>
                </div>
              </div>
            </body>
            </html>';

            //"https://'.$server["domain_name"].'/order-details?order_id='.$order_id.'&email='.$user_email.'&email_verification_code='.$code.'"

            $email = new \SendGrid\Mail\Mail();
            $email->setFrom($mail_server["sender_email"], $mail_server["sender_name"]);
            $email->setSubject("Sending Invoice from " . $server["domain_name"]);
            $email->addTo($user["email"], $user["firstname"] . " " . $user["lastname"]);
            // $email->addContent("text/plain", "and easy to do anywhere, even with PHP");
            $email->addContent(
                "text/html", $content
            );
            // $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
            $sendgrid = new \SendGrid($mail_server["api_key"]);
            try {
                $response = $sendgrid->send($email);
                // print $response->statusCode() . "\n";
                // print_r($response->headers());
                // print $response->body() . "\n";
                Email_logs::create([
                  'receiver_email' => $user["email"],
                  'subject' => "Sending Invoice from " . $server["domain_name"],
                  'pdoa_id' => $pdoa_id,
                ]);
            } catch (Exception $e) {
                echo 'Caught exception: ' . $e->getMessage() . "\n";
            }

            return response()->json([
                'success' => true,
                'message' => 'Your Order successfully accepted!',
            ]);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'No cart to make order!',
            ]);
        }
    }

    public function get_order($order_id)
    {
        $order = Orders::with(['user', 'invoice', 'pdoa', 'products'])->where('id', $order_id)->first();

        return response()->json([
            'success' => true,
            'message' => 'Getting Order success!',
            'data' => $order,
        ]);
    }

    public function get_order_summary($order_id)
    {
        $order = Orders::findOrFail($order_id);
        $pdoa_id = $order->pdoa_id;
        $server = PDOA::where(["id" => $pdoa_id])->get()->first();
        $admin = Users::where(["pdoa_id" => $pdoa_id])->where("role", "<", 3)->get()->first();
        $user = Users::find($order->owner_id);
        $mail_server = Mail_server::where(["pdoa_id" => $pdoa_id])->get()->first();
        $order_date = $order["updated_at"];
        $id = $order->id;

        $logo = "https://api.pulsewifi.net/default_logo.png";
        if ($server["brand_logo"] != null && $server["brand_logo"]) {
            $server["brand_logo"] = str_replace("public", "storage", $server["brand_logo"]);
            $logo = "https://api.pulsewifi.net/" . $server["brand_logo"];
        }

        $total_price = 0;
        $subcontent = "";
        $orders = [$order];
        for ($i = 0; $i < count($orders); $i++) {
            $fee_description = $orders[$i]->fee_description;
            $fee_amount = $orders[$i]->fee_amount;
            $total_price = $total_price + $fee_amount;
            $subcontent = $subcontent . '<div style="margin-top: 15px; display: flex;">
                                        <div style="font-weight: bold; width: 10%;">' . ($i + 1) . '</div>
                                        <div style="font-weight: bold; width: 40%;">' . $fee_description . '</div>
                                        <div style="width: 20%; color: #444444;">₹' . $fee_amount . '</div>
                                        <div style="width: 10%; color: #444444;"">1</div>
                                        <div style="width: 20%; text-align: right; color: #444444;"">₹ ' . $fee_amount . '</div>
                                    </div>
                                    <hr style="margin-top: 15px; border: 1px solid #EEEEEE;">';
        }
        if ($order->model_ids) {
            $ids = explode(',', $order->model_ids);
            $details = explode(',', $order->details);
            foreach ($ids as $index => $m_id) {
                $model = WifiRouterModel::find($m_id);
                $index_no = $index + count($orders) + 1;
                $subtotal = $model->price * $details[$index];
                $total_price = $total_price + $subtotal;
                $subcontent = $subcontent . '<div style="margin-top: 15px; display: flex;">
                                <div style="font-weight: bold; width: 10%;">' . $index_no . '</div>
                                <div style="font-weight: bold; width: 40%;">' . $model->name . '</div>
                                <div style="width: 20%; color: #444444;"">₹ ' . $model->price . '</div>
                                <div style="width: 10%; color: #444444;"">' . $details[$index] . '</div>
                                <div style="width: 20%; text-align: right; color: #444444;"">₹ ' . $subtotal . '</div>
                            </div>
                            <hr style="margin-top: 15px; border: 1px solid #EEEEEE;">';
            }
        }

        $content = '
              <div style="margin: 20px;">
                <div>
                  <img src="' . $logo . '" alt="logo" width="auto" height="40" />
                  <div style="margin-top: 15px; color: #888888;">
                   ' . $admin["address"] . ' ' . $admin["city"] . ' ' . $admin["state"] . ' ' . $admin["country"] . ', ' . $admin["postal_code"] . '
                  </div>
                  <div style="margin-top: 10px; color: #888888; display: flex;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope"
                      viewBox="0 0 16 16">
                      <path
                        d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z" />
                    </svg>&nbsp;&nbsp;
                    <div>' . $mail_server["sender_email"] . '</div>
                  </div>
                  <div style="margin-top: 10px; color: #888888; display: flex;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-telephone"
                      viewBox="0 0 16 16">
                      <path
                        d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122l-2.19.547a1.745 1.745 0 0 1-1.657-.459L5.482 8.062a1.745 1.745 0 0 1-.46-1.657l.548-2.19a.678.678 0 0 0-.122-.58L3.654 1.328zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z" />
                    </svg>&nbsp;&nbsp;
                    <div>' . $admin["phone_no"] . '</div>
                  </div>
                </div>
                <br>
                <hr style="border: 1px solid #EEEEEE;">
                <br>
                <div>
                  <div style="display: flex; justify-content: space-between;">
                    <div>
                      <div style="font-weight: bold; font-size: 18px;">Billed To:</div>
                      <div style="font-weight: bold; font-size: 18px; margin-top: 10px;">' . $user["firstname"] . ' ' . $user["lastname"] . '</div>
                      <div style="margin-top: 15px; color: #888888;">' . $user["address"] . ' ' . $user["city"] . ' ' . $user["state"] . ' ' . $user["country"] . ', ' . $user["postal_code"] . '
                      </div>
                      <div style="margin-top: 10px; color: #888888;">' . $user["email"] . '</div>
                      <div style="margin-top: 10px; color: #888888;">' . $user["phone_no"] . '</div>
                    </div>
                    <div>
                      <div style="margin-top: 20px;">
                        <div style="font-weight: bold; font-size: 18px; text-align: right;">Invoice Date:</div>
                        <div style="margin-top: 5px; color: #888888; text-align: right;">' . $order_date . '</div>
                      </div>
                      <div style="margin-top: 20px;">
                        <div style="font-weight: bold; font-size: 18px; text-align: right;">Order No:</div>
                        <div style="margin-top: 5px; color: #888888; text-align: right;">#' . $id . '</div>
                      </div>
                    </div>
                  </div>
                </div>
                <div style="margin-top: 40px">
                  <div style="font-weight: bold; font-size: 18px;">Order Summary</div>
                  <div style="margin-top: 15px; display: flex;">
                    <div style="font-weight: bold; font-size: 18px; width: 10%;">No.</div>
                    <div style="font-weight: bold; font-size: 18px; width: 40%;">Item</div>
                    <div style="font-weight: bold; font-size: 18px; width: 20%;">Price</div>
                    <div style="font-weight: bold; font-size: 18px; width: 10%;">Quantity</div>
                    <div style="font-weight: bold; font-size: 18px; width: 20%; text-align: right;">Total</div>
                  </div>
                  <hr style="margin-top: 15px; border: 1px solid #EEEEEE;">';

        $content = $content . $subcontent;
        $content = $content . '<div style="margin-top: 20px; text-align: right; font-weight: bold; font-size: 20px; display: flex; justify-content: space-between;">
                    <div></div>
                    <div style="display: flex; justify-content: space-between;">
                      <div style="margin-right: 50px;">Total</div>
                      <div>₹ ' . $total_price . '</div>
                    </div>
                  </div>
                  <div style="margin-top: 30px; text-align: right;">
                    <button data-target="' . $id . '" class="pay-now" style="cursor: pointer; padding: 10px 30px; background: #2d94ef; color: white; border: none; border-radius: 5px; font-size: 16px;">
                      Pay Now
                    </button>
                  </div>
                </div>
              </div>';

        if ($order->status != 0) {
            $content = '<h5>Order has already been processed!</h5><a class="btn btn-info" href="/">Continue</a>';
        }

        return response()->json([
            'success' => true,
            'message' => 'Getting Order success!',
            'data' => compact('order', 'content', 'total_price', 'user'),
        ]);
    }

    public function get_all_orders($pdoa_id)
    {
        $user = auth()->user();
        
        $orders = Orders::leftJoin('users', 'orders.owner_id', '=', 'users.id')
            ->select("orders.status", "orders.id", "users.firstname", "users.lastname", "orders.owner_id", "orders.fee_description", "orders.fee_amount", "orders.model_ids", "orders.total_amount", "orders.details", "orders.processed", "orders.created_at", "orders.updated_at");
        if($pdoa_id){
          $orders = $orders->where("orders.pdoa_id", $pdoa_id);
        }
        $orders = $orders->groupBy("orders.status", "orders.id", "users.firstname", "users.lastname", "orders.owner_id", "orders.fee_description", "orders.fee_amount", "orders.model_ids", "orders.total_amount", "orders.details", "orders.processed", "orders.created_at", "orders.updated_at")
            ->orderBy('orders.updated_at', 'desc')
            ->orderBy("orders.status", 'desc')
            ->orderBy('orders.total_amount', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Getting all orders success!',
            'data' => $orders,
        ]);
    }

    public function get_unpaid_orders($pdoa_id)
    {
        $user = auth()->user();
        $orders = Orders::leftJoin('users', 'orders.owner_id', '=', 'users.id')
            ->select("orders.status", "orders.id", "users.firstname", "users.lastname", "orders.owner_id", "orders.fee_description", "orders.fee_amount", "orders.model_ids", "orders.total_amount", "orders.details", "orders.processed", "orders.created_at")
            ->where("orders.status", "=", 0)
            ->where("orders.pdoa_id", $pdoa_id)
            ->groupBy("orders.status", "orders.id", "users.firstname", "users.lastname", "orders.owner_id", "orders.fee_description", "orders.fee_amount", "orders.model_ids", "orders.total_amount", "orders.details", "orders.processed", "orders.created_at")
            ->orderBy('orders.updated_at', 'desc')
            ->orderBy("orders.status", 'desc')
            ->orderBy('orders.total_amount', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Getting all orders success!',
            'data' => $orders,
        ]);
    }

    public function get_new_orders($pdoa_id)
    {
        $user = auth()->user();
        
        $orders = Orders::leftJoin('users', 'orders.owner_id', '=', 'users.id')
            ->select("orders.status", "orders.id", "users.firstname", "users.lastname", "orders.owner_id", "orders.fee_description", "orders.fee_amount", "orders.model_ids", "orders.total_amount", "orders.details", "orders.processed", "orders.created_at")
            ->where("orders.status", "=", 1)
            ->where("orders.pdoa_id", $pdoa_id)
            ->groupBy("orders.status", "orders.id", "users.firstname", "users.lastname", "orders.owner_id", "orders.fee_description", "orders.fee_amount", "orders.model_ids", "orders.total_amount", "orders.details", "orders.processed", "orders.created_at")
            ->orderBy('orders.updated_at', 'desc')
            ->orderBy("orders.status", 'desc')
            ->orderBy('orders.total_amount', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Getting all orders success!',
            'data' => $orders,
        ]);
    }

    public function get_processed_orders($pdoa_id)
    {
        $user = auth()->user();
        $orders = Orders::leftJoin('users', 'orders.owner_id', '=', 'users.id')
            ->select("orders.status", "orders.id", "users.firstname", "users.lastname", "orders.owner_id", "orders.fee_description", "orders.fee_amount", "orders.model_ids", "orders.total_amount", "orders.details", "orders.created_at", "orders.updated_at")
            ->where("orders.status", "=", 3)
            ->where("orders.pdoa_id", $pdoa_id)
            ->groupBy("orders.status", "orders.id", "users.firstname", "users.lastname", "orders.owner_id", "orders.fee_description", "orders.fee_amount", "orders.model_ids", "orders.total_amount", "orders.details", "orders.created_at", "orders.updated_at")
            ->orderBy('orders.updated_at', 'desc')
            ->orderBy("orders.status", 'desc')
            ->orderBy('orders.total_amount', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Getting all orders success!',
            'data' => $orders,
        ]);
    }

    public function get_incompleted_orders($pdoa_id)
    {
        $user = auth()->user();
        $orders = Orders::leftJoin('users', 'orders.owner_id', '=', 'users.id')
            ->select("orders.status", "orders.id", "users.firstname", "users.lastname", "orders.owner_id", "orders.fee_description", "orders.fee_amount", "orders.model_ids", "orders.total_amount", "orders.details", "orders.processed", "orders.created_at", "orders.updated_at")
            ->where("orders.status", "=", 2)
            ->where("orders.pdoa_id", $pdoa_id)
            ->groupBy("orders.status", "orders.id", "users.firstname", "users.lastname", "orders.owner_id", "orders.fee_description", "orders.fee_amount", "orders.model_ids", "orders.total_amount", "orders.details", "orders.processed", "orders.created_at", "orders.updated_at")
            ->orderBy('orders.updated_at', 'desc')
            ->orderBy("orders.status", 'desc')
            ->orderBy('orders.total_amount', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Getting all orders success!',
            'data' => $orders,
        ]);
    }

    public function process_order(Request $request, $order_id)
    {
        $order = Orders::find($order_id);
        if (!$order) {
            return response()->json([
                'success' => true,
                'message' => 'NoOrder',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'processed' => 'required|string',
            'status' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ValidationError',
                'data' => $validator->errors(),
            ]);
        }

        foreach ($request->all() as $key => $value) {
            $arr_update_keys[$key] = $value;
        }

        $order->update($arr_update_keys);

        $router_ids = explode(",", $request->input("router_ids"));
        for ($i = 0; $i < count($router_ids); $i++) {
            Wifi_router::where(['id' => $router_ids[$i]])->update(['owner_id' => $order["owner_id"]]);
            // $router = Wifi_router::where(['id' => $router_ids[$i]])->get()->first();
            // echo json_encode($router);
            // $router->update(['owner_id' => $order["owner_id"]]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order successfully updated!',
        ]);
    }

    public function initiatePayment($order_id, Request $request)
    {
        $order = Orders::findOrFail($order_id);

        $user = Users::find($order->owner_id);

        $total_price = 0;
        $total_price += $order->fee_amount;
        if ($order->model_ids) {
            $ids = explode(',', $order->model_ids);
            $details = explode(',', $order->details);
            foreach ($ids as $index => $m_id) {
                $model = WifiRouterModel::find($m_id);
                $total_price += ($model->price * $details[$index]);
            }
        }

        $server = PDOA::where(["id" => $order->pdoa_id])->first();

        $logo = "https://api.pulsewifi.net/default_logo.png";
        if ($server["brand_logo"]) {
            $server["brand_logo"] = str_replace("public", "storage", $server["brand_logo"]);
            $logo = "https://api.pulsewifi.net/" . $server["brand_logo"];
        }

        $extra = ['name' => $server->brand_name ?? 'Pulse WiFi', 'image' => $logo, 'description' => 'Order# ' . $order->id];

        $reference = PaymentReference::where('order_id', $order->id)->latest()->first();
        if ($reference && $reference->status == 'success') {
            return response()->json([
                'success' => false,
                'message' => 'Order has been previously paid! Please contact Administrator.',
            ], 400);
        }

        if ($reference && $reference->status == 'failed') {
            $reference = null;
        }
        // Todo: should get payment based on pdoa_id?!
        $settings = Payment_setting::first();
        $api_key = $settings->key;
        $api_secret = $settings->secret;
        $amount = (int)round($total_price * 100);

        if (!$reference) {
            $api = new Api($api_key, $api_secret);
            try {
                $razor_order = $api->order->create([
                    'receipt' => '123',
                    'amount' => $amount,
                    'currency' => 'INR',
                    'notes' => ['order_id' => $order_id],
                ]);

                $reference = new PaymentReference();
                $reference->order_id = $order->id;
                $reference->razorpay_order_id = $razor_order->id;
                $reference->save();
            } catch (Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'data' => $e->getTrace()
                ], 500);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Placed Razor order successfully!',
            'data' => compact('order', 'reference', 'api_key', 'amount', 'user', 'extra'),
        ]);
    }

    public function paymentStatus($razor_order_id, Request $request)
    {
        DB::beginTransaction();
        $payment_reference = PaymentReference::where('razorpay_order_id', $razor_order_id)->first();
        $payment_reference->status = $request->status;
        if ($request->status == 'success') {
            $payment_reference->razorpay_payment_reference = $request->input('response.razorpay_payment_id');

            $order = Orders::find($payment_reference->order_id);
            if ($order["model_ids"] == "") $order->update(["status" => 3]);
            else $order->update(["status" => 1]);

        }
        $payment_reference->razorpay_response = $request->all();
        $payment_reference->save();
        DB::commit();

        return response()->json(['success' => true, 'data' => $payment_reference]);
    }

}
