@extends('admin.layout.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('administrator/assets/sweetalert2/sweetalert2.min.css') }}" />
@endsection

@section('js')
    <script src="{{ asset('administrator/assets/dataTables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('administrator/assets/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('administrator/plugins.js') }}"></script>
    <script src="{{ asset('administrator/common.js') }}"></script>
    <script src="{{ asset('administrator/setting/setting.js') }}"></script>
@endsection

@section('content')
    <section id="main-content">
        <section class="wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="breadcrumb">
                        <li class="breadcumb-item"><a href="{{ route('admin') }}"><i class="fa fa-home"></i> Home</a>
                        </li>
                        <li class="active">Setting</li>
                    </ul>
                </div>
            </div>
            @can('add setting')
                <section class="form-admin">
                    <form id="setting-form" data-action="{{ route('admin.setting.store') }}" method="post"
                        class="row flex-center">
                        @csrf
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Config Key:</label>
                                <input type="text" name="config_key" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label>Config Value:</label>
                                <input type="text" name="config_value" class="form-control" />
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
                        <table class="table table-bordered table-striped" id="setting-table"
                            @can('edit setting') data-edit="1" @endcan
                            data-url="{{ route('admin.setting.data') }}">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Config Key</th>
                                    <th>Config Value</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </section>
        </section>
    </section>
@endsection
