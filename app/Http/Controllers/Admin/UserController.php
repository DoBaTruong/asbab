<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Avatar;
use DataTables;
use Role;
use Log;
use DB;
use Hash;

class UserController extends Controller
{
    public function employees($permission)
    {
        $employees = User::where('type', '<', 5)->get();
        foreach ($employees as $e) {
            $e->auth_permission = $permission;
        }
        if ($permission != 0) {
            return DataTables::of($employees)
                ->editColumn('type', function ($employee) {
                    switch ($employee->type) {
                        case '1':
                            $type = '<span class="btn btn-small btn-info">Giao hàng</span>';
                            break;
                        case '2':
                            $type = '<span class="btn btn-small btn-default">Bảo vệ</span>';
                            break;
                        case '3':
                            $type = '<span class="btn btn-small btn-success">Bán hàng</span>';
                            break;
                        case '4':
                            $type = '<span class="btn btn-small btn-danger">Quản lý</span>';
                            break;
                        default:
                            $type = '<span class="btn btn-small btn-warning">Chủ cửa hàng</span>';
                    }
                    return $type;
                })
                ->addColumn('action', function ($employee) {
                    switch ($employee->auth_permission) {
                        case '1':
                            $action = '<a href="'.route('admin.employee.edit', ['id' => $employee->id]).'" class="btn btn-info">Sửa</a>
                                        <a data-href="'.route('admin.employee.delete', ['id' => $employee->id]).'" class="btn btn-danger action-delete">Xóa</a>';
                            break;
                        case '2':
                            $action = '<a href="'.route('admin.employee.edit', ['id' => $employee->id]).'" class="btn btn-info">Sửa</a>';
                            break;
                        case '3':
                            $action = '<a data-href="'.route('admin.employee.delete', ['id' => $employee->id]).'" class="btn btn-danger action-delete">Xóa</a>';
                            break;
                    }
                    return $action;
                })
                ->rawColumns(['action','type'])
                ->make(true);
        } else {
            return DataTables::of($employees)
                ->editColumn('email', function ($employee) {
                    return '<div class="text-justify">'.$employee->email.'</div>';
                })
                ->editColumn('type', function ($employee) {
                    switch ($employee->type) {
                        case '1':
                            $type = '<span class="btn btn-small btn-info">Giao hàng</span>';
                            break;
                        case '2':
                            $type = '<span class="btn btn-small btn-dark">Bảo vệ</span>';
                            break;
                        case '3':
                            $type = '<span class="btn btn-small btn-success">Bán hàng</span>';
                            break;
                        case '4':
                            $type = '<span class="btn btn-small btn-danger">Quản lý</span>';
                            break;
                        default:
                            $type = '<span class="btn btn-small btn-warning">Chủ cửa hàng</span>';
                    }
                    return $type;
                })
                ->rawColumns(['email','type'])
                ->make(true);
        }
    }

    public function customers($permission)
    {
        $customers = User::where('type', '>=', 5)->get();
        if ($permission == 1) {
            return DataTables::of($customers)
                ->addColumn('orders', function ($customer) {
                    return $customer->orders->count();
                })
                ->addColumn('amount', function ($customer) {
                    return $customer->orders->sum('amount');
                })
                ->addColumn('level', function ($customer) {
                    $level = $customer->type == 5 ? '<span class="btn btn-primary">Thường</span>' : '<span class="btn btn-danger">VIP</span>';
                    return $level;
                })
                ->addColumn('check', function ($customer) {
                    return '<input type="checkbox" name="user_id[]" class="item" value="'.$customer->id.'" />';
                })
                ->rawColumns(['check','level','orders','amount'])
                ->make(true);
        } else {
            return DataTables::of($customers)
                ->addColumn('orders', function ($customer) {
                    return $customer->orders->count();
                })
                ->addColumn('amount', function ($customer) {
                    return $customer->orders->sum('amount');
                })
                ->addColumn('level', function ($customer) {
                    $level = $customer->type == 5 ? '<span class="btn btn-primary">Thường</span>' : '<span class="btn btn-danger">VIP</span>';
                    return $level;
                })
                ->rawColumns(['level','orders','amount'])
                ->make(true);
        }
    }

    public function create()
    {
        $roles = Role::get();
        return view('admin.user.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $messages = [
            'name.required' => 'Hãy nhập họ tên nhân viên.',
            'name.max' => 'Họ tên quá dài.',
            'email.required' => 'Email không được để trống.',
            'email.unique' => 'Email đã tồn tại.',
            'email.email' => 'Email không hợp lệ.',
            'type.required' => 'Hãy chọn chức vụ cho nhân viên.',
            'role_id.required' => 'Vui lòng chọn vai trò.',
            'role_id.max' => 'Vai trò không được lớn hơn 5.',
            'password.required' => 'Mật khẩu nhân viên không được để trống.',
            'password.min' => 'Mật khẩu không được ít hơn 6 ký tự.',
        ];

        $request->validate([
            'name' => ['bail','required','max:255'],
            'email' => ['bail','required','unique:users','email'],
            'type' => 'required',
            'role_id' => ['bail','required','array','max:5'],
            'password' => 'bail|required|min:6'
        ], $messages);

        try {
            DB::beginTransaction();
            $photo = Avatar::create($request->name)->toBase64();
            $basephoto = base64_decode(explode('base64,', $photo)[1]);
            $extension = explode('/', explode(';base64', $photo)[0])[1];
            $path = 'images/avatar/'.time().'.'.$extension;
            \Storage::disk('public')->put($path, $basephoto);
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'type' => $request->type,
                'password' => Hash::make($request->password),
                'avatar' => $path,
            ]);

            $user->roles()->attach($request->role_id);
            DB::commit();
            return redirect()->route('admin.employee.index');
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Message: '.$exception->getMessage().' line: '.$exception->getLine());
            return back();
        }
    }

    public function edit($id)
    {
        $employee = User::find($id);
        $roles = Role::get();
        return view('admin.user.edit', compact('employee','roles'));
    }

    public function update(Request $request, $id)
    {
        $messages = [
            'name.required' => 'Hãy nhập họ tên nhân viên.',
            'email.required' => 'Email không được để trống.',
            'email.unique' => 'Email đã tồn tại.',
            'email.email' => 'Email không hợp lệ.',
            'type.required' => 'Hãy chọn chức vụ cho nhân viên.',
            'role_id.required' => 'Vui lòng chọn vai trò.',
        ];

        $request->validate([
            'name' => 'required',
            'email' => ['bail','required','email','unique:users,email,'.User::find($id)->email.',email'],
            'type' => 'required',
            'role_id' => 'required'
        ], $messages);

        try {
            DB::beginTransaction();
            $emp = User::find($id);
            if ($emp->name !== $request->name) {
                if(file_exists(public_path($emp->avatar))) {
                    unlink(public_path($emp->avatar));
                }
                $photo = Avatar::create($request->name)->toBase64();
                $basephoto = base64_decode(explode('base64,', $photo)[1]);
                $extension = explode('/', explode(';base64', $photo)[0])[1];
                $path = 'images/avatar/'.time().'.'.$extension;
                \Storage::disk('public')->put($path, $basephoto);
                $emp->update([
                    'avatar' => $path
                ]);
            }

            $emp->update([
                'name' => $request->name,
                'email' => $request->email,
                'type' => $request->type,
                'password' => Hash::make($request->password)
            ]);
            $employee = User::find($id);
            $employee->roles()->sync($request->role_id);
            DB::commit();
            return redirect()->route('admin.employee.index');
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Message: '.$exception->getMessage().' line: '.$exception->getLine());
            return back();
        }
    }

    public function cus_update(Request $request, $type)
    {
        foreach($request->user_id as $id) {
            User::find($id)->update([
                'type' => $type
            ]);
        }
        return response()->json([
            'message' => 'success',
            'code' => 200
        ]);

    }

    public function destroy($id)
    {
        $employee = User::find($id);
        if(file_exists(public_path($employee->avatar))) {
            unlink(public_path($employee->avatar));
        }
        $employee->delete();
        return response()->json($employee);
    }
}
