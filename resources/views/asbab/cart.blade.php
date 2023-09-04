@extends('asbab.layout.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('guest/assets/owl-carousel/owl.carousel.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('guest/slider/slider.css') }}" />
    <link rel="stylesheet" href="{{ asset('guest/product/product.css') }}" />
@endsection

@section('js')
    <script src="{{ asset('guest/assets/owl-carousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('guest/product/product.js') }}"></script>
    <script src="{{ asset('guest/slider/slider.js') }}"></script>
    <script src="{{ asset('guest/cart/cart.js') }}"></script>
@endsection

@section('content')
    <main>
        <section class="breadcrumb-area">
            <div class="container">
                <div class="row">
                    <nav class="breadcrumb">
                        <a class="breadcrumb-item" href="{{ route('asbab.home') }}">Home</a>
                        <span class="breadcrumb-item active">shopping cart</span>
                    </nav>
                </div>
            </div>
        </section>
        <section class="product-wishlist">
            <div class="container">
                @if (!empty($notify))
                    {!! $notify !!}
                @endif
                @php
                    $total = session()->get('fee_ship') !== null ? session()->get('fee_ship')['fee'] : 0;
                @endphp
                <form method="post">
                    <div class="row">
                        <table class="product-table table-responsive product-cart-list">
                            <thead>
                                <th class="prd-thumbnail">Image</th>
                                <th class="prd-price">Product</th>
                                <th class="prd-qtt text-center">Quantity</th>
                                <th class="prd-price">Total</th>
                                <th class="prd-action">Remove</th>
                            </thead>
                            <tbody>
                                @if ($carts)
                                    @foreach ($carts as $key => $cart)
                                        @php
                                            $total += $cart['price'] * $cart['quantity'];
                                        @endphp
                                        <tr class="cart-product-item">
                                            <td class="prd-thumbnail">
                                                <a href="#">
                                                    <img src="{{ asset($cart['image_path']) }}" alt="Product Image" />
                                                </a>
                                            </td>
                                            <td class="text-center">
                                                <p>{{ $cart['name'] }}</p>
                                                <div class="d-flex justify-content-center">
                                                    <span
                                                        class="old-price mr-4"></span>${{ number_format($cart['price'], 2, '.', ',') }}
                                                </div>
                                            </td>
                                            <td class="prd-qtt text-center">
                                                <div class="d-flex justify-content-center">
                                                    <div class="prd-quantity">
                                                        <span class="qtt-btn qtt-minus"><i
                                                                class="fa fa-minus"></i></span>
                                                        <input type="number" name="prd_qtt[{{ $cart['id'] }}]" min="0"
                                                            step="1" class="show-qtt"
                                                            value="{{ $cart['quantity'] }}" />
                                                        <span class="qtt-btn qtt-plus"><i class="fa fa-plus"></i></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="prd-price">
                                                ${{ number_format($cart['price'] * $cart['quantity'], 2, '.', ',') }}
                                            </td>
                                            <td class="prd-action">
                                                <a data-href="{{ route('asbab.cart.removecart', ['id' => $cart['id']]) }}"
                                                    class="btn-remove-cart"><i class="fa fa-times"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="text-center">No have product on cart !</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                        <div class="product-list-xs product-cart-list d-none">
                            @if ($carts)
                                @foreach ($carts as $key => $cart)
                                    <div class="single-product cart-product-item">
                                        <div class="product-item">
                                            <div class="prd-item-thumb">
                                                <a href="#"><img src="{{ asset($cart['image_path']) }}" alt="" /></a>
                                                <a data-href="{{ route('asbab.cart.removecart', ['id' => $cart['id']]) }}"
                                                    class="btn-remove-cart"><i class="fa fa-times"></i></a>
                                            </div>
                                            <div class="prd-item-infor">
                                                <div class="infor-content">
                                                    <a href="#">{{ $cart['name'] }}</a>
                                                    <div class="infor-rating text-center">
                                                        <span class="stars"><i class="far fa-star"></i><i
                                                                class="far fa-star"></i><i class="far fa-star"></i><i
                                                                class="far fa-star"></i><i
                                                                class="far fa-star"></i></span>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <p class="infor-price"><span
                                                                class="old-price pr-3"></span>${{ number_format($cart['price'] * $cart['quantity'], 2, '.', ',') }}
                                                        </p>
                                                        <div class="prd-quantity">
                                                            <span class="qtt-btn qtt-minus"><i
                                                                    class="fa fa-minus"></i></span>
                                                            <input type="number" name="prd_qtt[{{ $cart['id'] }}]"
                                                                min="0" step="1" class="show-qtt"
                                                                value="{{ $cart['quantity'] }}" />
                                                            <span class="qtt-btn qtt-plus"><i
                                                                    class="fa fa-plus"></i></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-danger">No have product on cart !</div>
                            @endif
                        </div>
                        <div class="product-group-btn">
                            <a href="{{ route('asbab.shop.index') }}" class="fr-btn">CONTINUE SHOPPING</a>
                            <a href="#" data-href="{{ route('asbab.cart.update') }}"
                                class="fr-btn btn-update-cart">UPDATE</a>
                        </div>
                    </div>
                    <div class="row" id="checkout-area">
                        @php
                            $session_fee_ship = session()->get('fee_ship');
                            if ($session_fee_ship) {
                                $address = explode(',', $session_fee_ship['address']);
                                $basic_address = '';
                                $details_address = '';
                                for ($i = 0; $i < 3; $i++) {
                                    if ($i == 2) {
                                        $basic_address .= $address[$i];
                                    } else {
                                        $basic_address .= $address[$i] . ',';
                                    }
                                }
                                for ($j = 3; $j < count($address); $j++) {
                                    if ($address[$j] !== '') {
                                        if ($j == count($address) - 1) {
                                            $details_address .= $address[$j];
                                        } else {
                                            $details_address .= $address[$j] . ',';
                                        }
                                    }
                                }
                            }
                        @endphp
                        <div class="col-lg-6 col-12 p-0 checkout-area-left">
                            @if (auth()->user())
                                <div class="shop-coupon-code mb-3">
                                    <span>Enter your coupon code</span>
                                    <div class="coupon-box d-flex">
                                        <input type="text" name="coupon-code"
                                            value="{{ session()->get('coupon') !== null ? session()->get('coupon')->code : '' }}" />
                                        <div class="shop-cp-btn">
                                            <a href="#" data-href="{{ route('asbab.checkout.coupon') }}"
                                                class="btn-coupon-check">Enter</a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="shop-fee-ship mb-3" data-url="{{ route('asbab.home') }}">
                                <span>Shipping Address</span>
                                <div class="form-group">
                                    <select name="province_id" class="form-control"
                                        data-url="{{ route('admin.delivery.provinces') }}"
                                        data-province="{{ session()->get('fee_ship') !== null ? session()->get('fee_ship')['province_id'] : '' }}">
                                        <option value="">Choose Province</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select name="district_id" class="form-control" disabled
                                        data-url="{{ route('admin') }}">
                                        <option value="">Choose District</option>
                                        @if ($session_fee_ship !== null)
                                            <option selected value="{{ $session_fee_ship['district_id'] }}">
                                                {{ $address[1] }}</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select name="ward_id" class="form-control" disabled>
                                        <option value="">Choose Ward</option>
                                        @if ($session_fee_ship !== null)
                                            <option selected value="{{ $session_fee_ship['ward_id'] }}">
                                                {{ $address[2] }}</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group">
                                    <div class="d-flex">
                                        <input type="text" name="details_address" class="form-control mr-2"
                                            placeholder="Street, Lane, No House"
                                            value="{{ session()->get('fee_ship') !== null ? $details_address : '' }}" />
                                        <input readonly type="text" name="basic_address" class="form-control"
                                            placeholder="Address Customer"
                                            value="{{ session()->get('fee_ship') !== null ? $basic_address : '' }}" />                                        
                                    </div>
                                </div>
                                <a href="#" class="fr-btn btn-cal-fee">Fee</a>
                            </div>
                            <div class="shop-info-customer mb-3">
                                <span>Order Information</span>
                                <div class="form-group">
                                    <input type="text" name="user_id" hidden
                                        value="{{ auth()->user() ? auth()->id() : null }}" />
                                </div>
                                <div class="form-group">
                                    <input type="text" name="customer_name" class="form-control" placeholder="Full name"
                                        value="{{ auth()->user() ? auth()->user()->name : null }}" />
                                </div>
                                <div class="form-group">
                                    <input type="text" name="customer_mail" class="form-control"
                                        placeholder="Email Address"
                                        value="{{ auth()->user() ? auth()->user()->email : null }}" />
                                </div>
                                <div class="form-group">
                                    <input type="text" name="customer_phone" class="form-control" placeholder="Phone No"
                                        value="{{ auth()->user() ? auth()->user()->phone : null }}" />
                                </div>
                                @if (!auth()->user())
                                    <div class="form-group shop-checkbox">
                                        <label>
                                            <input class="create-account" type="checkbox" name="account" value="1" />
                                            Create an account
                                        </label>
                                    </div>
                                    <div class="form-group shop-checkbox">
                                        <label>
                                            <input type="checkbox" name="agreeTerm" value="1" />
                                            Terms and conditions
                                        </label>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-6 col-12 p-0">
                            <div class="shop-cart-total" data->
                                @php
                                    $tax = session()->get('fee_ship') !== null ? ($total - session()->get('fee_ship')['fee']) * 0.1 : $total * 0.1;
                                @endphp
                                <h6>Cart total</h6>
                                <ul class="cart-desk-list">
                                    <li><span class="list-name">Cart total</span><span
                                            class="list-qtt cart-total-price">${{ number_format($total, 2, '.', ',') }}</span>
                                    </li>
                                    <li><span class="list-name">Tax</span><span
                                            class="list-qtt tax-fee-cart">${{ number_format($tax, 2, '.', ',') }}</span>
                                    </li>
                                    <li><span class="list-name">Shipping</span><span
                                            class="list-qtt feeship-cart">${{ session()->get('fee_ship') !== null ? number_format(session()->get('fee_ship')['fee'], 2, '.', ',') : 0 }}</span>
                                    </li>
                                    <li>
                                        <span class="list-name">Order total</span>
                                        <div>
                                            <span
                                                class="list-qtt calc-total-cart">${{ number_format($total + $tax, 2, '.', ',') }}</span>
                                            <span class="coupon-cart">
                                                @if (session()->get('coupon') !== null)
                                                    @if (session()->get('coupon')->type === 0)
                                                        {{ '(- $' . number_format(session()->get('coupon')->discount, 2, '.', ',') . ')' }}
                                                    @else
                                                        {{ '(- $' . number_format((session()->get('coupon')->discount * ($total + $tax)) / 100, 2, '.', ',') . ')' }}
                                                    @endif
                                                @endif
                                            </span>
                                        </div>
                                    </li>
                                </ul>
                                <div class="payment-btn">
                                    <a href="#" class="fr-btn"
                                        data-url="{{ route('asbab.checkout.payment') }}">Place Order</a>
                                    <div class="method-pay">
                                        <div class="form-group">
                                            <label>
                                                <input type="radio" name="paymethod" value="0" />
                                                Paypal
                                                <div id="paypal-marks-container"></div>
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <label>
                                                <input checked type="radio" name="paymethod" value="1" />
                                                Cash on delievery
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <label>
                                                <input type="radio" name="paymethod" value="2" />
                                                Credit card & Direct bank transfer
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
        <section class="slide-brand-area">
            <div class="container">
                <ul class="row owl-carousel owl-brand">
                    @foreach ($brands as $brand)
                        <li class="brand-logo"><a href="{{ $brand->link }}"><img src="{{ asset($brand->image_path) }}"
                                    alt="{{ $brand->name }}" /></a></li>
                    @endforeach
                </ul>
            </div>
        </section>
    </main>
@endsection
