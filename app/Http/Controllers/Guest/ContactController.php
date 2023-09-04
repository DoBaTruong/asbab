<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Support;
use App\Traits\EditorUploadImage;
use Storage;
use DB;
use Log;

class ContactController extends Controller
{
    use EditorUploadImage;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('asbab.contact');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function question(Request $request)
    {
        $validator = $request->validate([
            'name' => 'required',
            'email' => 'bail|required|email',
            'subject' => 'required',
            'details' => 'required'
        ]);
        
        try {
            DB::beginTransaction();
            $support = Support::create([
                'name' => $request->name,
                'email' => $request->email,
                'subject' => $request->subject,
                'question' => ''
            ]);
            $details = $this->SaveUploadEditorImage($request, 'support/'.$support->id);
            
            $support->update([
                'question' => $details
            ]);
            DB::commit();
            return response()->json($support, 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Message: '.$exception->getMessage().' line: '.$exception->getLine());
            return response()->json([
                'message' => 'There are incorrect values in the form !',
                'errors' => $validator->getMessageBag()->toArray()
            ], 422);
        }

    }
}
