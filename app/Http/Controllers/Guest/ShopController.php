<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Tag;

class ShopController extends Controller
{
    public function index()
    {
        $products = Product::all();
        $brands = Brand::all();
        $categories = Category::where('parent_id', 0)->orderBy('name', 'asc')->get();
        $tags = Tag::withCount('products')->orderBy('products_count', 'desc')->take(9)->get();
        $seller = Product::orderBy('sell', 'DESC')->take('4')->get();
        return view('asbab.shop', compact('products','brands','categories','tags','seller'));
    }

    public function get_data(Request $request)
    {
        if (trim($request->type) !== '') {
            switch ($request->type) {
                case 'category':
                    $products = Product::where('category_id', $request->data)->orderBy('id', 'desc')->get();
                    break;
                case 'tag':
                    $products = Tag::where('id', $request->data)->first()->products()->orderBy('id', 'desc')->get();
                    break;
                case 'price':
                    $products = Product::whereBetween('price', $request->data)->orderBy('price', 'asc')->get();
                    break;
                default:
                    $products = Product::orderBy('id', 'desc')->get();
            }
        } else {
            switch ($request->data) {
                case '2':
                    $products = Product::withCount('comments')->orderBy('comments_count', 'desc')->get();
                    break;
                case '1':
                    $products = Product::orderBy('name', 'asc')->get();
                    break;
                case '4':
                    $products = Product::orderBy('price', 'asc')->get();
                    break;
                case '3':
                    $products = Product::withCount('rates')->orderBy('rates_count', 'desc')->get();
                    break;
                case '0':
                    $products = Product::orderBy('id', 'desc')->get();
                    break;
                default:
                    $products = Product::orderBy('id', 'desc')->get();
            }
        }
        
        $baseUrl = route('asbab.home');
        return response()->json(['products' => $products, 'baseUrl' => $baseUrl]);
    }
}
