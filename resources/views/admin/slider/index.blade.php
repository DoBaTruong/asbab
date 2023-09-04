@extends('admin.layout.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('administrator/assets/sweetalert2/sweetalert2.min.css') }}" />
@endsection

@section('js')
    <script src="{{ asset('administrator/assets/dataTables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('administrator/assets/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('administrator/plugins.js') }}"></script>
    <script src="{{ asset('administrator/common.js') }}"></script>
    <script src="{{ asset('administrator/slider/slider.js') }}"></script>
@endsection

@section('content')
    <section id="main-content">
        <section class="wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="breadcrumb">
                        <li class="breadcumb-item"><a href="{{ route('admin') }}"><i class="fa fa-home"></i> Home</a>
                        </li>
                        <li class="active">Sliders</li>
                    </ul>
                </div>
            </div>

            <section class="panel">
                <div class="panel-body">
                    <div class="adv-table">
                        @can('add slider')
                            <div class="flex-end center mb-15">
                                <div class="btn-group text-right">
                                    <a class="btn btn-success" id="create-btn-slider" data-toggle="modal" href="#createSlider">
                                        Add New <i class="fa fa-plus"></i>
                                    </a>
                                </div>
                            </div>
                        @endcan
                        <table class="table table-bordered table-striped" id="sliders-table"
                            @if(auth()->user()->can('edit slider') || auth()->user()->can('delete slider'))
                                @if (auth()->user()->can('edit slider') && auth()->user()->can('delete slider'))
                                    data-url="{{ route('admin.slider.data', ['permission' => 1]) }}"
                                @elseif(auth()->user()->can('edit slider'))
                                    data-url="{{ route('admin.slider.data', ['permission' => 2]) }}"
                                @elseif(auth()->user()->can('delete slider')) 
                                    data-url="{{ route('admin.slider.data', ['permission' => 3]) }}"
                                @endif 
                            @else
                                data-url="{{ route('admin.slider.data', ['permission' => 0]) }}"
                            @endif>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th class="text-center">Image</th>
                                    <th>Description</th>
                                    @if(auth()->user()->can('edit slider') || auth()->user()->can('delete slider'))
                                        <th class="text-center">Action</th>
                                    @endif
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </section>
            <div class="modal fade full-width-modal-right" id="createSlider" tabindex="-1" role="dialog"
                aria-labelledby="createSliderLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content-wrap">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title text-center">Add Slider</h4>
                            </div>
                            <form method="post" class="modal-body slider-body" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label>Name:</label>
                                    <input type="text" name="name" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label>Image:</label>
                                    <div class="files-view"></div>
                                    <div class="file-btn">
                                        <input hidden type="file" name="image_path" class="file-choose" />
                                        <div class="btn btn-info file-choose-alt choose"><span></span>Choose</div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Description:</label>
                                    <textarea name="description" rows="5" class="form-control"></textarea>
                                </div>
                                <div class="flex-end">
                                    <button data-dismiss="modal" class="btn btn-shadow btn-default"
                                        type="button">Close</button>
                                    <button id="add-btn-slider" class="btn btn-shadow btn-success ml-10" type="submit"
                                        data-url="{{ route('admin.slider.store') }}">Add</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade full-width-modal-right" id="editSlider" tabindex="-1" role="dialog"
                aria-labelledby="editSliderLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content-wrap">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title text-center"></h4>
                            </div>
                            <form method="post" class="modal-body slider-body" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label>Name:</label>
                                    <input type="text" name="name" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label>Image:</label>
                                    <div class="files-view">
                                        <span class="view-item"><img src="" alt="" /></span>
                                    </div>
                                    <div class="file-btn">
                                        <input hidden type="file" name="image_path" class="file-choose" />
                                        <div class="btn btn-info file-choose-alt choose"><span></span>Choose</div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Description:</label>
                                    <textarea name="description" rows="5" class="form-control"></textarea>
                                </div>
                                <div class="flex-end">
                                    <button data-dismiss="modal" class="btn btn-shadow btn-default"
                                        type="button">Close</button>
                                    <button id="edit-btn-slider" class="btn btn-shadow btn-success ml-10"
                                        type="submit">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </section>
@endsection
