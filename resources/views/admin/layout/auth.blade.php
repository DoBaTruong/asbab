<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Asbab - Authentication</title>

    <link rel="stylesheet" href="{{ asset('administrator/assets/bootstrap/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('administrator/assets/font-awesome/css/font-awesome.css') }}" />
    <link rel="stylesheet" href="{{ asset('administrator/common.css') }}" />
    @yield('css')
</head>

<body>
    @yield('content')
    
    <script src="{{ asset('administrator/assets/jquery/jquery-3.5.0.min.js') }}"></script>
    <script src="{{ asset('administrator/assets/bootstrap/bootstrap.min.js') }}"></script>
    <script src="{{ asset('administrator/assets/validate/jquery.validate.min.js') }}"></script>
    @yield('js')
</body>

</html>
