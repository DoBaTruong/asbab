<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\Delivery;
use App\Models\Product;
use App\Models\Order;
use App\Models\Bill;
use App\Models\User;
use App\Models\Sell;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Exception\PayPalConnectionException;
use PayPal\Rest\ApiContext;
use Mail;
use Role;
use Hash;
use Log;
use DB;

class CheckoutController extends Controller
{
    public function __construct()
    {
        $this->apiContext = new ApiContext(
            new OAuthTokenCredential(
                'AQaayUhnL3b_f_6LWjo71z2k7Nayvog1l4H1SIQRhFmUG7os4-uKOx1NH94znmd1_yVa2cdNXHg41FzW',
                'EBcTsAvB4_5zke39gnNZsdOtJV3CMMDenvc0MIM8DyC86684qMydJkb5ZqeDKJWaUDjj_0NJ3r-xsF3M'
            )
        );
    }
    public function coupon(Request $request)
    {   
        if(trim($request->coupon_code) !== '') {
            $coupon = Coupon::where('code', $request->coupon_code)->first();
            if (isset($coupon)) {
                if(strtotime($coupon->time_out_of) >= time()) {
                    $couponAuth = Coupon::where('code', $request->coupon_code)->where('used','LIKE', '%'.auth()->id().'%')->first();
                    if(isset($couponAuth)) {
                        return response()->json([
                            'message' => 'Coupon code is used!'
                        ], 422);
                    } else {
                        session()->put('coupon', $coupon);
                        return response()->json([
                            'coupon' => session()->get('coupon'),
                            'code' => 200
                        ], 200);
                    }
                } else {
                    return response()->json([
                        'message' => 'Coupon code is expired!'
                    ], 422);
                }
            } else {
                return response()->json([
                    'message' => 'Coupon code not exist!'
                ], 422);
            }
        } else {
            return response()->json([
                'message' => 'Enter your coupon code, please!'
            ], 422);
        }
    }

    public function calc_fee_ship(Request $request)
    {
        $validator = $request->validate([
            'province_id' => 'required',
            'district_id' => 'required',
            'ward_id'=> 'required',
            'details_address' => 'required'
        ]);

        try {
            DB::beginTransaction();
            $feeDelivery = Delivery::where('province_id', $request->province_id)->where('district_id', $request->district_id)->where('ward_id', $request->ward_id)->first();
            if(isset($wardFee)) {
                $fee = $feeDelivery->feeship;
            } else {
                $fee = 50;
            }
            
            session()->put('fee_ship', [
                'fee' => $fee,
                'province_id' => $request->province_id,
                'district_id' => $request->district_id,
                'ward_id' => $request->ward_id,
                'address' => $request->address
            ]);

            DB::commit();
            return response()->json([
                'feeship' => session()->get('fee_ship'),
                'coupon' => session()->get('coupon'),
                'code' => 200,
                'message' => 'success'
            ], 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Message: '.$exception->getMessage().' line: '.$exception->getLine());
            return response()->json([
                'message' => 'There are incorrect values in the form !',
                'errors' => $validator->getMessageBag()->toArray()
            ], 422);
        }
    }

    public function payment(Request $request)
    {
        $fee_ship = session()->get('fee_ship');
        if($fee_ship === null) {
            return response()->json([
                'message' => 'There are incorrect values in the form !',
                'errors' => [
                    'details_address' => 'Enter your address, please !'
                ]
            ], 422);
        } else {
            if($request->create_account == 1) {
                $validator = $request->validate([
                    'customer_name' => 'required',
                    'customer_mail' => 'bail|required|email|unique:users,email',
                    'customer_phone' => 'bail|required|regex:/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/im',
                    'agreeTerm' => 'required',
                    'customer_password' => 'bail|required|min:6'
                ]);
            } else {
                $validator = $request->validate([
                    'customer_name' => 'required',
                    'customer_mail' => 'bail|required|email',
                    'customer_phone' => 'bail|required|regex:/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/im'
                ]);
            }
            try {
                DB::beginTransaction();
                if($request->create_account == 1) {
                    $user = [
                        'name' => $request->customer_name,
                        'email' => $request->customer_mail,
                        'password' => Hash::make($request->customer_password),
                        'phone' => $request->customer_phone,
                        'type' => 5,
                        'address' => $fee_ship['address']
                    ];
                } else {
                    $user = null;
                }
                
                $order = [
                    'code' => substr(md5(microtime()),rand(0,26),6),
                    'name' => $request->customer_name,
                    'phone' => $request->customer_phone,
                    'mail' => $request->customer_mail,
                    'address' => $fee_ship['address'],
                    'coupon_id' => session()->get('coupon') !== null ? session()->get('coupon')->id : null,
                    'user_id' => $request->user_id,
                    'fee_ship' => $fee_ship['fee'],
                    'amount' => $request->amount,
                    'paymethod' => $request->paymethod
                ];

                $payment = [
                    'order' => $order,
                    'user' => $user
                ];

                session()->put('payment', $payment);

                DB::commit();
                
                switch ($request->paymethod) 
                {
                    case '0': $urlRedirect = route('asbab.checkout.paypal'); break;
                    case '1': $urlRedirect = route('asbab.checkout.success'); break;
                    case '2': $urlRedirect = route('asbab.checkout.vnpay'); break;
                }
                return response()->json($urlRedirect, 200);
            } catch (\Exception $exception) {
                DB::rollBack();
                Log::error('Message: '.$exception->getMessage().' line: '.$exception->getLine());
                return response()->json([
                    'message' => 'There are incorrect values in the form !',
                    'errors' => $validator->getMessageBag()->toArray()
                ], 422);
            }
        }
    }

    public function paypal()
    {
        session()->put('checkout', 'paypal');
        $order = (session()->get('payment'))['order'];
        if ($order['coupon_id'] !== null) {
            $coupon = Coupon::find($order['coupon_id']);
            if ($coupon->type == 0) {
                $discount = $coupon->discount;
            } else {
                $discount = $order['amount'] * $coupon->discount / 100;
            }
        } else {
            $discount = 0;
        }

        $total = $order['amount'] * 1.1 + $order['fee_ship'] - $discount;
        $tax = $order['amount'] * 0.1;

        $payer = new Payer();
        $payer->setPaymentMethod('paypal');
        $items = [];

        foreach (session()->get('cart') as $key => $cart) {
            $item = new Item();
            $item->setName($cart['name'])
                ->setCurrency('USD')
                ->setQuantity($cart['quantity'])
                ->setPrice($cart['price']);
            $items[] = $item;
        }
        $itemList = new ItemList();
        $itemList->setItems($items);

        $details = new Details();
        $details->setSubtotal($order['amount'])
                ->setShipping($order['fee_ship'])
                ->setTax($tax)
                ->setHandlingFee(-$discount);

        $amount = new Amount();
        $amount->setTotal($total)
                ->setCurrency('USD')
                ->setDetails($details);
        $transaction = new Transaction();
        $transaction->setAmount($amount)
                    ->setItemList($itemList)
                    ->setDescription($order['name'])
                    ->setInvoiceNumber($order['code']);

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl(route('asbab.checkout.success'))
                    ->setCancelUrl(route('asbab.checkout.cancel'));

        $payment = new Payment();
        $payment->setIntent('sale')
                ->setPayer($payer)
                ->setRedirectUrls($redirectUrls)
                ->setTransactions([$transaction]);
        try {
            $payment->create($this->apiContext);
            return  redirect($payment->getApprovalLink());
        } catch (PayPalConnectionException $paypalException) {
            echo $paypalException->getData();
            // throw new \Exception($paypalException->getMessage());
        }
    }

    public function vnpay() 
    {
        $order = (session()->get('payment'))['order'];
        if ($order['coupon_id'] !== null) {
            $coupon = Coupon::find($order['coupon_id']);
            if ($coupon->type == 0) {
                $discount = $coupon->discount;
            } else {
                $discount = $order['amount'] * $coupon->discount / 100;
            }
        } else {
            $discount = 0;
        }
        $total = $order['amount'] * 1.1 + $order['fee_ship'] - $discount;

        $vnp_TmnCode = "X8I14G3R"; //Mã website tại VNPAY 
        $vnp_HashSecret = "UMVJGBUSKZJSDQRUTGLULAMQBXGTIVBR"; //Chuỗi bí mật
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = route('asbab.checkout.vnpay.return');
        $vnp_TxnRef = $order['code']; //Mã đơn hàng. Trong thực tế Merchant cần insert đơn hàng vào DB và gửi mã này sang VNPAY
        $vnp_OrderInfo = "Payment Invoice Service";
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $total * 23000;
        $vnp_Locale = 'vn';
        $vnp_IpAddr = request()->ip();

        $inputData = array(
            "vnp_Version" => "2.0.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount * 100, 
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . $key . "=" . $value;
            } else {
                $hashdata .= $key . "=" . $value;
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }
        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash('sha256', $vnp_HashSecret . $hashdata);
            $vnp_Url .= 'vnp_SecureHashType=SHA256&vnp_SecureHash=' . $vnpSecureHash;
        }
        return redirect($vnp_Url);
    }

    public function vnpayReturn(Request $request)
    {
        if ($request->vnp_ResponseCode == '00') {
            return redirect()->route('asbab.checkout.success');
        } else {
            return redirect()->route('asbab.checkout.cancel');
        }
    }

    public function success(Request $request)
    {
        if (session()->get('checkout') == 'paypal') 
        {
            $apiContext = new \PayPal\Rest\ApiContext(
                new \PayPal\Auth\OAuthTokenCredential(
                    'AZOybgnUFnEbb6xniaVqaeZXI21OYTP-xVOa-Cv3_uf0Quwj0If5358kUMdHjb5s2wV225FxcTsU2w1m',     // ClientID
                    'EEEwHsKC-CYRc82_Ec1aEk-qBAkh3Bz2_W6A7CekAPuL1Sc4l5gbh-fuCjbm8-JqeLrmXacYIQmTnNZt'      // ClientSecret
                )
            );

            $paymentId = $_GET['paymentId'];
            $payment = \PayPal\Api\Payment::get($paymentId, $apiContext);
            $execution = new \PayPal\Api\PaymentExecution();
            $execution->setPayerId($_GET['PayerID']);
            $payment->execute($execution, $apiContext);
        }
        try {
            DB::beginTransaction();
            $order = (session()->get('payment'))['order'];
            $user = (session()->get('payment'))['user'];
            if ($user !== null) {
                $userNew = User::create($user);
                $role = Role::where('name', 'Visitor')->first();
                $userNew->roles()->sync($role->id);
                $user_id = $userNew->id;
            } else {
                $user_id = $order['user_id'];
            }

            $orderNew = Order::create($order);

            $orderNew->update([
                'user_id' => $user_id
            ]);

            if($orderNew->paymethod != 1) {
                $orderNew->update([
                    'status' => 1
                ]);
            }

            if(session()->get('coupon') !== null) {
                Coupon::find(session()->get('coupon')->id)->update([
                    'quantity' => session()->get('coupon')->quantity - 1,
                    'used' => session()->get('coupon')->used !== null ? get('coupon')->used.','.$user_id : $user_id
                ]);
            }

            foreach (session()->get('cart') as $key => $cart) {
                $bill[] = Bill::create([
                    'order_id' => $orderNew->id,
                    'product_id' => $key,
                    'product_name' => $cart['name'],
                    'product_price' => $cart['price'],
                    'quantity' => $cart['quantity']
                ]);
                
                Product::find($key)->update([
                    'sell' => Product::find($key)->sell + $cart['quantity']
                ]);

                Sell::create([
                    'product_id' => $key,
                    'quantity' => $cart['quantity'],
                    'amount' => $cart['price'] * $cart['quantity']
                ]);
            }

            $title = 'The order has been confirmed at '.date('d/m/Y');
            $cus_mail = $orderNew->mail;
            $cus_name = $orderNew->name;

            if ($orderNew->paymethod != 1) {
                $cashout = true;
            } else {
                $cashout = false;
            }

            Mail::send('asbab.mail_order', ['order' => $orderNew, 'cashout' => $cashout], function ($message) use ($title, $cus_mail, $cus_name) {
                $message->from('admin.asbabfurniture@gmail.com', 'Asbab Furniture Shop');
                $message->subject($title);
                $message->to($cus_mail, $cus_name);
            });
            DB::commit();
            session()->forget('cart');
            session()->forget('coupon');
            session()->forget('fee_ship');
            session()->forget('payment');
            
            $notify = '<div class="alert alert-success row">The order has been placed, success !</div>';
            return view('asbab.cart', compact('notify'));
        } catch (\PayPal\Exception\PayPalConnectionException $ex) {
            die($ex);
        }
    }

    public function cancel()
    {
        $notify = '<div class="alert alert-danger row">The order has been canceled !</div>';
        return view('asbab.cart', compact('notify'));
    }
}
