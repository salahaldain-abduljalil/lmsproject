<?php
namespace App\Traits;

use Illuminate\Http\Request;

trait FileUploadTrait{

    function uploadFile(Request $request, string $inputName, ?string $oldPath = null, string $path = '/uploads'){
        if($request->hasFile($inputName)){

            $file = $request->{$inputName}; /// it's mean fetch the input name from the request.
            $ext = $file->getClientOriginalExtension();
            $filename = 'media_'.Uniqid().'.'.$ext;
            $file->move(public_path($path),$filename);
            return $path.'/'.$filename;
        }
       return null; 

    }
}
?>
