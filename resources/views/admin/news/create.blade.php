@extends('admin.layout.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('administrator/assets/summernote/summernote.css') }}" />
@endsection

@section('js')
    <script src="{{ asset('administrator/assets/summernote/summernote.min.js') }}"></script>
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
                        <li class="breadcumb-item"><a href="{{ route('admin') }}"><i class="fa fa-home"></i> Home</a></li>
                        <li class="active">Add News</li>
                    </ul>
                </div>
            </div>

            <section class="form-admin">
                <form action="{{ route('admin.news.store') }}" method="post" class="row" enctype="multipart/form-data">
                    @csrf
                    <div class="col-lg-9">
                        <div class="form-group">
                            <label>Title:</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                autofocus value="{{ old('name') }}" />
                            @error('name')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Image:</label>
                            <div class="files-view"></div>
                            <div class="file-btn">
                                <input hidden type="file" name="image_path"
                                    class="file-choose @error('image_path') is-invalid @enderror" />
                                <div class="btn btn-info file-choose-alt choose"><span></span>Choose</div>
                                @error('image_path')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Abstract:</label>
                            <textarea name="abstract" rows="5" class="form-control @error('abstract') is-invalid @enderror">{{ old('abstract') }}</textarea>
                            @error('abstract')
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
                        <div class="form-group">
                            <label>Authors:</label>
                            <input type="text" name="authors" class="form-control @error('authors') is-invalid @enderror"
                                autofocus value="{{ old('authors') }}" />
                            @error('authors')
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
