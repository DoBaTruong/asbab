@extends('admin.layout.app')

@section('js')
    <script src="{{ asset('administrator/plugins.js') }}"></script>
    <script src="{{ asset('administrator/common.js') }}"></script>
@endsection

@section('content')
    <section id="main-content">
        <section class="wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="breadcrumb">
                        <li class="breadcumb-item"><a href="{{ route('admin') }}"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcumb-item"><a href="{{ route('admin.coupon.index') }}">Coupons</a></li>
                        <li class="active">Add Coupon</li>
                    </ul>
                </div>
            </div>
            <section class="form-admin">
                <form action="{{ route('admin.coupon.store') }}" method="post" class="row">
                    @csrf
                    <div class="col-lg-9">
                        <div class="form-group">
                            <label>Name:</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                autofocus value="{{ old('name') }}" />
                            @error('name')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Code:</label>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                                value="{{ old('code') }}" />
                            @error('code')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Type:</label>
                            <select name="type" class="form-control @error('type') is-invalid @enderror">
                                <option value="">Select type coupon</option>
                                <option {{ old('type') == 0 ? 'selected' : '' }} value="0">Reduce by money</option>
                                <option {{ old('type') == 1 ? 'selected' : '' }} value="1">Decrease in percentage
                                </option>
                            </select>
                            @error('type')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Discount:</label>
                            <input type="text" name="discount" class="form-control @error('discount') is-invalid @enderror"
                                value="{{ old('discount') }}" />
                            @error('discount')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Quantity:</label>
                            <input type="text" name="quantity" class="form-control @error('quantity') is-invalid @enderror"
                                value="{{ old('quantity') }}" />
                            @error('quantity')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Time Out Of:</label>
                            <input type="date" name="time_out_of"
                                class="form-control @error('time_out_of') is-invalid @enderror"
                                value="{{ old('time_out_of') }}" />
                            @error('time_out_of')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <button class="btn btn-success text-uppercase" type="submit">Add</button>
                        </div>
                    </div>
                </form>
            </section>
        </section>
    </section>
@endsection
