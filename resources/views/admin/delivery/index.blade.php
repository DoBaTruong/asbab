@extends('admin.layout.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('administrator/assets/sweetalert2/sweetalert2.min.css') }}" />
@endsection

@section('js')
    <script src="{{ asset('administrator/assets/dataTables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('administrator/assets/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('administrator/plugins.js') }}"></script>
    <script src="{{ asset('administrator/common.js') }}"></script>
    <script src="{{ asset('administrator/delivery/delivery.js') }}"></script>
@endsection

@section('content')
    <section id="main-content">
        <section class="wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="breadcrumb">
                        <li class="breadcumb-item"><a href="{{ route('admin') }}"><i class="fa fa-home"></i> Home</a>
                        </li>
                        <li class="active">Deliveries</li>
                    </ul>
                </div>
            </div>
            @can('add delivery')
                <section class="form-admin">
                    <form id="delivery-form" data-action="{{ route('admin.delivery.store') }}" method="post"
                        class="row flex-center">
                        @csrf
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Provinces:</label>
                                <select name="province_id" class="form-control"
                                    data-url="{{ route('admin.delivery.provinces') }}">
                                    <option value="">Select Province</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Districts:</label>
                                <select name="district_id" disabled class="form-control" data-url="{{ route('admin') }}">
                                    <option value="">Select District</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Wards:</label>
                                <select name="ward_id" disabled class="form-control">
                                    <option value="">Select Ward</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Fee Ship:</label>
                                <input type="text" name="feeship" class="form-control" value="{{ old('feeship') }}" />
                            </div>
                            <div class="form-group">
                                <button class="btn btn-success text-uppercase" type="submit">Add</button>
                            </div>
                        </div>
                    </form>
                </section>
            @endcan

            <section class="panel">
                <div class="panel-body">
                    <div class="adv-table">
                        <table class="table table-bordered table-striped" id="delivery-table"
                            data-url="{{ route('admin.delivery.data') }}"
                            @can('edit delivery') data-edit="1" @endcan>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Province</th>
                                    <th>District</th>
                                    <th>Ward</th>
                                    <th>FeeShip</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </section>
        </section>
    </section>
@endsection
