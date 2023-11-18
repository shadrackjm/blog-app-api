<?php

namespace App\Http\Controllers;

// use Storage;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests,DispatchesJobs;

    public function saveImage($image, $path = 'public'){
        if (!$image) {
            return null;
        }

         $filename = time().'.png';
        // save image
        \Storage::disk($path)->put($filename, base64_decode($image));
        // return path
        return URL::to('/').'/storage/'.$path.'/'.$filename;
    }
}
