@extends('admin.layout.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('administrator/assets/sweetalert2/sweetalert2.min.css') }}" />
@endsection

@section('js')
    <script src="{{ asset('administrator/assets/dataTables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('administrator/assets/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('administrator/plugins.js') }}"></script>
    <script src="{{ asset('administrator/common.js') }}"></script>
    <script src="{{ asset('administrator/newspaper/newspaper.js') }}"></script>
@endsection

@section('content')
    <section id="main-content">
        <section class="wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="breadcrumb">
                        <li class="breadcumb-item"><a href="{{ route('admin') }}"><i class="fa fa-home"></i> Home</a>
                        </li>
                        <li class="active">News</li>
                    </ul>
                </div>
            </div>

            <section class="panel">
                <div class="panel-body">
                    <div class="adv-table">
                        @can('add news')
                            <div class="flex-end center mb-15">
                                <div class="btn-group text-right">
                                    <a href="{{ route('admin.news.create') }}" class="btn btn-success">
                                        Add New <i class="fa fa-plus"></i>
                                    </a>
                                </div>
                            </div>
                        @endcan
                        <table class="table table-bordered table-striped" id="news-table"
                            @if(auth()->user()->can('edit news') || auth()->user()->can('delete news'))
                                @if (auth()->user()->can('edit news') && auth()->user()->can('delete news'))
                                    data-url="{{ route('admin.news.data', ['permission' => 1]) }}"
                                @elseif(auth()->user()->can('edit news'))
                                    data-url="{{ route('admin.news.data', ['permission' => 2]) }}"
                                @elseif(auth()->user()->can('delete news')) 
                                    data-url="{{ route('admin.news.data', ['permission' => 3]) }}"
                                @endif 
                            @else
                                data-url="{{ route('admin.news.data', ['permission' => 0]) }}"
                            @endif>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Thumb</th>
                                    <th>Abstract</th>
                                    <th>Authors</th>
                                    @if(auth()->user()->can('edit news') || auth()->user()->can('delete news'))
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
