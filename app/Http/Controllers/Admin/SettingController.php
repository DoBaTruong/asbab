<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use DataTables;
use Log;
use DB;

class SettingController extends Controller
{
    public function index()
    {
        $sets = Setting::get();
        return DataTables::of($sets)
            ->editColumn('config_value', function ($set) {
                return '<input class="form-control" data-url="'.route('admin.setting.update', ['id' => $set->id]).'" disabled type="text" name="config_value" value="'.$set->config_value.'" />';
            })
            ->rawColumns(['config_value'])
            ->make(true); 
    }

    public function store(Request $request)
    {
        $validator = $request->validate([
            'config_key' => 'bail|required|unique:settings',
            'config_value' => 'required'
        ]);

        try {
            DB::beginTransaction();
            $set = Setting::create([
                'config_key' => $request->config_key,
                'config_value' => $request->config_value
            ]);
    
            DB::commit();
            return response()->json($set);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Message: '.$exception->getMessage().' line: '.$exception->getLine());
            return response()->json([
                'message' => 'There are incorrect values in the form !',
                'errors' => $validator->getMessageBag()->toArray()
            ], 422);
        }
    }

    public function update(Request $request, $id)
    {
        $set = Setting::find($id);
        $validator = $request->validate([
            'config_value' => 'required'
        ]);

        try {
            DB::beginTransaction();
            $set->update([
                'config_value' => $request->config_value
            ]);
    
            DB::commit();
            return response()->json($set);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Message: '.$exception->getMessage().' line: '.$exception->getLine());
            return response()->json([
                'message' => 'There are incorrect values in the form !',
                'errors' => $validator->getMessageBag()->toArray()
            ], 422);
        }
    }
}
