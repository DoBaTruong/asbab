@extends('admin.layout.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('administrator/assets/sweetalert2/sweetalert2.min.css') }}"/>
@endsection

@section('js')
    <script src="{{ asset('administrator/assets/dataTables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('administrator/assets/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('administrator/plugins.js') }}"></script>
    <script src="{{ asset('administrator/common.js') }}"></script>
    <script src="{{ asset('administrator/user/customer.js') }}"></script>
@endsection

@section('content')
    <section id="main-content">
        <section class="wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="breadcrumb">
                        <li class="breadcumb-item"><a href="{{ route('admin') }}"><i class="fa fa-home"></i> Trang
                                chủ</a>
                        </li>
                        <li class="active">Khách hàng</li>
                    </ul>
                </div>
            </div>

            <section class="panel">
                <div class="panel-body">
                    <form class="adv-table">
                        @can('sửa khách hàng')
                            <div class="flex-end center mb-15">
                                <div class="btn-group text-right">
                                    <a data-url="{{ route('admin.customer.update', ['type' => 5]) }}"
                                       class="btn btn-success btn-update-vip">
                                        Thường
                                    </a>
                                </div>
                                <div class="btn-group text-right" style="margin-left: 10px">
                                    <a data-url="{{ route('admin.customer.update', ['type' => 6]) }}"
                                       class="btn btn-success btn-update-vip">
                                        VIP
                                    </a>
                                </div>
                            </div>
                        @endcan
                        <table class="table table-bordered table-striped" id="customers-table"
                               @if(auth()->user()->can('sửa khách hàng'))
                               data-url="{{ route('admin.customer.data', ['permission' => 1]) }}"
                               @else
                               data-url="{{ route('admin.customer.data', ['permission' => 0]) }}"
                            @endif>
                            <thead>
                            <tr>
                                @can('sửa khách hàng')
                                    <th class="text-center"><input type="checkbox" name="check" data-toggle="checkall"
                                                                   data-target=".item"/></th>
                                @endcan
                                <th>Họ và tên</th>
                                <th>Điện thoại</th>
                                <th>Đơn hàng</th>
                                <th>Tổng số</th>
                                <th class="text-center">Cấp</th>
                            </tr>
                            </thead>
                        </table>
                    </form>
                </div>
            </section>
        </section>
    </section>
@endsection
