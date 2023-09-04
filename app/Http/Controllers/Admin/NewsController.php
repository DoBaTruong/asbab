<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\News;
use App\Traits\StorageImageTrait;
use App\Traits\EditorUploadImage;
use DataTables;
use File;
use Storage;
use Log;
use DB;
use Str;

class NewsController extends Controller
{
    use StorageImageTrait;
    use EditorUploadImage;

    public function index($permission)
    {
        $news = News::get();
        foreach ($news as $n) {
            $n->auth_permission = $permission;
        }
        if ($permission != 0) {
            return DataTables::of($news)
                ->editColumn('image_path', function ($new) {
                    return '<img src="'.asset($new->image_path).'" />';
                })
                ->addColumn('action', function ($new) {
                    switch ($new->auth_permission) {
                        case '1':
                            $action = '<a href="'.route('admin.news.edit', ['id' => $new->id]).'" class="btn btn-info">Edit</a>
                                        <a data-href="'.route('admin.news.delete', ['id' => $new->id]).'" class="btn btn-danger action-delete">Delete</a>';
                            break;
                        case '2':
                            $action = $action = '<a href="'.route('admin.news.edit', ['id' => $new->id]).'" class="btn btn-info">Edit</a>';
                            break;
                        case '3':
                            $action = '<a data-href="'.route('admin.news.delete', ['id' => $new->id]).'" class="btn btn-danger action-delete">Delete</a>';
                            break;
                    }
                    return $action;
                })
                ->rawColumns(['image_path', 'action'])
                ->make(true);
        } else {
            return DataTables::of($news)
                ->editColumn('image_path', function ($new) {
                    return '<img src="'.asset($new->image_path).'" />';
                })
                ->rawColumns(['image_path'])
                ->make(true);
        }
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['bail','required','unique:news,title','max:255'],
            'abstract' => 'required',
            'image_path' => 'bail|required|image|mimes:jpg,jpeg,png,gif|max:102400',
            'details' => 'required',
            'authors' => 'required'
        ]);

        try {
            DB::beginTransaction();
            $details = $this->SaveUploadEditorImage($request, 'news');

            $dataImageUpload = $this->storageUploadImageTrait($request, 'image_path', "news");
            $dataNew = [
                'title' =>  $request->name,
                'abstract' => $request->abstract,
                'details' => $details,
                'slug' => Str::slug($request->name),
                'user_id' => auth()->id(),
                'authors' => $request->authors,
                'image_path' => $dataImageUpload['file_path'],
                'image_name' => $dataImageUpload['file_name']
            ];

            $new = News::create($dataNew);
            DB::commit();
            return redirect()->route('admin.news.index');
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Message: '.$exception->getMessage().' line: '.$exception->getLine());
            return back();
        }
    }

    public function edit($id)
    {
        $new = News::find($id);
        return view('admin.news.edit', compact('new'));
    }

    public function update(Request $request, $id)
    {
        $new = News::find($id);
        $request->validate([
            'name' => ['bail','required','unique:news,title,'.$new->slug.',slug','max:255'],
            'abstract' => 'required',
            'image_path' => 'bail|image|mimes:jpg,jpeg,png,gif|max:102400',
            'details' => 'required',
            'authors' => 'required'
        ]);
        try {
            DB::beginTransaction();
            $uploadDir = public_path('images/upload/news/'.$new->slug);
            if (File::isDirectory($uploadDir)) {
                File::deleteDirectory($uploadDir);
            }
            $details = $this->SaveUploadEditorImage($request, 'news');
            $dataImageUploadUpdate = $this->storageUploadImageTrait($request, 'image_path', "news");
            $dataNewUpdate = [
                'title' =>  $request->name,
                'abstract' => $request->abstract,
                'slug' => Str::slug($request->name),
                'user_id' => auth()->id(),
                'details' => $details,
                'authors' => $request->authors
            ];
            if (!empty($dataImageUploadUpdate)) {
                if(file_exists(public_path($new->image_path))) {
                    unlink(public_path($new->image_path));
                }
                $dataNewUpdate['image_path'] = $dataImageUploadUpdate['file_path'];
                $dataNewUpdate['image_name'] = $dataImageUploadUpdate['file_name'];
            }
            $new->update($dataNewUpdate);
            DB::commit();
            return redirect()->route('admin.news.index');
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Message: '.$exception->getMessage().' line: '.$exception->getLine());
            return back();
        }
    }

    public function destroy($id)
    {
        $new = News::find($id);
        $uploadDir = 'images/upload/news/'.$new->slug;
        Storage::disk('public')->deleteDirectory($uploadDir);
        if(file_exists(public_path($new->image_path))) {
            unlink(public_path($new->image_path));
        }
        $new->delete();
        return response()->json($new);
    }
}
