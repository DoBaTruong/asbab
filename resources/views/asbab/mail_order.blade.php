<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Order Confirm</title>
    <link rel="stylesheet" href="{{ asset('guest/assets/bootstrap/bootstrap.min.css') }}" />
    <style>
        body {
            font-family: serif
        }

        .table thead th {
            border: none;
            border-top: 1px solid #fff
        }

        .table {
            width: 100% !important;
            display: table;
            color: #fff
        }

        .table td {
            padding: 10px 0;
            border-top: 1px solid #fff
        }

    </style>
</head>

<body>
    <div class="container" style="background: #222; border-radius: 12px; padding: 20px 15px">
        <div class="col-md-12">
            <p style="text-align: center; color: #fff">This is an automated email. Please do not reply to this email !
            </p>
            <div class="row" style="background: cadetblue; padding: 15px; border-radius: 10px">
                <div class="col-12" style="text-align: center; color: #fff;">
                    <h2 style="font-weight: 900; text-align: center; display: block">ASBAB FURNITURE</h2>
                    <h4 style="text-transform: uppercase; font-family: sans-serif;">Prestige -
                        Quanlity - Fast</h4>
                </div>
                <div class="col-12">
                    <p style="color: #fff">Dear <strong style="color: #000; text-decoration: underline">Do Ba
                            Truong</strong>,</p>
                </div>
                <div class="col-12">
                    <p style="color: #fff">You or someone has been ordered at shop with information as:
                    </p>
                    <h4 style="color: #fff; text-transform: uppercase">Order information:</h4>
                    <p></p>
                    <p style="margin-left: 10px">- Code: <strong
                            style="color: #fff; text-transform: uppercase">{{ $order->code }}</strong></p>
                    <p style="margin-left: 10px">- Coupon: <strong
                            style="color: #fff; text-transform: uppercase">{{ $order->coupon_id !== null ? $order->coupons->code : 'none' }}</strong>
                    </p>
                    <p style="margin-left: 10px">- Fee ship: <strong
                            style="color: #fff;">${{ number_format($order->fee_ship, 2, '.', ',') }}</strong></p>
                    <p style="margin-left: 10px">- Note: <strong
                            style="font-weight: 700; color: #fff;">{{ $order->note !== null ? $order->note : 'none' }}</strong>
                    </p>
                </div>
                <div class="col-12">
                    <h4 style="color: #fff; text-transform: uppercase">Customer information:</h4>
                    <p></p>
                    <p style="margin-left: 10px">- Full name: <strong style="color: #fff;">{{ $order->name }}/strong>
                    </p>
                    <p style="margin-left: 10px">- Email: <strong style="color: #fff;">{{ $order->mail }}</strong>
                    </p>
                    <p style="margin-left: 10px">- Phone: <strong style="color: #fff;">{{ $order->phone }}</strong>
                    </p>
                    <p style="margin-left: 10px">- Address receive: <strong
                            style="color: #fff;">{{ $order->address }}</strong>
                    </p>
                    <p style="margin-left: 10px">- Payment method: <strong
                            style="text-transform: uppercase; font-weight: 700; color: #fff;">
                            @switch($order->paymethod)
                                @case(0)
                                    {{ 'Paypal' }}
                                @break
                                @case(1)
                                    {{ 'Cash on delievery' }}
                                @break
                                @case(2)
                                    {{ 'Credit card' }}
                                @break
                                @case(3)
                                    {{ 'Direct bank transfer' }}
                                @break
                            @endswitch
                        </strong>
                    </p>
                </div>
                <div class="col-12">
                    <p style="color: #fff;">If recipient information is not available, we will contact
                        the orderer to exchange information
                        about the order placed.</p>
                    <table class="table col-12">
                        <thead>
                            <tr style="text-align: center">
                                <th></th>
                                <th style="text-align: justify">Name</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $total = 0;
                            @endphp
                            @foreach ($order->bills as $key => $bill)
                                @php
                                    $amount = $bill->product_price * $bill->quantity;
                                    $total += $amount;
                                @endphp
                                <tr style="text-align: center">
                                    <td>
                                        <span
                                            style="padding-left: 15px; padding-right: 15px;">{{ $key }}</span>
                                    </td>
                                    <td style="text-align: justify">{{ $bill->product_name }}</td>
                                    <td>${{ number_format($bill->product_price, 2, '.', ',') }}</td>
                                    <td>{{ $bill->quantity }}</td>
                                    <td>${{ number_format($amount, 2, '.', ',') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        @php
                            if ($order->coupon_id !== null) {
                                switch ($order->coupons->type) {
                                    case '0':
                                        $discount = $order->coupons->discount;
                                        break;
                                    case '1':
                                        $discount = ($total * $order->coupons->discount) / 100;
                                        break;
                                }
                            } else {
                                $discount = 0;
                            }
                        @endphp
                        <tfoot>
                            <tr>
                                <td colspan="5" style="text-align: right">
                                    Total:
                                    <span>${{ number_format($total - $discount + $order->fee_ship + $total * 0.1, 2, '.', ',') }}</span>
                                    (10% VAT)
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="col-12">
                    <p style="color: #fff">For more information, please contact us at the website: <a
                            href="{{ route('asbab.contact.index') }}">{{ route('asbab.home') }}</a>, or contact the hotline:
                        0988041615. Thank you for your order in our shop !</p>
                </div>
            </div>
            @if (empty($cashout))
                <div class="col-12" style="margin-top: 20px">
                    <a style="background-color: #fff; padding: 10px 20px; margin: 0 auto; display: block; width:max-content; text-decoration:none; color: #000; border-radius: 4px;"
                        href="{{ route('asbab.order.confirm', ['id' => $order->id]) }}">Confirm Order</a>
                </div>
            @endif
        </div>
    </div>
</body>

</html>
