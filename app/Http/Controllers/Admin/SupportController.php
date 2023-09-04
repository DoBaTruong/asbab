<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Support;
use App\Models\User;
use DataTables;
use Storage;
use Mail;
use Log;
use DB;

class SupportController extends Controller
{
    public function index($permission)
    {
        $supports = Support::get();
        $incre = 0;
        foreach ($supports as $s) {
            $incre++;
            $s->incre = $incre;
            $s->auth_permission = $permission;
        }
        if ($permission != 0) {
            return DataTables::of($supports)
                ->editColumn('id', function ($support) {
                    return $support->incre;
                })
                ->editColumn('user', function ($support) {
                    return $support->name.'<br>'.$support->email;
                })
                ->addColumn('require', function ($support) {
                    if ($support->status == 0) {
                        $question = '<div class="text-justify">'.$support->question.'</div>';
                    } else {
                        $question = '<div class="text-justify" data-toggle="tooltip" title="'.$support->reply.'">'.$support->question.'</div>';
                    }
                    return $question;
                })
                ->editColumn('status', function ($support) {
                    $status = $support->status == 0 ? '<span class="btn btn-danger">Not approved yet</span>' : '<span data-toggle="tooltip" title="'.User::find($support->status)->name.'-'.User::find($support->status)->email.'" class="btn btn-success">Approved</span>';
                    return $status;
                })
                ->addColumn('access', function ($support) {
                    if($support->status == 0) {
                        switch ($support->auth_permission) {
                            case '1':
                                $access = '<a data-href="'.route('admin.support.reply', ['id' => $support->id]).'" class="btn btn-small btn-primary btn-support-reply">Reply</a>
                                            <a data-href="'.route('admin.support.delete', ['id' => $support->id]).'" class="btn btn-small btn-danger btn-support-delete">Delete</a>';
                                break;
                            case '2':
                                $access = $action = '<a data-href="'.route('admin.support.reply', ['id' => $support->id]).'" class="btn btn-small btn-primary btn-support-reply">Reply</a>';
                                break;
                            case '3':
                                $access = '<a data-href="'.route('admin.support.delete', ['id' => $support->id]).'" class="btn btn-small btn-danger btn-support-delete">Delete</a>';
                                break;
                        }
                    } else {
                        if($support->auth_permission == 1 || $support->auth_permission == 3) {
                            $access = '<a data-href="'.route('admin.support.delete', ['id' => $support->id]).'" class="btn btn-small btn-danger btn-support-delete">Delete</a>';
                        }
                    }
                    return $access;
                })
                ->rawColumns(['id','user','require','status','access'])
                ->make(true);
        } else {
            return DataTables::of($supports)
            ->editColumn('id', function ($support) {
                return $support->incre;
            })
            ->editColumn('user', function ($support) {
                return $support->name.'<br>'.$support->email;
            })
            ->addColumn('require', function ($support) {
                if ($support->status == 0) {
                    $question = '<div class="text-justify">'.$support->question.'</div>';
                } else {
                    $question = '<div class="text-justify" data-toggle="tooltip" title="'.$support->reply.'">'.$support->question.'</div>';
                }
                return $question;
            })
            ->editColumn('status', function ($support) {
                $status = $support->status == 0 ? '<span class="btn btn-danger">Not approved yet</span>' : '<span data-toggle="tooltip" title="'.User::find($support->status)->name.'-'.User::find($support->status)->email.'" class="btn btn-success">Approved</span>';
                return $status;
            })
            ->rawColumns(['id','user','require','status'])
            ->make(true);
        }
    }

    public function store($id, Request $request)
    {
        $validator = $request->validate([
            'reply' => 'required'
        ]);
        try {
            DB::beginTransaction();
            $support = Support::find($id);
            $support->update([
                'status' => auth()->id(),
                'reply' => $request->reply,
            ]);
            $title = 'Respond to requests';
            $cus_mail = $support->email;
            $cus_name = $support->name;
            Mail::html($request->reply, function ($message) use ($title, $cus_mail, $cus_name) {
                $message->from('admin.asbabfurniture@gmail.com', 'Asbab Furniture Shop');
                $message->subject($title);
                $message->to($cus_mail, $cus_name);
            });
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

    public function destroy($id)
    {
        $support = Support::find($id);
        $uploadDir = 'images/upload/support/'.$support->id;
        Storage::disk('public')->deleteDirectory($uploadDir);
        $support->delete();
        return response()->json($support);
    }
}
