@extends('admin.layout.auth')

@section('css')
    <link rel="stylesheet" href="{{ asset('administrator/login/login.css') }}" />
@endsection

@section('js')
    <script src="{{ asset('administrator/login/login.js') }}"></script>
@endsection

@section('content')
    <section class="login-wrap d-flex">
        <section class="bg-login">
            <img src="{{ asset('administrator/images/backgrounds/login_page.jpg') }}" alt="" />
        </section>
        <section class="form-login items-center">
            <form method="post" action="{{ route('admin.login') }}">
                @csrf
                <h1>Asbab Furniture</h1>
                <div class="form-group">
                    <input type="text" name="email" placeholder="Địa chỉ email *" />
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Mật khẩu *" />
                </div>
                <div class="form-group flex-between center">
                    <label for="remember"><input type="checkbox" name="remember_me" id="remember" />Ghi nhớ đăng nhập</label>
                    <a href="#">Quên mật khẩu ?</a>
                </div>
                <button class="btn btn-info btn-shadow btn-login" type="submit">ĐĂNG NHẬP</button>
            </form>
        </section>
    </section>
@endsection
