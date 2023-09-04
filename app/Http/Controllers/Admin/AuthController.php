<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Category;
use App\Models\Order;
use App\Models\Message;

class AuthController extends Controller
{
    public function index ()
    {
        if(auth()->check()) {
            $orders = Order::where('status','<',4)->get();
            $customers = User::where('type','>',4)->get();
            return view('admin.dashboard',compact('orders','customers'));
        } else {
            return view('admin.login');
        } 
    }

    public function login(Request $request)
    {
        $remember = $request->has('remember_me') ? true : false;
        if (auth()->attempt([
            'email' => $request->email,
            'password' => $request->password
        ], $remember)) {
            return redirect()->to('admin/');
        }

        return back();
    }

    public function logout()
    {
        auth()->logout();
        return redirect()->to('admin');
    }
    
    public function line_chart()
    {
        $orders = Order::orderBy('updated_at', 'asc')->get();
        $periods = [];
        foreach ($orders as $order) {
            $date_order = date('m', strtotime($order->updated_at));
            if(!in_array($date_order, $periods)) {
                $periods[] = $date_order;
            }
        }

        $data = [];
        foreach ($periods as $p) {
            $y = date('Y');
            $my = ($y - 1).'-'.$p;
            $data[] = [
                'period' => $my,
                'Sales '.$y => $orders->whereBetween('updated_at', [$y.'-'.$p, $y.'-'.($p + 1)])->sum('amount'),
                'Sales '.($y - 1) => $orders->whereBetween('updated_at', [($y - 1).'-'.$p, ($y - 1).'-'.($p + 1)])->sum('amount'),
            ];
        }
        return response()->json($data);
    }

    public function donuts_chart()
    {
        $orders = Order::where('updated_at', '>=', date('Y-m'))->get();
        $labels = Category::where('parent_id', 0)->get();
        $total = $orders->sum('amount');
        $data = [];
        foreach ($orders as $order) {
            foreach ($order->bills as $bill) {
                $cat_id = id_parent($bill->images->category_id, $labels, 'parent_id', 'id');
                foreach ($labels as $label) {
                    if($label->id == $cat_id) {
                        $label->amount = $label->amount !== null ? $label->amount + $bill->product_price * $bill->quantity : $bill->product_price * $bill->quantity ;
                    }
                }
            }
        }

        foreach ($labels as $label) {
            $data[] = [
                'label' => $label->name,
                'value' => $label->amount !== null ? number_format($label->amount / $total * 100, 2) : 0
            ];
        }

        return response()->json($data);
    }

   public function notification()
   {
       $userIds = $messages = [];
       foreach (Message::where('read', 0)->where('type', 'client')->where('repfor', 0)->orderBy('created_at', 'desc')->get() as $m) {
           if(!in_array($m->user_id, $userIds)) {
                $userIds[] = $m->user_id;
                $messages[] = $m;
           }
       }
       return response()->json($messages, 200);
   }
}
