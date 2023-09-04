@extends('admin.layout.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('administrator/assets/sweetalert2/sweetalert2.min.css') }}" />
@endsection

@section('js')
    <script src="{{ asset('administrator/assets/dataTables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('administrator/assets/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('administrator/plugins.js') }}"></script>
    <script src="{{ asset('administrator/common.js') }}"></script>
    <script src="{{ asset('administrator/coupon/coupon.js') }}"></script>
@endsection

@section('content')
    <section id="main-content">
        <section class="wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="breadcrumb">
                        <li class="breadcumb-item"><a href="{{ route('admin') }}"><i class="fa fa-home"></i> Home</a></li>
                        <li class="active">Coupons</li>
                    </ul>
                </div>
            </div>

            <section class="panel">
                <div class="panel-body">
                    <div class="adv-table">
                        @can('add coupon')
                            <div class="flex-end center mb-15">
                                <div class="btn-group text-right">
                                    <a class="btn btn-success" href="{{ route('admin.coupon.create') }}">
                                        Add New <i class="fa fa-plus"></i>
                                    </a>
                                </div>
                            </div>
                        @endcan

                        <table class="table table-bordered table-striped" id="coupons-table"
                            @if(auth()->user()->can('edit coupon') || auth()->user()->can('delete coupon') || auth()->user()->can('send coupon'))
                                @if (auth()->user()->can('edit coupon') && auth()->user()->can('delete coupon') && auth()->user()->can('send coupon'))
                                    data-url="{{ route('admin.coupon.data', ['permission' => 1]) }}"
                                @elseif(auth()->user()->can('edit coupon'))
                                    data-url="{{ route('admin.coupon.data', ['permission' => 2]) }}"
                                @elseif(auth()->user()->can('delete coupon')) 
                                    data-url="{{ route('admin.coupon.data', ['permission' => 3]) }}"
                                @elseif(auth()->user()->can('send coupon'))
                                    data-url="{{ route('admin.coupon.data', ['permission' => 4]) }}"
                                @endif 
                            @else
                                data-url="{{ route('admin.coupon.data', ['permission' => 0]) }}"
                            @endif>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th class="text-center">Code</th>
                                    <th class="text-center">Discount</th>
                                    <th class="text-center">Quantity</th>
                                    @if(auth()->user()->can('edit coupon') || auth()->user()->can('delete coupon'))
                                        <th class="text-center">Action</th>
                                    @endif
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </section>
        </section>
    </section>
@endsection
