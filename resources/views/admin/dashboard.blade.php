@extends('admin.layout.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('administrator/assets/morris.js-0.4.3/morris.css') }}" />
    <link rel="stylesheet" href="{{ asset('administrator/dashboard/dashboard.css') }}" />
@endsection

@section('js')
    <script src="{{ asset('administrator/assets/morris.js-0.4.3/morris.min.js') }}"></script>
    <script src="{{ asset('administrator/assets/morris.js-0.4.3/raphael-min.js') }}"></script>
    <script src="{{ asset('administrator/plugins.js') }}"></script>
    <script src="{{ asset('administrator/common.js') }}"></script>
    <script src="{{ asset('administrator/dashboard/dashboard.js') }}"></script>
@endsection

@section('content')
    <section id="main-content">
        <section class="wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="breadcrumb">
                        <li class="breadcumb-item"><a href="{{ route('admin') }}"><i class="fa fa-home"></i> Trang chủ</a></li>
                        <li class="active">Tổng hợp</li>
                    </ul>
                </div>
            </div>
            <div class="row state-overview">
                <div class="col-lg-3 col-sm-6">
                    <section class="panel">
                        <div class="symbol terques">
                            <i class="fa fa-user"></i>
                        </div>
                        <div class="value">
                            <h1 class="count">
                                {{ $customers->where('created_at', '>=', date('Y-m'))->count() }}
                            </h1>
                            <p>Khách hàng mới</p>
                        </div>
                    </section>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <section class="panel">
                        <div class="symbol red">
                            <i class="fa fa-tags"></i>
                        </div>
                        <div class="value">
                            <h1 class=" count2">
                                {{ number_format($orders->where('created_at', '>=', date('Y-m'))->sum('amount'), 0, '.', ',') }}
                            </h1>
                            <p>Doanh số</p>
                        </div>
                    </section>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <section class="panel">
                        <div class="symbol yellow">
                            <i class="fa fa-shopping-cart"></i>
                        </div>
                        <div class="value">
                            <h1 class="count3">
                                {{ $orders->where('created_at', '>=', date('Y-m'))->count() }}
                            </h1>
                            <p>Đơn hàng mới</p>
                        </div>
                    </section>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <section class="panel">
                        <div class="symbol blue">
                            <i class="fa fa-pagelines"></i>
                        </div>
                        <div class="value">
                            <h1 class="count4">
                                0
                            </h1>
                            <p>Quảng cáo</p>
                        </div>
                    </section>
                </div>
            </div>

            <div class="row" id="sales-chart">
                <div class="col-lg-6">
                    <section class="panel">
                        <header class="panel-heading bg-primary">
                            Doanh số hàng tháng
                        </header>
                        <div class="panel-body">
                            <div id="sales-month" class="graph"></div>
                        </div>
                    </section>
                </div>
                <div class="col-lg-6">
                    <section class="panel">
                        <header class="panel-heading bg-success">
                            Doanh số theo danh mục
                        </header>
                        <div class="panel-body">
                            <div id="sales-categories" class="graph"></div>
                        </div>
                    </section>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <section class="panel">
                        <header class="panel-heading">
                            <b>Đơn hàng chờ xử lý.</b>
                        </header>
                        <div class="panel-body">
                            <section id="orders-store">
                                <table class="table table-bordered table-striped table-condensed">
                                    <thead>
                                        <tr>
                                            <th>Mã đơn hàng</th>
                                            <th>Khách hàng</th>
                                            <th>Liên hệ</th>
                                            <th class="numeric">Tổng cộng</th>
                                            <th>Ngày đặt</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($orders->where('status', '<=', 1)->take(5) as $order)
                                            <tr>
                                                <td class="text-uppercase">{{ $order->code }}</td>
                                                <td>{{ $order->name }}</td>
                                                <td>{{ $order->phone }}</td>
                                                <td class="numeric">${{ number_format($order->amount, 2, '.', ',') }}</td>
                                                <td>{{ date('d.m.Y', strtotime($order->created_at)) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @can('list order')
                                    <div class="text-right">
                                        <a class="btn btn-primary btn-shadow" href="{{ route('admin.order.index') }}">Chi tiết</a>
                                    </div>
                                @endcan
                            </section>
                        </div>
                    </section>
                </div>
            </div>
        </section>
    </section>
@endsection
