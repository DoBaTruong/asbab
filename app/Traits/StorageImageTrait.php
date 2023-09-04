<?php
namespace App\Traits;
use Str;

trait StorageImageTrait {
    public function storageUploadImageTrait($request, $fielname, $foldername)
    {
        if($request->hasFile($fielname)) {
            $file = $request->$fielname;
            $basename = $file->getClientOriginalName();
            $filename = Str::random(20).'.'.$file->extension();
            $path = $request->file($fielname)->move(public_path('images/'.$foldername), $filename);
            return [
                'file_name' => $basename,
                'file_path' => 'images/'.$foldername.'/'.$filename
            ];
        } else {
            return null;
        }
    }

    public function storageUploadMultiImageTrait($file, $foldername)
    {
        $basename = $file->getClientOriginalName();
        $filename = Str::random(20).'.'.$file->extension();
        $path = $file->move(public_path('images/'.$foldername), $filename);
        return [
            'file_name' => $basename,
            'file_path' => 'images/'.$foldername.'/'.$filename
        ];
    }
}