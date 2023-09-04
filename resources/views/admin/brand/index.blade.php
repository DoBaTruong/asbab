@extends('admin.layout.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('administrator/assets/sweetalert2/sweetalert2.min.css') }}" />
@endsection

@section('js')
    <script src="{{ asset('administrator/assets/dataTables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('administrator/assets/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('administrator/plugins.js') }}"></script>
    <script src="{{ asset('administrator/common.js') }}"></script>
    <script src="{{ asset('administrator/brand/brand.js') }}"></script>
@endsection

@section('content')
    <section id="main-content">
        <section class="wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="breadcrumb">
                        <li class="breadcumb-item"><a href="{{ route('admin') }}"><i class="fa fa-home"></i> Home</a></li>
                        <li class="active">Brands</li>
                    </ul>
                </div>
            </div>

            <section class="form-admin">
                @can('add brand')
                    <form id="brand-form" data-action="{{ route('admin.brand.store') }}" method="post"
                        class="row flex-center">
                        @csrf
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Brand:</label>
                                <input type="text" name="name" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label>Links:</label>
                                <input type="text" name="link" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label>Logo:</label>
                                <div class="files-view"></div>
                                <div class="file-btn">
                                    <input hidden type="file" name="image_path" class="file-choose" />
                                    <div class="btn btn-info file-choose-alt choose"><span></span>Choose</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-success text-uppercase" type="submit">Add</button>
                            </div>
                        </div>
                    </form>
                @endcan
            </section>

            <section class="panel">
                <div class="panel-body">
                    <div class="adv-table">
                        <table class="table table-bordered table-striped" id="brands-table"
                        @if(auth()->user()->can('edit brand') || auth()->user()->can('delete brand'))
                            @if (auth()->user()->can('edit brand') && auth()->user()->can('delete brand'))
                                data-url="{{ route('admin.brand.data', ['permission' => 1]) }}"
                            @elseif(auth()->user()->can('edit brand'))
                                data-url="{{ route('admin.brand.data', ['permission' => 2]) }}"
                            @elseif(auth()->user()->can('delete brand')) 
                                data-url="{{ route('admin.brand.data', ['permission' => 3]) }}"
                            @endif
                        @else
                            data-url="{{ route('admin.brand.data', ['permission' => 0]) }}"
                        @endif>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Brand</th>
                                    <th>Logo</th>
                                    <th>Link</th>
                                    @if(auth()->user()->can('edit brand') || auth()->user()->can('delete brand'))
                                        <th class="text-center">Action</th>
                                    @endif
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </section>

            <div class="modal fade full-width-modal-right" id="editBrand" tabindex="-1" role="dialog"
                aria-labelledby="editBrandLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content-wrap">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title text-center"></h4>
                            </div>
                            <form class="modal-body brand-body">
                                @csrf
                                <input hidden type="text" name="url" class="form-control" />

                                <div class="form-group">
                                    <label>Brand:</label>
                                    <input type="text" name="name" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label>Links:</label>
                                    <input type="text" name="link" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label>Logo:</label>
                                    <div class="files-view"></div>
                                    <div class="file-btn">
                                        <input hidden type="file" name="image_path" class="file-choose" />
                                        <div class="btn btn-info file-choose-alt choose"><span></span>Choose</div>
                                    </div>
                                </div>
                                <div class="flex-end">
                                    <button data-dismiss="modal" class="btn btn-shadow btn-default"
                                        type="button">Close</button>
                                    <button id="update-btn-brand" class="btn btn-shadow btn-success ml-10" type="submit">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </section>
@endsection
