<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Slider;
use App\Models\Product;
use App\Models\News;
use App\Models\Brand;
use App\Models\User;
use App\Model\Rating;
use Log;
use DB;

class HomeController extends Controller
{
    public function index()
    {
        $sliders = Slider::all();
        $products = Product::latest()->take(9)->get();
        $news = News::latest()->paginate(3);
        $seller = Product::orderBy('sell', 'DESC')->take('4')->get();
        $good = Product::withCount('rates')->orderBy('rates_count', 'desc')->first();
        return view('asbab.home', compact('sliders','products','news','seller','good'));
    }
    public function login(Request $request)
    {
        $validator = $request->validate([
            'email' => 'bail|required|email',
            'password' => 'bail|required|min:6'
        ]);
        try {
            $user = User::where('email', $request->email)->first();
            if($user) {
                $remember = $request->has('remember_me') ? true : false;
                if (auth()->attempt([
                    'email' => $request->email,
                    'password' => $request->password
                ], $remember)) {
                    return response()->json([
                        'message' => 'success',
                        'code' => 200
                    ], 200);
                }
                
                return response()->json([
                    'message' => 'There are incorrect values in the form !',
                    'errors' => [
                        'password' => 'The password is incorrect.'
                    ]
                ], 422);
            } else {
                return response()->json([
                    'message' => 'There are incorrect values in the form !',
                    'errors' => [
                        'email' => 'The account no exist.'
                    ]
                ], 422);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Message: '.$exception->getMessage().' line: '.$exception->getLine());
            return response()->json([
                'message' => 'There are incorrect values in the form !',
                'errors' => $validator->getMessageBag()->toArray()
            ], 422);
        }
    }

    public function logout()
    {
        auth()->logout();
        return redirect()->route('asbab.home');
    }
}
