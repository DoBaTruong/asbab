<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Models\Setting;
use Role;
use PDF;
use DataTables;
use Log;
use DB;
use Str;

class OrderController extends Controller
{
    public function index()
    {
        $shippers = User::where('type', 1)->get();
        return view('admin.order.index', compact('shippers'));
    }

    public function data($show, $update)
    {
        $orders = Order::get();
        foreach ($orders as $o) {
            $o->permission_show = $show;
        }
        if ($update == 0) {
            return DataTables::of($orders)
            ->editColumn('code', function ($order) {
                if ($order->permission_show == 1) {
                    return '<a class="text-uppercase" href="'.route('admin.order.show', ['id' => $order->id]).'">'.$order->code.'</a>';
                } else {
                    return $order->code;
                }
            })
            ->editColumn('amount', function ($order) {
                return '$'.number_format($order->amount, 2, '.', ',');
            })
            ->editColumn('status', function ($order) {
                switch ($order->status) {
                    case '0': $status = '<span class="btn btn-sm btn-danger">Not Confirm</span>'; break;
                    case '1': $status = '<span class="btn btn-sm btn-info">Processing</span>'; break;
                    case '2': $status = '<span class="btn btn-sm btn-primary">Shipping</span>'; break;
                    case '3': $status = '<span class="btn btn-sm btn-success">Delivered</span>'; break;
                    case '4': $status = '<span class="btn btn-sm btn-default">Canceled</span>'; break;
                }
                return $status;
            })
            ->rawColumns(['amount','code','status'])
            ->make(true);
        } else {
            return DataTables::of($orders)
            ->addColumn('check', function ($order) {
                return '<input type="checkbox" name="order_id[]" class="item" value="'.$order->id.'" />';
            })
            ->editColumn('code', function ($order) {
                if ($order->permission_show == 1) {
                    return '<a class="text-uppercase" href="'.route('admin.order.show', ['id' => $order->id]).'">'.$order->code.'</a>';
                } else {
                    return $order->code;
                }
            })
            ->editColumn('amount', function ($order) {
                return '$'.number_format($order->amount, 2, '.', ',');
            })
            ->editColumn('status', function ($order) {
                switch ($order->status) {
                    case '0': $status = '<span class="btn btn-sm btn-danger">Not Confirm</span>'; break;
                    case '1': $status = '<span class="btn btn-sm btn-info">Processing</span>'; break;
                    case '2': $status = '<span class="btn btn-sm btn-primary">Shipping</span>'; break;
                    case '3': $status = '<span class="btn btn-sm btn-success">Delivered</span>'; break;
                    case '4': $status = '<span class="btn btn-sm btn-default">Canceled</span>'; break;
                }
                return $status;
            })
            ->rawColumns(['check','amount','code','status'])
            ->make(true);
        }
    }

    public function show($id)
    {
        $order = Order::find($id);
        return view('admin.order.show', compact('order'));
    }

    public function update(Request $request, $status)
    {
        if (!empty($request->order_id)) {
            foreach ($request->order_id as $id) {
                if($status == 2) {
                    Order::find($id)->update([
                        'status' => $status,
                        'ship_id' => $request->ship_id,
                        'note' => $request->note
                    ]); 
                } else {
                    Order::find($id)->update([
                        'status' => $status
                    ]);
                }
            }
            
            return response()->json([
                'message' => 'success',
                'code' => 200
            ]);
        }
    }

    public function print($id)
    {
        $settings = Setting::all();
        $order = Order::find($id);
        $order->shop_name = $settings->where('config_key', 'shop_name')->first()->config_value;
        $order->shop_address = $settings->where('config_key', 'shop_address')->first()->config_value;
        $order->shop_phone = $settings->where('config_key', 'shop_phone')->first()->config_value;
        $order->shop_logo_path = public_path('guest\images\logo\logo.png');
        $order->bootstrap = public_path('administrator\assets\bootstrap\bootstrap.min.css');
        $pdf = PDF::loadView('admin.order.print', ['order' => $order]);
        return $pdf->stream($order->code.'.pdf');
    }
}
