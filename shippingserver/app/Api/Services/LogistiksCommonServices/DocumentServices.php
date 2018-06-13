<?php

namespace Api\Services\LogistiksCommonServices;

use Api\Services\BlueCollar\BaseServiceProvider;
use App\UserMessageUpload;
use Storage;

class DocumentServices extends BaseServiceProvider
{
    public static function storeDoc($request, $path)
    {
        $file = $request->uploadFile->file;
        $file = explode(',', $file);
        $image = base64_decode($file[1]);
        $f = finfo_open();
        $result = finfo_buffer($f, $image, FILEINFO_MIME_TYPE);
        $filePath = $path . uniqid();
        if ($result == 'image/jpg' || $result == 'image/jpeg') {
            $filePath .= '.jpg';
        } else if ($result == 'image/png') {
            $filePath .= '.png';
        } else {
            $filePath .= '.pdf';
        }

        $attachment = new UserMessageUpload();
        $attachment->user_message_id = $request->uploadFile->userMessageId;
        $attachment->filepath = $filePath;
        $attachment->name = $filePath;
        $attachment->created_ip = $_SERVER['REMOTE_ADDR'];
        $attachment->save();
        Storage::disk('local')->put($filePath, $image);
        return $attachment;
    }

    public static function getDoc($file, $path)
    {
        $path = $path . $file;
        if (Storage::disk('local')->exists($path)) {
            $attachment = Storage::disk('local')->get($path);
            $type = Storage::disk('local')->mimeType($path);
            return Response::make($attachment, 200)->header("Content-Type", $type);
        } else {
            return Response::json('This file does not exists on our server.');
        }
    }

}
