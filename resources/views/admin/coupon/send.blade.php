<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif
        }

        .coupon {
            border: 5px dotted #bbb;
            width: 80%;
            border-radius: 15px;
            margin: 0 auto;
            max-width: 600px;
        }

        .container {
            padding: 2px 16px;
            background-color: #f1f1f1
        }

        .promo {
            background-color: #ccc;
            font-weight: 900;
            padding: 3px
        }

        a,
        .expire {
            color: red;
            text-align: center
        }

        p.code {
            font-size: 20px;
            text-align: center
        }

        p {
            line-height: 1.5;
            text-align: justify
        }

        h2.note {
            text-align: center;
            font-size: large;
            text-decoration: underline
        }
    </style>
</head>

<body>
    <div class="coupon">
        <div class="container">
            <h4 style="text-align: center">Coupon's code form shop <a style="color: blue" target="_blank"
                    href="{{ route('asbab.home') }}">www.asbab.dev.com</a></h4>
        </div>
        <div class="container" style="background-color: #fff">
            <h2 class="note">
                @switch($coupon->typr)
                    @case(0)
                        {{ 'Sale off $' . number_format($coupon->discount, 2, '.', ',') }}
                    @break
                    @case(1)
                        {{ 'Sale off ' . $coupon->discount . '%' }}
                    @break
                @endswitch
                for total payment orders.
            </h2>
            <p>You have purchased at the shop <a target="_blank" href="{{ route('asbab.home') }}">www.asbab.dev.com</a>!
                If you already have an account, please <a target="_blank" href="{{ route('asbab.home').'#login_account' }}">log in</a> to your account to purchase and enter the code below to
                receive a discount. Thank you! Wish you a lot of health and peace in life.</p>
        </div>
        <div class="container">
            <p class="code">Use code: <span class="promo">{{ $coupon->code }}</span> only 100 tickets</p>
            <p class="expire">Expiration date: {{ date('d/m/Y', strtotime($coupon->time_out_of)) }}</p>
        </div>
    </div>
</body>

</html>
