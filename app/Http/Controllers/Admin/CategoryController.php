<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Components\CategoryRecusive;
use DataTables;
use Log;
use DB;
use Str;

class CategoryController extends Controller
{
    public function index($permission)
    {
        $categories = Category::all();
        foreach ($categories as $e) {
            $e->auth_permission = $permission;
        }

        if ($permission != 0) {
            return DataTables::of($categories)
                ->addColumn('action', function ($category) {
                    switch ($category->auth_permission) {
                        case '1':
                            $action = '<a data-href="' . route('admin.category.edit', ['id' => $category->id]) . '" class="btn btn-info action-edit" data-toggle="modal" href="#editCategory">Sửa</a>
                                        <a data-href="' . route('admin.category.delete', ['id' => $category->id]) . '" class="btn btn-danger action-delete">Xóa</a>';
                            break;
                        case '2':
                            $action = $action = '<a data-href="' . route('admin.category.edit', ['id' => $category->id]) . '" class="btn btn-info action-edit" data-toggle="modal" href="#editCategory">Sửa</a>';
                            break;
                        case '3':
                            $action = '<a data-href="' . route('admin.category.delete', ['id' => $category->id]) . '" class="btn btn-danger action-delete">Xóa</a>';
                            break;
                    }
                    return $action;
                })
                ->make(true);
        } else {
            return DataTables::of($categories)
                ->editColumn('name', function ($category) {
                    return '<div class="text-justify">' . $category->name . '</div>';
                })
                ->rawColumns(['name'])
                ->make(true);
        }
    }

    public function create()
    {
        $recusive = new CategoryRecusive(Category::get());
        $htmloptions = $recusive->get_categories_recusive();
        return response()->json($htmloptions);
    }

    public function store(Request $request)
    {
        $messages = [
            'name.required' => 'Tên danh mục không được để trống.',
            'name.unique' => 'Danh mục đã có.',
            'name.max' => 'Tên danh mục quá dài.',
            'parent_id.required' => 'Vui lòng chọn danh mục cha.'
        ];

        $validator = $request->validate([
            'name' => ['bail', 'required', 'unique:categories', 'max:255'],
            'parent_id' => 'required'
        ], $messages);

        try {
            DB::beginTransaction();
            $category = Category::create([
                'name' => $request->name,
                'parent_id' => $request->parent_id,
                'slug' => Str::slug($request->name)
            ]);

            DB::commit();
            return response()->json($category);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Message: ' . $exception->getMessage() . ' line: ' . $exception->getLine());
            return response()->json([
                'message' => 'There are incorrect values in the form !',
                'errors' => $validator->getMessageBag()->toArray()
            ], 422);
        }
    }

    public function edit($id)
    {
        $category = Category::find($id);
        $recusive = new CategoryRecusive(Category::get());
        $htmloptions = $recusive->get_categories_recusive([$category->parent_id]);
        $editUrl = route('admin.category.update', ['id' => $id]);
        return response()->json([
            'category' => $category,
            'htmlOptions' => $htmloptions,
            'editUrl' => $editUrl
        ]);
    }

    public function update(Request $request, $id)
    {
        $messages = [
            'name.required' => 'Tên danh mục không được để trống.',
            'name.unique' => 'Danh mục đã có.',
            'name.max' => 'Tên danh mục quá dài.',
            'parent_id.required' => 'Vui lòng chọn danh mục cha.'
        ];

        $validator = $request->validate([
            'name' => ['bail', 'required', 'unique:categories,name,' . Category::find($id)->name . ',name', 'max:255'],
            'parent_id' => 'required'
        ], $messages);

        try {
            DB::beginTransaction();
            Category::find($id)->update([
                'name' => $request->name,
                'parent_id' => $request->parent_id,
                'slug' => Str::slug($request->name)
            ]);
            $category = Category::find($id);
            DB::commit();
            return response()->json($category);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Message: ' . $exception->getMessage() . ' line: ' . $exception->getLine());
            return response()->json([
                'message' => 'There are incorrect values in the form !',
                'errors' => $validator->getMessageBag()->toArray()
            ], 422);;
        }
    }

    public function destroy($id)
    {
        $category = Category::find($id)->delete();
        return response()->json($category);
    }
}
