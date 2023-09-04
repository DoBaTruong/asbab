<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Bill;
use App\Models\Product;
use App\Traits\StorageImageTrait;
use App\Traits\CommentTrait;
use Storage;
use Log;
use Hash;
use DB;

class AccountController extends Controller
{
    use StorageImageTrait;
    use CommentTrait;

    public function index()
    {
        if(auth()->id()) {
            return view('asbab.account');
        } else {
            return redirect()->to('asbab#login_account');
        }
    }

    public function edit_profile(Request $request)
    {
        $image_path = auth()->user()->profile_photo_path;
        $avaUpload = $this->storageUploadImageTrait($request, 'pf_avata', "avatar");
        $dataProfile = [
            'name' =>  $request->pf_name,
            'email' => $request->pf_mail,
            'phone' => $request->pf_tel,
            'gender' => $request->pf_sex,
            'birdth' => $request->pf_birdth !== null ? $request->pf_birdth : auth()->user()->birdth,
            'address' => $request->pf_add
        ];

        if ($avaUpload !== null) {
            $dataProfile['profile_photo_path'] = $avaUpload['file_path'];
            
            if(file_exists(public_path($image_path))) {
                unlink(public_path($image_path));
            }
        }
        auth()->user()->update($dataProfile);
        return response()->json($dataProfile, 200);
    }

    public function reset_password(Request $request)
    {
        $validator = $request->validate([
            'password' => 'bail|min:6|password',
            'newpass' => 'bail|min:6|required',
            're_newpass' => 'bail|min:6|required_with:newpass|same:newpass'
        ]);
        
        try {
            DB::beginTransaction();
            auth()->user()->update([
                'password' => Hash::make($request->newpass)
            ]);
            DB::commit();
            return response()->json([
                'message' => 'success',
                'code' => 200
            ]);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Message: '.$exception->getMessage().' line: '.$exception->getLine());
            return response()->json([
                'message' => 'There are incorrect values in the form !',
                'errors' => $validator->getMessageBag()->toArray()
            ], 422);
        }
    }

    public function history(Request $request)
    {
        if (auth()->user()) {
            switch ($request->status) {
                case '0':
                    $orders = Order::where('user_id', auth()->id())->get();
                    break;
                case '1':
                    $orders = Order::where('user_id', auth()->id())->where('status', '<=', 2)->get();
                    break;
                case '2':
                    $orders = Order::where('user_id', auth()->id())->where('status', 3)->get();
                    break;
                case '3':
                    $orders = Order::where('user_id', auth()->id())->where('status', 4)->get();
                    break;
                default:
                    $orders = Order::where('user_id', auth()->id())->get();
                    break;
            }
            $pagi = $this->paginate($orders, $request->page, $request->items);
            
            return response()->json([
                'orders' => $orders,
                'baseUrl' => route('asbab.home'),
                'pagination' => $pagi
            ], 200);
        } else {
            return redirect()->to('asbab#login_account');
        }
    }

    public function detail($id)
    {
        $bills = Bill::where('order_id', $id)->get();
        
        return response()->json([
            'bills' => $bills,
            'baseUrl' => route('asbab.home')
        ], 200);
    }

    public function cancel_order(Request $request, $id)
    {
        $order = Order::find($id)->update([
            'status' => 4,
            'reason' => $request->reason
        ]);
        return response()->json($order);
    }

    public function confirm($id)
    {
        Order::find($id)->update([
            'status' => 1
        ]);
        return redirect()->route('asbab.home');
    }
}
