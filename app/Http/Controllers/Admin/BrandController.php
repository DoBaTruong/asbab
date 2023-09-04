<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use App\Traits\StorageImageTrait;
use DataTables;
use Storage;
use Log;
use DB;

class BrandController extends Controller
{
    use StorageImageTrait;

    public function index($permission)
    {
        $brands = Brand::get();
        foreach ($brands as $e) {
            $e->auth_permission = $permission;
        }
        if ($permission != 0) {
            return DataTables::of($brands)
                ->editColumn('image_path', function ($brand) {
                    return '<image src="'.asset($brand->image_path).'" alt="" />';
                })
                ->addColumn('action', function ($brand) {
                    switch ($brand->auth_permission) {
                        case '1':
                            $action = '<a data-href="'.route('admin.brand.edit', ['id' => $brand->id]).'" class="btn btn-info action-edit" data-toggle="modal" href="#editBrand">Edit</a>
                                        <a data-href="'.route('admin.brand.delete', ['id' => $brand->id]).'" class="btn btn-danger action-delete">Delete</a>';
                            break;
                        case '2':
                            $action = $action = '<a data-href="'.route('admin.brand.edit', ['id' => $brand->id]).'" class="btn btn-info action-edit" data-toggle="modal" href="#editBrand">Edit</a>';
                            break;
                        case '3':
                            $action = '<a data-href="'.route('admin.brand.delete', ['id' => $brand->id]).'" class="btn btn-danger action-delete">Delete</a>';
                            break;
                    }
                    return $action;
                })
                ->rawColumns(['image_path','action'])
                ->make(true);
        } else {
            return DataTables::of($brands)
                ->editColumn('link', function ($brand) {
                    return '<div class="text-justify">'.$brand->link.'</div>';
                })
                ->editColumn('image_path', function ($brand) {
                    return '<image src="'.asset($brand->image_path).'" alt="" />';
                })
                ->rawColumns(['link','image_path'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $validator = $request->validate([
            'name' => ['bail','required','unique:brands','max:255'],
            'link' => 'bail|required|url',
            'image_path' => 'bail|required|image|mimes:jpg,jpeg,png,gif'
        ]);

        try {
            DB::beginTransaction();
            $dataImageUpload = $this->storageUploadImageTrait($request, 'image_path', "brand");

            $brand = Brand::create([
                'name' => $request->name,
                'link' => $request->link,
                'image_path' => $dataImageUpload['file_path'],
                'image_name' => $dataImageUpload['file_name']
            ]);
    
            DB::commit();
            return response()->json($brand);
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
        $brand = Brand::find($id);
        $updateUrl = route('admin.brand.update', ['id' => $id]);
        return response()->json([
            'brand' => $brand,
            'url' => $updateUrl
        ]);
    }

    public function update(Request $request, $id)
    {
        $brand = Brand::find($id);
        $validator = $request->validate([
            'name' => ['bail','required','unique:brands,name,'.$brand->name.',name','max:255'],
            'link' => 'bail|required|url',
            'image_path' => 'bail|image|mimes:jpg,jpeg,png,gif'
        ]);

        try {
            DB::beginTransaction();
            $dataImageUpload = $this->storageUploadImageTrait($request, 'image_path', "brand");

            $dataBrandUpdate = [
                'name' => $request->name,
                'link' => $request->link
            ];

            if (!empty($dataImageUpload)) {
                if(file_exists(public_path($brand->image_path))) {
                    unlink(public_path($brand->image_path));
                }
                $dataBrandUpdate['image_path'] = $dataImageUpload['file_path'];
                $dataBrandUpdate['image_name'] = $dataImageUpload['file_name'];
            }

            $brand->update($dataBrandUpdate);
    
            DB::commit();
            return response()->json($brand);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Message: '.$exception->getMessage().' line: '.$exception->getLine());
            return response()->json([
                'message' => 'There are incorrect values in the form !',
                'errors' => $validator->getMessageBag()->toArray()
            ], 422);
        }
    }
    
    public function destroy($id)
    {
        $brand = Brand::find($id);

        if(file_exists(public_path($brand->image_path))) {
            unlink(public_path($brand->image_path));
        }
        $brand->delete();

        return response()->json($brand);
    }
}
