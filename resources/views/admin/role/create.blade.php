@extends('admin.layout.app')

@section('js')
    <script src="{{ asset('administrator/plugins.js') }}"></script>
    <script src="{{ asset('administrator/common.js') }}"></script>
    <script src="{{ asset('administrator/role/role.js') }}"></script>
@endsection

@section('content')
    <section id="main-content">
        <section class="wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="breadcrumb">
                        <li class="breadcumb-item"><a href="{{ route('admin') }}"><i class="fa fa-home"></i> Trang chủ</a>
                        </li>
                        <li class="breadcumb-item"><a href="{{ route('admin.role.index') }}">Vai trò</a></li>
                        <li class="active">Thêm mới</li>
                    </ul>
                </div>
            </div>

            <section class="form-admin">
                <form action="{{ route('admin.role.store') }}" method="post" class="row">
                    @csrf
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Vai trò:</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   autofocus value="{{ old('name') }}"/>
                            @error('name')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Mô tả:</label>
                            <textarea name="description" rows="5"
                                      class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                            @error('description')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="top-permission">
                                <label for="checkall">
                                    <input type="checkbox" id="checkall" class="check-all"/>
                                    Tất cả
                                </label>
                            </div>

                            @foreach ($modules as $module => $actions)
                                <div class="panel panel-info permission">
                                    <div class="panel-heading">
                                        <label for="{{ $module }}" class="text-capitalize">
                                            <input type="checkbox" id="{{ $module }}" class="check-parent"/>
                                            {{ $module }}
                                        </label>
                                    </div>
                                    <div class="panel-body flex-between">
                                        @foreach ($actions as $action)
                                            <div class="module-item"
                                                 style="flex: 0 0 {{ 100/count(config('permission.modules.'.$module)) }}%">
                                                <label for="{{ Str::slug($action.' '.$module) }}" class="text-capitalize">
                                                    <input type="checkbox" name="permission_id[]" id="{{ Str::slug($action.' '.$module) }}"
                                                           value="{{ Permission::where('name', $action.' '.$module)->first()->id }}"
                                                           class="check-child"/>
                                                    {{ $action }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="form-group">
                            <button class="btn btn-success text-uppercase" type="submit">Thêm</button>
                        </div>
                    </div>
                </form>
            </section>
        </section>
    </section>
@endsection
