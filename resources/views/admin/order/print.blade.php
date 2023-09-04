<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <title>Invoice</title>
    <link rel="stylesheet" href="{{ $order->bootstrap }}" />
</head>

<body>
    <header style="padding-bottom: 0">
        <div style="display: flex;">
            <div style="width: 25%; position: relative; top: 40px">
                <img src="{{ $order->shop_logo_path }}" alt="" />
            </div>
            <div style="position: relative; left: 25%; width: 75%, left: 25%">
                <h1 class="text-primary text-center">INVOICE</h1>
                <div class="alert">
                    <span style="width: 33.3%; display: inline-block"><span class="alert-link">DATE:</span>
                        {{ date('d/m/Y') }}</span>
                    <span style="width: 33.3%; display: inline-block" class="text-uppercase text-center"><span
                            class="alert-link">ORDER
                            CODE:</span> {{ $order->code }}</span>
                    <span style="width: 33.3%; display: inline-block; text-align:right"><span
                            class="alert-link">SHIPPER:</span> {{ $order->shippers->name }}</span>
                </div>
            </div>
        </div>
        <div style="display: flex;">
            <div style="width: 50%">
                <h4 style="text-decoration: underline">SOLD TO:</h4>
                <table class="table">
                    <tbody>
                        <tr class="alert">
                            <td style="border: none; padding-left: 0">SHOP NAME: <span
                                    class="alert-link text-uppercase">{{ $order->shop_name }}</span>
                            </td>
                        </tr>
                        <tr class="alert">
                            <td style="border: none; padding-left: 0">HOTLINE: <span
                                    class="alert-link">{{ $order->shop_phone }}</span>
                            </td>
                        </tr>
                        <tr class="alert">
                            <td style="border: none; padding-left: 0">ADDRESS: <span
                                    class="alert-link">{{ $order->shop_address }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div style="width: 50%; position: absolute; left: 50%">
                <h4 style="text-decoration: underline">SHIP TO:</h4>
                <table class="table table-borderless">
                    <tbody>
                        <tr class="alert">
                            <td style="border: none; padding-left: 0">BUYER: <span
                                    class="alert-link">{{ $order->users->name }}</span></td>
                        </tr>
                        <tr class="alert">
                            <td style="border: none; padding-left: 0">PHONE: <span
                                    class="alert-link">{{ $order->phone }}</span></td>
                        </tr>
                        <tr class="alert">
                            <td style="border: none; padding-left: 0">ADDRESS: <span
                                    class="alert-link">{{ $order->address }}</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </header>
    <main>
        <div>
            <div class="alert" style="padding: 0">
                <h3 class="text-center text-success alert-link">ORDER DETAILS</h3>
                <table class="table">
                    <thead class="bg-info">
                        <tr>
                            <th class="text-justify">Number</th>
                            <th class="text-justify">Product</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Unit Price</th>
                            <th class="text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $amount = 0;
                        @endphp
                        @foreach ($order->bills as $key => $bill)
                            @php
                                $total = $bill->product_price * $bill->quantity;
                                $amount += $total;
                            @endphp
                            <tr class="text-center">
                                <td class="text-justify">{{ $key + 1 }}</td>
                                <td class="text-justify">{{ $bill->product_name }}</td>
                                <td>{{ $bill->quantity }}</td>
                                <td>{{ '$' . number_format($bill->product_price, 2, '.', ',') }}</td>
                                <td>{{ '$' . number_format($total, 2, '.', ',') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        @php
                            $tax = $amount * 0.1;
                            $feeship = $order->fee_ship;
                            if (isset($order->coupons)) {
                                switch ($order->coupons->type) {
                                    case '0':
                                        $discount = $order->coupons->discount;
                                        break;
                                    case '1':
                                        $discount = ($order->coupons->discount * $amount) / 100;
                                        break;
                                    default:
                                        $discount = 0;
                                }
                            } else {
                                $discount = 0;
                            }
                        @endphp
                        <tr class="text-right">
                            <td colspan="5" style="padding-right: 0">Fee ship:
                                ${{ number_format($feeship, 2, '.', ',') }}</td>
                        </tr>
                        <tr class="table-borderless text-right">
                            <td colspan="5" style="border: none; padding-right: 0">Tax: ${{ number_format($tax, 2, '.', ',') }}</td>
                        </tr>
                        @if (!empty($order->coupon_id))
                            <tr class="table-borderless text-right">
                                <td colspan="5" style="border: none; padding-right: 0">Discount:
                                    - ${{ number_format($discount, 2, '.', ',') }}
                                </td>
                            </tr>
                        @endif
                        <tr class="table-borderless text-right">
                            <td colspan="5" style="border: none; padding-right: 0">Amount: <span
                                    class="alert-link text-danger">${{ number_format($amount + $tax + $feeship - $discount, 2, '.', ',') }}</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </main>
    <footer>
            <table class="table">
                <tbody>
                    <tr class="alert">
                        <td class="text-center" style="border: none">
                            <div>Seller</div>
                            <div><i>(Signature, Full name)</i></div>
                        </td>
                        <td class="text-center" style="border: none">
                            <div>Buyer</div>
                            <div><i>(Signature, Full name)</i></div>
                        </td>
                    </tr>
                </tbody>
            </table>
    </footer>
</body>

</html>
