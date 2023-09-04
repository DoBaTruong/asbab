<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DataTables;
use Role;
use Permission;
use DB;
use Log;

class RoleController extends Controller
{
    public function index($permission)
    {
        $roles = Role::get();
        foreach ($roles as $r) {
            $r->auth_permission = $permission;
        }
        if ($permission != 0) {
            return DataTables::of($roles)
                ->addColumn('action', function ($role) {
                    if (mb_strtolower($role->name) !== 'quản trị') {
                        switch ($role->auth_permission) {
                            case '1':
                                $action = '<a href="' . route('admin.role.edit', ['id' => $role->id]) . '" class="btn btn-info">Sửa</a>
                                            <a data-href="' . route('admin.role.delete', ['id' => $role->id]) . '" class="btn btn-danger action-delete">Xóa</a>';
                                break;
                            case '2':
                                $action = $action = '<a href="' . route('admin.role.edit', ['id' => $role->id]) . '" class="btn btn-info">Sửa</a>';
                                break;
                            case '3':
                                $action = '<a data-href="' . route('admin.role.delete', ['id' => $role->id]) . '" class="btn btn-danger action-delete">Xóa</a>';
                                break;
                        }
                        return $action;
                    } else {
                        return '';
                    }
                })
                ->rawColumns(['action'])
                ->make(true);
        } else {
            return DataTables::of($roles)
                ->editColumn('description', function ($role) {
                    return '<div class="text-justify">' . $role->description . '</div>';
                })
                ->rawColumns(['description'])
                ->make(true);
        }
    }

    public function create()
    {
        $modules = config('permission.modules');

        return view('admin.role.create', compact('modules'));
    }

    public function store(Request $request)
    {
        $messages = [
            'name.required' => 'Vui lòng nhập tên vai trò.',
            'name.unique' => 'Vai trò này đã tồn tại.',
            'name.max' => 'Tên quá dài.',
            'description.required' => 'Mô tả vai trò không được để trống.'
        ];

        $validator = $request->validate([
            'name' => ['bail', 'required', 'unique:roles', 'max:255'],
            'description' => 'required'
        ], $messages);

        try {
            DB::beginTransaction();
            $role = Role::create([
                'name' => $request->name,
                'description' => $request->description
            ]);

            $role->permissions()->attach($request->permission_id);

            DB::commit();
            return redirect()->route('admin.role.index');
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Message: ' . $exception->getMessage() . ' line: ' . $exception->getLine());
            return back();
        }
    }

    public function edit($id)
    {
        $role = Role::find($id);
        $modules = config('permission.modules');

        return view('admin.role.edit', compact('role', 'modules'));
    }

    public function update(Request $request, $id)
    {
        $role = Role::find($id);

        $messages = [
            'name.required' => 'Vui lòng nhập tên vai trò.',
            'name.unique' => 'Vai trò này đã tồn tại.',
            'name.max' => 'Tên quá dài.',
            'description.required' => 'Mô tả vai trò không được để trống.'
        ];

        $validator = $request->validate([
            'name' => ['bail', 'required', 'unique:roles,name,' . $role->name . ',name', 'max:255'],
            'description' => 'required'
        ], $messages);

        try {
            DB::beginTransaction();
            $role->update([
                'name' => $request->name,
                'description' => $request->description
            ]);
            $role->permissions()->sync($request->permission_id);
            DB::commit();
            return redirect()->route('admin.role.index');
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Message: ' . $exception->getMessage() . ' line: ' . $exception->getLine());
            return back();
        }
    }

    public function destroy($id)
    {
        $role = Role::find($id);
        $role->permissions()->delete();
        $role->delete();
        return response()->json($role);
    }
}
