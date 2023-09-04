<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\Product;

class CartController extends Controller
{
    public function index()
    {
        $carts = session()->get('cart');
        $brands = Brand::all();
        return view('asbab.cart', compact('carts','brands'));
    }

    public function addcart(Request $request, $id)
    {
        $quantity = $request->quantity;
        $product = Product::find($id);
        $cart = session()->get('cart');
        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $quantity; 
        } else {
            $cart[$id] = [
                'id' => $id,
                'name' => $product->name,
                'image_path' => $product->feature_image_path,
                'price' => $product->price,
                'quantity' => $quantity
            ];
        }
        session()->put('cart', $cart);

        $carts = [];
        foreach (session()->get('cart') as $c) {
            $carts[] = $c;
        }
        
        return response()->json([
            'carts' => $carts,
            'code' => 200,
            'baseUrl' => route('asbab.home'),
            'message' => 'success'
        ], 200);
    }

    public function removecart($id)
    {
        $cart = session()->get('cart');
        unset($cart[$id]);
        session()->put('cart', $cart);

        $carts = [];
        foreach (session()->get('cart') as $c) {
            $carts[] = $c;
        }
        
        return response()->json([
            'carts' => $carts,
            'code' => 200,
            'baseUrl' => route('asbab.home'),
            'message' => 'success'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $carts = session()->get('cart');
        foreach ($request->data as $d) {
            $carts[$d['id']]['quantity'] = $d['quantity'];
        }
        session()->put('cart', $carts);

        $carts = [];
        foreach (session()->get('cart') as $c) {
            $carts[] = $c;
        }

        return response()->json([
            'carts' => $carts,
            'baseUrl' => route('asbab.home'),
            'coupon' => session()->get('coupon'),
            'fee_ship' =>session()->get('fee_ship'),
            'code' => 200,
            'message' => 'success'
        ], 200);
    }
}
