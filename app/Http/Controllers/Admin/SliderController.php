<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Slider;
use App\Traits\StorageImageTrait;
use DataTables;
use Storage;
use Log;
use DB;
use Str;

class SliderController extends Controller
{
    use StorageImageTrait;
    
    public function index($permission)
    {
        $sliders = Slider::get();
        foreach ($sliders as $s) {
            $s->auth_permission = $permission;
        }

        if ($permission != 0) {
            return DataTables::of($sliders)
                ->editColumn('image_path', function ($slider) {
                    return '<img src="'.asset($slider->image_path).'" />';
                })
                ->addColumn('action', function ($slider) {
                    switch ($slider->auth_permission) {
                        case '1':
                            $action = '<a data-href="'.route('admin.slider.edit', ['id' => $slider->id]).'" class="btn btn-info action-edit" data-toggle="modal" href="#editSlider">Edit</a>
                                        <a data-href="'.route('admin.slider.delete', ['id' => $slider->id]).'" class="btn btn-danger action-delete">Delete</a>';
                            break;
                        case '2':
                            $action = $action = '<a data-href="'.route('admin.slider.edit', ['id' => $slider->id]).'" class="btn btn-info action-edit" data-toggle="modal" href="#editSlider">Edit</a>';
                            break;
                        case '3':
                            $action = '<a data-href="'.route('admin.slider.delete', ['id' => $slider->id]).'" class="btn btn-danger action-delete">Delete</a>';
                            break;
                    }
                    return $action;
                })
                ->rawColumns(['image_path', 'action'])
                ->make(true);
        } else {
            return DataTables::of($sliders)
                ->editColumn('description', function ($slider) {
                    return '<div class="text-justify">'.$slider->description.'</div>';
                })
                ->editColumn('image_path', function ($slider) {
                    return '<img src="'.asset($slider->image_path).'" />';
                })
                ->rawColumns(['description','image_path'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $validator = $request->validate([
            'name' => ['bail','required','min:1','max:255'],
            'image_path' => 'bail|required|image|mimes:jpg,jpeg,png,gif',
            'description' => 'required'
        ]);

        try {
            DB::beginTransaction();
            $dataImageUpload = $this->storageUploadImageTrait($request, 'image_path', "slider");

            $slider = Slider::create([
                'name' => $request->name,
                'image_path' => $dataImageUpload['file_path'],
                'image_name' => $dataImageUpload['file_name'],
                'description' => $request->description,
                'slug' => Str::slug($request->name)
            ]);
    
            DB::commit();
            return response()->json($slider);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Message: '.$exception->getMessage().' line: '.$exception->getLine());
            return response()->json([
                'message' => 'There are incorrect values in the form !',
                'errors' => $validator->getMessageBag()->toArray()
            ], 422);
        }
    }

    public function edit($id)
    {
        $slider = Slider::find($id);
        $updateUrl = route('admin.slider.update', ['id' => $id]);
        return response()->json([
            'slider' => $slider,
            'updateUrl' => $updateUrl
        ]);
    }

    public function update(Request $request, $id)
    {
        $slider = Slider::find($id);
        $validator = $request->validate([
            'name' => ['bail','required','min:2','max:255'],
            'description' => 'required',
            'image_path' => 'bail|image|mimes:jpg,jpeg,png,gif|max:102400'
        ]);
        try {
            DB::beginTransaction();

            $dataImageUploadUpdate = $this->storageUploadImageTrait($request, 'image_path', "slider");
            $dataSLiderUpdate = [
                'name' =>  $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description
            ];

            if (!empty($dataImageUploadUpdate)) {
                if(file_exists(public_path($slider->image_path))) {
                    unlink(public_path($slider->image_path));
                }
                $dataSLiderUpdate['image_path'] = $dataImageUploadUpdate['file_path'];
                $dataSLiderUpdate['image_name'] = $dataImageUploadUpdate['file_name'];
            }
            $slider = Slider::find($id);
            $slider->update($dataSLiderUpdate);
            DB::commit();
            return response()->json($slider);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Message: '.$exception->getMessage().' line: '.$exception->getLine());
            return response()->json([
                'message' => 'There are incorrect values in the form !',
                'errors' => $validator->getMessageBag()->toArray()
            ], 422);;
        }
    }

    public function destroy($id)
    {
        $slider = Slider::find($id);
        if(file_exists(public_path($slider->image_path))) {
            unlink(public_path($slider->image_path));
        }
        $slider->delete();
        return response()->json($slider);
    }
}
