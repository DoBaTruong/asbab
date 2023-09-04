<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\News;
use App\Models\Product;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::where('name', 'LIKE', '%'.$request->keyword.'%')->orWhere('price', 'LIKE', '%'.$request->keyword.'%')->orWhere('details', 'LIKE', '%'.$request->keyword.'%')->orderBy('created_at', 'desc')->get();
        if ($products !== null) {
            foreach ($products as $p) {
                $p->searchtype = 0;
                $p->url = route('asbab.product.show', ['slug' => $p->slug]);
            }
        }
        $news = News::where('title', 'LIKE', '%'.$request->keyword.'%')->orWhere('abstract', 'LIKE', '%'.$request->keyword.'%')->orWhere('details', 'LIKE', '%'.$request->keyword.'%')->orWhere('authors', 'LIKE', '%'.$request->keyword.'%')->orderBy('created_at', 'desc')->get();
        if ($news !== null) {
            foreach ($news as $n) {
                $n->searchtype = 1;
                $n->url = route('asbab.news.details', ['slug' => $n->slug]);
            }
        }
        $data = $products->merge($news)->sort(function ($a, $b) {
            if ($a->created_at == $b->created_at) {
                return 0;
            }

            return $a->created_at < $b->created_at ? 1 : -1;
        });

        return response()->json([
            'data' => $data,
            'keyword' => $request->keyword,
            'searchUrl' => route('asbab.search.index')
        ], 200);
    }
}
