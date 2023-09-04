<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Permission;
use DB;
use Log;

class PermissionController extends Controller
{
    public function create()
    {
        $permissions = config('permission.modules');

        $Arrpers = [];
        foreach (Permission::all() as $per) {
            $nameTmp = explode(' ', $per->name);
            $nameModule = end($nameTmp);
            if (!in_array($nameModule, $Arrpers)) {
                $Arrpers[] = $nameModule;
            }
        }

        $records = Permission::all()->pluck('name')->toArray();
        $modules = [];
        foreach ($permissions as $key => $mod) {
            $modules[$key] = [];
            foreach ($mod as $m) {
                if (!in_array(mb_strtolower($m . ' ' . $key), $records)) {
                    array_push($modules[$key], $m);
                }
            }

            if (count($modules[$key]) == 0) {
                unset($key, $modules);
            }
        }

        if (empty($modules)) {
            $modules = [];
        }

        return view('admin.permission.create')->with('modules', $modules);
    }

    public function get_actions(Request $request)
    {
        $actions = config('permission.modules.' . $request->module);
        $acts = [];
        foreach ($actions as $a) {
            $p = Permission::where('name', $a . ' ' . $request->module)->first();

            if (empty($p)) {
                $acts[] = $a;
            }
        }

        return response()->json($acts);
    }

    public function store(Request $request)
    {
        $validator = $request->validate([
            'name' => 'required'
        ]);

        try {
            DB::beginTransaction();
            foreach ($request->name as $value) {
                Permission::firstOrCreate(['name' => mb_strtolower($value)]);
            }
            DB::commit();
            return redirect()->route('admin.permission.create');
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Message: ' . $exception->getMessage() . ' line: ' . $exception->getLine());
            return back();
        }
    }
}
