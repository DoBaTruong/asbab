<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\User;
use DataTables;
use Mail;
use Log;
use DB;

class CouponController extends Controller
{
    public function index($permission)
    {
        $coupons = Coupon::get();
        foreach ($coupons as $e) {
            $e->auth_permission = $permission;
        }
        if ($permission != 0) {
            return DataTables::of($coupons)
                ->editColumn('discount', function ($coupon) {
                    return $coupon->type === 0 ? $coupon->discount.'$' : $coupon->discount.'%';
                })
                ->addColumn('action', function ($coupon) {
                    switch ($coupon->auth_permission) {
                        case '1':
                            $action = '<a href="'.route('admin.coupon.edit', ['id' => $coupon->id]).'" class="btn btn-info action-edit">Edit</a>
                                        <a data-href="'.route('admin.coupon.delete', ['id' => $coupon->id]).'" class="btn btn-danger action-delete">Delete</a>
                                        <div class="btn-group btn-send-coupon">
                                            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="true">Send Coupon <span class="caret"></span></button>
                                            <ul class="dropdown-menu pull-right">
                                                <li><a data-href="'.route('admin.coupon.send', ['id' => $coupon->id, 'type' => 5]).'">Normal</a></li>
                                                <li><a data-href="'.route('admin.coupon.send', ['id' => $coupon->id, 'type' => 6]).'">Vips</a></li>
                                            </ul>
                                        </div>';
                            break;
                        case '2':
                            $action = $action = '<a href="'.route('admin.coupon.edit', ['id' => $coupon->id]).'" class="btn btn-info action-edit">Edit</a>';
                            break;
                        case '3':
                            $action = '<a data-href="'.route('admin.coupon.delete', ['id' => $coupon->id]).'" class="btn btn-danger action-delete">Delete</a>';
                            break;
                        case '4':
                            $action = '<div class="btn-group btn-send-coupon">
                                            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="true">Send Coupon <span class="caret"></span></button>
                                            <ul class="dropdown-menu pull-right">
                                                <li><a data-href="'.route('admin.coupon.send', ['id' => $coupon->id, 'type' => 5]).'">Normal</a></li>
                                                <li><a data-href="'.route('admin.coupon.send', ['id' => $coupon->id, 'type' => 6]).'">Vips</a></li>
                                            </ul>
                                        </div>';
                            break;
                    }
                    return $action;
                })
                ->rawColumns(['discount','action'])
                ->make(true);
        } else {
            return DataTables::of($coupons)
                ->make(true);
        }
    }

    public function send($id, $type)
    {
        $coupon = Coupon::find($id);
        $date = date('d.m.Y');
        $title = "Coupon's code of day ".$date;
        $mails = [];
        foreach (User::where('type', $type)->get() as $customer) {
            $mails[] = $customer->email;
        }

        Mail::send('admin.coupon.send', compact('coupon'), function ($message) use ($title, $mails) {
            $message->from('admin.asbabfurniture@gmail.com', 'Asbab Furniture');
            $message->to($mails);
            $message->subject($title);
        });

        return response()->json([
            'message' => 'success',
            'code' => 200
        ], 200);
    }

    public function create()
    {
        return view('admin.coupon.create');
    }

    public function store(Request $request)
    {
        $validator = $request->validate([
            'name' => ['bail','required','max:255'],
            'code' => ['bail','required','unique:coupons'],
            'type' => 'required',
            'time_out_of' => 'required',
            'quantity' => 'bail|required|integer',
            'discount' => ['bail','required','numeric','regex:/^\d*(\.\d{0,2})?$/']
        ]);

        try {
            DB::beginTransaction();
            $coupon = Coupon::create([
                'name' => $request->name,
                'code' => $request->code,
                'type' => $request->type,
                'quantity' => $request->quantity,
                'time_out_of' => $request->time_out_of,
                'discount' => $request->discount
            ]);
    
            DB::commit();
            return redirect()->route('admin.coupon.index');
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Message: '.$exception->getMessage().' line: '.$exception->getLine());
            return back();
        }
    }

    public function edit($id)
    {
        $coupon = Coupon::find($id);
        return view('admin.coupon.edit', compact('coupon'));
    }

    public function update(Request $request, $id)
    {
        $coupon = Coupon::find($id);
        $validator = $request->validate([
            'name' => ['bail','required','max:255'],
            'code' => ['bail','required','unique:coupons,code,'.$coupon->code.',code'],
            'type' => 'required',
            'time_out_of' => 'required',
            'quantity' => 'bail|required|integer',
            'discount' => ['bail','required','numeric','regex:/^\d*(\.\d{0,2})?$/']
        ]);

        try {
            DB::beginTransaction();
            $coupon->update([
                'name' => $request->name,
                'code' => $request->code,
                'type' => $request->type,
                'quantity' => $request->quantity,
                'time_out_of' => $request->time_out_of,
                'discount' => $request->discount
            ]);
            DB::commit();
            return redirect()->route('admin.coupon.index');
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Message: '.$exception->getMessage().' line: '.$exception->getLine());
            return back();
        }
    }

    public function destroy($id)
    {
        $coupon = Coupon::find($id)->delete();
        return response()->json($coupon);
    }
}
