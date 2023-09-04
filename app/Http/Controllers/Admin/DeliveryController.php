<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Delivery;
use Province;
use District;
use Ward;
use DataTables;
use Log;
use DB;

class DeliveryController extends Controller
{
    public function index()
    {
        $deliveries = Delivery::get();
        return DataTables::of($deliveries)
            ->editColumn('province_id', function ($delivery) {
                return Province::find($delivery->province_id)->name;
            })
            ->editColumn('district_id', function ($delivery) {
                return District::find($delivery->district_id)->name;
            })
            ->editColumn('ward_id', function ($delivery) {
                return Ward::find($delivery->ward_id)->name;
            })
            ->editColumn('feeship', function ($delivery) {
                return '<input class="form-control text-center" data-url="'.route('admin.delivery.update', ['id' => $delivery->id]).'" disabled type="text" name="feeship" value="'.$delivery->feeship.'" />';
            })
            ->rawColumns(['feeship','province_id','district_id','ward_id'])
            ->make(true);
    }

    public function provinces()
    {
        $provinces = Province::all();
        return response()->json($provinces);
    }

    public function districts($id)
    {
        $wards = Ward::where('district_id', $id)->get();
        return response()->json($wards);
    }

    public function wards($id)
    {
        $districts = District::where('province_id', $id)->get();
        return response()->json($districts);
    }

    public function store(Request $request)
    {
        $validator = $request->validate([
            'province_id' => 'required',
            'district_id' => 'required',
            'ward_id' => 'required',
            'feeship' => ['bail','required','numeric','regex:/^\d*(\.\d{0,2})?$/']
        ]);

        try {
            DB::beginTransaction();
            $delivery = Delivery::create([
                'province_id' => $request->province_id,
                'district_id' => $request->district_id,
                'ward_id' => $request->ward_id,
                'feeship' => $request->feeship
            ]);
    
            DB::commit();
            return response()->json($delivery);
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
        $delivery = Delivery::find($id);

        $validator = $request->validate([
            'feeship' => ['bail','required','numeric','regex:/^\d*(\.\d{0,2})?$/']
        ]);

        try {
            DB::beginTransaction();
            $delivery->update([
                'feeship' => $request->feeship
            ]);
    
            DB::commit();
            return response()->json($delivery);
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
