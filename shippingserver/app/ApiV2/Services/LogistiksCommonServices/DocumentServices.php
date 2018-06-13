<?php

namespace ApiV2\Services\LogistiksCommonServices;

use ApiV2\Services\BlueCollar\BaseServiceProvider;
use App\UserMessageUpload;
use Storage;
use App\OrderDocument;
use App\ApiV2\Events\OrderEvent;

use Tymon\JWTAuth\Facades\JWTAuth;
use ApiV2\Services\GenerateFiles\GeneratePDF as Generate;

class DocumentServices extends BaseServiceProvider
{
    public static function storeDoc($request, $path)
    { 

        if(isset($request->uploadFile['type']) && !empty($request->uploadFile['type']) && $request->uploadFile['type'] =='OrderDocument') {
           return  static::uploadOrderDocument($request->uploadFile,$path);
        }        

        $file = $request->uploadFile['file'];
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
        $attachment->user_message_id = $request->uploadFile['userMessageId'];
        $attachment->filepath = $filePath;
        $attachment->name = $filePath;
        $attachment->created_by = $userID = JWTAuth::parseToken()->getPayload()->get('id');
        $attachment->created_ip = $_SERVER['REMOTE_ADDR'];
        $attachment->save();
        Storage::disk('local')->put($filePath, $image);
        return $attachment;
    }

    public static function uploadOrderDocument($input,$path) {
        $file =$input['file']['doc'];
        $file = explode(',', $file);
        $image = base64_decode($file[1]);
        $f = finfo_open();
        $result = finfo_buffer($f, $image, FILEINFO_MIME_TYPE);
        $filePath = $path.'order/'. uniqid();
        if ($result == 'image/jpg' || $result == 'image/jpeg') {
            $filePath .= '.jpg';
        } else if ($result == 'image/png') {
            $filePath .= '.png';
        } else {
            $filePath .= '.pdf';
        }
        
        if(Storage::disk('local')->put($filePath, $image)) {
           
            
            return response()->json([
                'isSuccessfull'=>true,
                'filePath'=>$filePath,
                'payload'=>self::storeOrderDocumentToTable($input,$filePath)
            ]);
        }

        return response()->json([
            'isSuccessfull'=>false,
            'filePath'=>$filePath
        ]);
        
    }

    public static function uploadImage($input,$path) {
        $file =$input['picture']['doc'];
        $file = explode(',', $file);
        $image = base64_decode($file[1]);
        $f = finfo_open();
        $result = finfo_buffer($f, $image, FILEINFO_MIME_TYPE);
        $filePath = $path.uniqid();
        if ($result == 'image/jpg' || $result == 'image/jpeg') {
            $filePath .= '.jpg';
        } else if ($result == 'image/png') {
            $filePath .= '.png';
        } else {
            $filePath .= '.pdf';
        }
        
        if(Storage::disk('local')->put($filePath, $image)) {          
            
            return $filePath;
        }

        return false;
        
    }

    public static function storeOrderDocumentToTable($input,$filePath) {
        $orderDocument = new OrderDocument();
        $orderDocument->role = JWTAuth::parseToken()->getPayload()->get('active_role_id');
        $orderDocument->order_id = $input['orderItemId'];
        $orderDocument->attachment = $filePath;
        $orderDocument->title = $input['title'];
        $orderDocument->created_by = $userID = JWTAuth::parseToken()->getPayload()->get('id');
        $orderDocument->created_ip = $_SERVER['REMOTE_ADDR'];

        \DB::transaction(function () use ($orderDocument) {               
            $orderDocument->save();
        });

        return $orderDocument;
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

    public static function fetchOrderDocument($request)
    {
        return response()->json([
            'isSuccessfull'=>true,
            'payload'=>OrderDocument::where('order_id',$request->data)->orderBy('id','DESC')->get()
        ]);
    }

    public static function generateInvoice($request) {

        return (new Generate)->generateInvoicePdf($request);
                
    }


    public static function storecontractDoc($request, $path)
    { 
       
        if(!isset($request['name']))
            {return array('path'=>'','name'=>'');}
        $uploadFile['type']=$request['type'];
        $uploadFile['name']=$request['name'];
        $uploadFile['file']['doc']=$request['doc'];
        return  static::uploadcontractdocument($uploadFile,$path);
      
    }
    public static function storearticleImage($request, $path)
    { 
      
        if(!isset($request['name']))
            {return array('path'=>'','name'=>'');}
        
        $uploadFile['name']=$request['name'];
        $uploadFile['file']['doc']=$request['doc'];
        return  static::uploadcontractdocument($uploadFile,$path);
      
    }

    public static function uploadcontractdocument($input,$path) {
        $file =$input['file']['doc'];
        $file = explode(',', $file);
        $image = base64_decode($file[1]);
        $f = finfo_open();
        $result = finfo_buffer($f, $image, FILEINFO_MIME_TYPE);
        $filePath = $path.''. uniqid();
        if ($result == 'image/jpg' || $result == 'image/jpeg') {
            $filePath .= '.jpg';
        } else if ($result == 'image/png') {
            $filePath .= '.png';
        } else if ($result == 'application/pdf') {
            $filePath .= '.pdf';
        } else if ($result == 'application/msword') {
            $filePath .= '.doc';
        } else if ($result == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
            $filePath .= '.docx';
        }
        Storage::disk('local')->put($filePath, $image);
        return array('path'=>$filePath,'name'=>$input['name']);
     }

    public static function emailOrderDocument($request) {

        try {
            $document = OrderDocument::find($request->data['id']);
            $document->serviceId = $request->data['serviceId'];
            $document->orderNo = $request->data['orderNo'];
            $document->postType = $request->data['postType'];
            $document->postId = $request->data['postId'];
            $document->orderId =$request->data['orderId'];
            event(new OrderEvent($document));
            
            return response()->json([
                'isSuccessfull'=>true,
                'payload'=>$document
            ]); 

        } catch(Exception $e) {

        } 
        
    }

}
