@extends('admin.layout.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('administrator/assets/select2/select2.min.css') }}"/>
@endsection

@section('js')
    <script src="{{ asset('administrator/assets/select2/select2.min.js') }}"></script>
    <script src="{{ asset('administrator/plugins.js') }}"></script>
    <script src="{{ asset('administrator/common.js') }}"></script>
    <script src="{{ asset('administrator/user/employee.js') }}"></script>
@endsection

@section('content')
    <section id="main-content">
        <section class="wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="breadcrumb">
                        <li class="breadcumb-item"><a href="#"><i class="fa fa-home"></i> Trang chủ</a></li>
                        <li class="active">Thêm nhân viên</li>
                    </ul>
                </div>
            </div>

            <section class="form-admin">
                <form action="{{ route('admin.employee.store') }}" method="post" class="row">
                    @csrf
                    <div class="col-lg-9">
                        <div class="form-group">
                            <label>Họ và tên:</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   autofocus value="{{ old('name') }}"/>
                            @error('name')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Email:</label>
                            <input type="text" name="email" class="form-control @error('email') is-invalid @enderror"
                                   autofocus value="{{ old('email') }}"/>
                            @error('email')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Chức vụ:</label>
                            <select name="type"
                                    class="form-control @error('role_id') is-invalid @enderror">
                                <option value="">Chọn chức vụ</option>
                                <option @if(old('type') == 4) selected @endif value="4">Quản lý</option>
                                <option @if(old('type') == 3) selected @endif value="3">Bán hàng</option>
                                <option @if(old('type') == 2) selected @endif value="2">Bảo vệ</option>
                                <option @if(old('type') == 1) selected @endif value="1">Giao hàng</option>
                            </select>
                            @error('type')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Vai trò:</label>
                            <select name="role_id[]"
                                    class="form-control select2_init @error('role_id') is-invalid @enderror" multiple>
                                <option value=""></option>
                                @foreach ($roles as $role)
                                    <option {{ collect(old('role_id'))->contains($role->id) ? 'selected' : '' }}
                                            value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @error('role_id')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Mật khẩu:</label>
                            <input type="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror" autofocus/>
                            @error('password')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
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
