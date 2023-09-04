@extends('admin.layout.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('administrator/assets/sweetalert2/sweetalert2.min.css') }}" />
@endsection

@section('js')
    <script src="{{ asset('administrator/assets/dataTables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('administrator/assets/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('administrator/plugins.js') }}"></script>
    <script src="{{ asset('administrator/common.js') }}"></script>
    <script src="{{ asset('administrator/order/order.js') }}"></script>
@endsection

@section('content')
    <section id="main-content">
        <section class="wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="breadcrumb">
                        <li class="breadcumb-item"><a href="{{ route('admin') }}"><i class="fa fa-home"></i> Home</a>
                        </li>
                        <li class="active">Orders Details</li>
                    </ul>
                </div>
            </div>

            <section class="panel">
                <div class="panel-body">
                    <secction class="adv-table">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th colspan="5" class="text-center text-uppercase bg-success">Buyer Information</th>
                                </tr>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ isset($order->user_id) ? $order->users->name : $order->name }}</td>
                                    <td>{{ isset($order->user_id) ? $order->users->email : $order->mail }}</td>
                                    <td>{{ isset($order->user_id) ? $order->users->phone : $order->phone }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </secction>
                </div>
            </section>

            <section class="panel">
                <div class="panel-body">
                    <secction class="adv-table">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th colspan="6" class="text-center text-uppercase bg-success">Delivery Information</th>
                                </tr>
                                <tr>
                                    <th>Shipper</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Address</th>
                                    <th>Payment method</th>
                                    <th>Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    switch ($order->paymethod) {
                                        case '0':
                                            $pay = 'Paypal';
                                            break;
                                        case '1':
                                            $pay = 'Cash on delivery';
                                            break;
                                        case '2':
                                            $pay = 'Credit card';
                                            break;
                                        case '3':
                                            $pay = 'Direct bank transfer';
                                            break;
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $order->shippers !== null ? $order->shippers->name : 'none' }}</td>
                                    <td>{{ $order->shippers !== null ? $order->shippers->email : 'none' }}</td>
                                    <td>{{ $order->shippers !== null ? $order->shippers->phone : 'none' }}</td>
                                    <td>{{ $order->address }}</td>
                                    <td>{{ $pay }}</td>
                                    <td class="note-delivery">{{ $order->note ? $order->note : 'none' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </secction>
                </div>
            </section>

            <section class="panel">
                <div class="panel-body">
                    <secction class="adv-table">
                        <table class="table table-striped table-middle">
                            <thead>
                                <tr>
                                    <th colspan="5" class="text-center text-uppercase bg-success">Order Information</th>
                                </tr>
                                <tr>
                                    <th class="text-center">STT</th>
                                    <th>Name</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-center">Price</th>
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
                                        <td>{{ $key }}</td>
                                        <td class="text-justify">{{ $bill->product_name }}</td>
                                        <td>{{ $bill->quantity }}</td>
                                        <td>{{ '$' . number_format($bill->product_price, 2, '.', ',') }}</td>
                                        <td>{{ '$' . number_format($total, 2, '.', ',') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            @php
                                $feeship = $order->fee_ship;
                                $tax = $amount * 0.1;
                                if ($order->coupon_id !== null) {
                                    if ($order->coupons->type == 0) {
                                        $discount = $order->coupons->discount;
                                    } else {
                                        $discount = ($amount * $order->coupons->discount) / 100;
                                    }
                                } else {
                                    $discount = 0;
                                }
                                $totalamount = $amount - $discount + $tax + $feeship;
                            @endphp
                            <tfoot>
                                <tr>
                                    <td>Fee ship: <span>${{ number_format($feeship, 2, '.', ',') }}</span></td>
                                    <td>Tax: <span>${{ number_format($tax, 2, '.', ',') }}</span></td>
                                    <td colspan="3" rowspan="2" class="text-right">
                                        <span>Amount: </span><span
                                            class="text-primary ml-10">{{ '$' . number_format($totalamount, 2, '.', ',') }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">Discount:
                                        <span>{{ '- $' . number_format($discount, 2, '.', ',') }}</span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </secction>
                </div>
                @can('print order')
                    @if ($order->status > 1)
                        <div class="flex-center">
                            <a href="{{ route('admin.order.print', ['id' => $order->id]) }}"
                                class="btn btn-info mb-15">Print</a>
                        </div>
                    @endif
                @endcan
            </section>
        </section>
    </section>
@endsection
