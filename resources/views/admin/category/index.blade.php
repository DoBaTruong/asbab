@extends('admin.layout.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('administrator/assets/sweetalert2/sweetalert2.min.css') }}"/>
@endsection

@section('js')
    <script src="{{ asset('administrator/assets/dataTables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('administrator/assets/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('administrator/plugins.js') }}"></script>
    <script src="{{ asset('administrator/common.js') }}"></script>
    <script src="{{ asset('administrator/category/category.js') }}"></script>
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
                        <li class="active">Danh mục</li>
                    </ul>
                </div>
            </div>

            <section class="panel">
                <div class="panel-body">
                    <div class="adv-table">
                        @can('thêm danh mục')
                            <div class="flex-end center mb-15">
                                <div class="btn-group text-right">
                                    <a data-url="{{ route('admin.category.create') }}" class="btn btn-success"
                                       id="create-btn-category" data-toggle="modal" href="#createCategory">
                                        Thêm mới <i class="fa fa-plus"></i>
                                    </a>
                                </div>
                            </div>
                        @endcan
                        <table class="table table-bordered table-striped" id="categories-table"
                               @if(auth()->user()->can('sửa danh mục') || auth()->user()->can('xóa danh mục'))
                               @if (auth()->user()->can('sửa danh mục') && auth()->user()->can('xóa danh mục'))
                               data-url="{{ route('admin.category.data', ['permission' => 1]) }}"
                               @elseif(auth()->user()->can('sửa danh mục'))
                               data-url="{{ route('admin.category.data', ['permission' => 2]) }}"
                               @elseif(auth()->user()->can('xóa danh mục'))
                               data-url="{{ route('admin.category.data', ['permission' => 3]) }}"
                               @endif
                               @else
                               data-url="{{ route('admin.category.data', ['permission' => 0]) }}"
                            @endif>
                            <thead>
                            <tr>
                                <th>STT</th>
                                <th>Tên danh mục</th>
                                @if(auth()->user()->can('sửa danh mục') || auth()->user()->can('xóa danh mục'))
                                    <th class="text-center">Hành động</th>
                                @endif
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </section>
            <div class="modal fade full-width-modal-right" id="createCategory" tabindex="-1" role="dialog"
                 aria-labelledby="createCategoryLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content-wrap">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title text-center">Thêm danh mục</h4>
                            </div>
                            <form class="modal-body category-body">
                                @csrf
                                <div class="form-group">
                                    <label>Tên:</label>
                                    <input type="text" name="name" class="form-control"/>
                                </div>
                                <div class="form-group">
                                    <label>Danh mục cha:</label>
                                    <select name="parent_id" class="form-control">
                                        <option value="0">Chọn danh mục cha</option>
                                    </select>
                                </div>
                                <div class="flex-end">
                                    <button data-dismiss="modal" class="btn btn-shadow btn-default"
                                            type="button">Đóng
                                    </button>
                                    <button id="add-btn-category" class="btn btn-shadow btn-success ml-10" type="submit"
                                            data-url="{{ route('admin.category.store') }}">Thêm
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade full-width-modal-right" id="editCategory" tabindex="-1" role="dialog"
                 aria-labelledby="editCategoryLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content-wrap">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title text-center"></h4>
                            </div>
                            <form class="modal-body category-body">
                                @csrf
                                <input hidden type="text" name="url" class="form-control"/>

                                <div class="form-group">
                                    <label>Tên:</label>
                                    <input type="text" name="name" class="form-control"/>
                                </div>
                                <div class="form-group">
                                    <label>Danh mục cha:</label>
                                    <select name="parent_id" class="form-control">
                                        <option value="0">Chọn danh mục cha</option>
                                    </select>
                                </div>
                                <div class="flex-end">
                                    <button data-dismiss="modal" class="btn btn-shadow btn-default"
                                            type="button">Đóng
                                    </button>
                                    <button id="edit-btn-category" class="btn btn-shadow btn-success ml-10"
                                            type="submit">Cập nhật
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </section>
@endsection
