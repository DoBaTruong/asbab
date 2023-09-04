@extends('admin.layout.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('administrator/assets/summernote/summernote.css') }}" />
    <link rel="stylesheet" href="{{ asset('administrator/assets/select2/select2.min.css') }}" />
@endsection

@section('js')
    <script src="{{ asset('administrator/assets/summernote/summernote.min.js') }}"></script>
    <script src="{{ asset('administrator/assets/select2/select2.min.js') }}"></script>
    <script src="{{ asset('administrator/plugins.js') }}"></script>
    <script src="{{ asset('administrator/common.js') }}"></script>
    <script src="{{ asset('administrator/product/product.js') }}"></script>
@endsection

@section('content')
    <section id="main-content">
        <section class="wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="breadcrumb">
                        <li class="breadcumb-item"><a href="{{ route('admin') }}"><i class="fa fa-home"></i> Home</a></li>
                        <li class="active">Add Product</li>
                    </ul>
                </div>
            </div>

            <section class="form-admin">
                <form action="{{ route('admin.product.store') }}" method="post" class="row product-form" enctype="multipart/form-data">
                    @csrf
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Name:</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                autofocus value="{{ old('name') }}" />
                            @error('name')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Category:</label>
                            <select class="form-control @error('category_id') is-invalid @enderror" id="category_id"
                                name="category_id">
                                <option value="">Select Category</option>
                                {!! $htmloptions !!}
                            </select>
                            @error('category_id')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Tags:</label>
                            <select name="tags[]" class="form-control tags_select_choose @error('tags') is-invalid @enderror"
                                multiple>
                            </select>
                            @error('tags')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Price:</label>
                            <input type="text" name="price" class="form-control @error('price') is-invalid @enderror"
                                placeholder="15000.000" value="{{ old('price') }}" />
                            @error('price')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Quantity:</label>
                            <input type="text" name="quantity" class="form-control @error('quantity') is-invalid @enderror"
                                placeholder="15" value="{{ old('quantity') }}" />
                            @error('quantity')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Description:</label>
                            <textarea name="details" id="content-description" rows="10"
                                class="form-control @error('details') is-invalid @enderror">{{ old('details') }}</textarea>
                            @error('details')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Image:</label>
                            <div class="files-view"></div>
                            <div class="file-btn">
                                <input hidden type="file" name="feature_image_path"
                                    class="file-choose @error('feature_image_path') is-invalid @enderror" />
                                <div class="btn btn-info file-choose-alt choose"><span></span>Choose</div>
                                @error('feature_image_path')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Image Details:</label>
                            <div class="files-view multiview"></div>
                            <div class="file-btn @error('image_path') is-invalid @enderror">
                                <input hidden multiple type="file" name="image_path[]" class="file-choose" />
                                <div class="btn btn-info file-choose-alt choose"><span></span>Choose</div>
                            </div>
                            @error('image_path')
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
