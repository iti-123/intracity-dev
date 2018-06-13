<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 07-02-2017
 * Time: 19:37
 */

namespace Api\Controllers\BlueCollar;


use Api\Controllers\BaseController;
use Response;
use Storage;

class DocumentController extends BaseController
{
    public function getDoc($file)
    {
        $path = 'bluecollar/docs/' . $file;
        if (Storage::disk('local')->exists($path)) {
            $attachment = Storage::disk('local')->get($path);
            $type = Storage::disk('local')->mimeType($path);
            return Response::make($attachment, 200)->header("Content-Type", $type);
        } else {
            return Response::json('This file does not exists on our server.');
        }
    }
}
