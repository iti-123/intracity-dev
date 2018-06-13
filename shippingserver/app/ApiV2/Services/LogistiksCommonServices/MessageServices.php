<?php

namespace ApiV2\Services\LogistiksCommonServices;

use ApiV2\Model\Message;
use ApiV2\Model\CommunityMessage;
use ApiV2\Services\BlueCollar\BaseServiceProvider;
use ApiV2\Utils\CommonComponents;
use Log;
use Storage;
use Tymon\JWTAuth\Facades\JWTAuth;
use DB;

class MessageServices extends BaseServiceProvider
{
    public static function send($data)
    {
        $message = new Message();
        Log::info("Message communication start");

        $bo = json_decode($data->getContent());
        
        
        $checkId = $bo->id == is_int($bo->id) ? $bo->id : decrypt($bo->id);
        
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $message->lkp_service_id = $bo->lkp_service_id;
        $message->sender_id = $bo->message_from;
        $message->recepient_id = $bo->message_to;
        $message->post_item_id = $checkId;
        //dd($message->recepient_id);
         $message->post_id= property_exists($bo, 'postId')? $bo->postId:'';
        // $message->post_item_id=$bo->postItemId;
        // $message->order_id=$bo->orderId;
        // $message->contract_id=$bo->contractId;
        $message->quote_id = $bo->buyer_quote;
        $message->quote_item_id = $bo->buyer_quote_item;
        // $message->enquiry_id=$bo->enquiryId;
        // $message->lead_id=$bo->leadId;
        $message->lkp_message_type_id = 1; //$bo->messageType;
        $message->message_no = self::generateMessageNumber($bo->lkp_service_id);
        $message->subject = $bo->message_subject;
        $message->message = $bo->message_body;
        $message->is_read = 0;
        $message->is_draft = 0;
        $message->is_reminder = 0;
        $message->is_notified = 0;
        $message->is_general = 0;
        $message->is_term = 0;
        // $message->parent_message_id=$bo->parentMessageId;
        // $message->actual_parent_message_id=$bo->actualParentMessageId;

        $now = date('Y-m-d H:i:s');
        $message->created_at = $now;
        $message->updated_at = $now;
        $message->created_by = $userId;
        $message->created_ip = $_SERVER['REMOTE_ADDR'];
        $message->updated_ip = $_SERVER['REMOTE_ADDR'];

        try {
            Log::info("Message successfully send");
            $message->save();
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }

        return $message;

    }

    public static function generateMessageNumber($serviceId)
    {
        $msgid = CommonComponents::getMessageID();
        $created_year = date('Y');
        switch ($serviceId) {
            case FCL :
                $randString = 'FCL/' . $created_year . '/' . str_pad($msgid, 6, "0", STR_PAD_LEFT);
                $message_no = $randString;
                break;
            case LCL :
                $randString = 'LCL/' . $created_year . '/' . str_pad($msgid, 6, "0", STR_PAD_LEFT);
                $message_no = $randString;
                break;
            case AirFreight :
                $randString = 'AirFreight/' . $created_year . '/' . str_pad($msgid, 6, "0", STR_PAD_LEFT);
                $message_no = $randString;
                break;
            case _INTRACITY_ :
                $randString = 'INTRACITY/' . $created_year . '/' . str_pad($msgid, 6, "0", STR_PAD_LEFT);
                $message_no = $randString;
                break;
            case _HYPERLOCAL_ :
                $randString = 'HYPERLOCAL/' . $created_year . '/' . str_pad($msgid, 6, "0", STR_PAD_LEFT);
                $message_no = $randString;
                break;    
            default:
                $randString = 'GENERAL/' . $created_year . '/' . str_pad($msgid, 6, "0", STR_PAD_LEFT);
                $message_no = $randString;
                break;
        }
        return $message_no;
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

    public static function getMessage($request)
    {
        $request = json_decode($request->requestData);
        $message = \DB::table('user_messages as um')
            ->select("um.*", "users.username as fromName")
            ->where([
                // 'lkp_service_id' => $request->serviceId,
                'recepient_id' => $request->user_id,
            ])
            ->orWhere('sender_id',$request->user_id)
            ->join('users', function ($join) {
                $join->on('users.id', '=', 'sender_id');
            })
            //->groupBy('sender_id')
            ->get();

        return response()->json([
            "payload" => $message,
            "isSuccessfull" => true,
        ]);
    }

    public static function getMessageBySenderId($senderId)
    {
        $message = \DB::table('user_messages as um')
            ->select("um.*", "senderUser.username as fromName", "receiverUser.username as toName","uf.filepath")
            ->where([
                'sender_id' => $senderId,
            ])
            ->join('users as senderUser', function ($join) {
                $join->on('senderUser.id', '=', 'sender_id');
            })
            ->join('users as receiverUser', function ($join) {
                $join->on('receiverUser.id', '=', 'recepient_id');
            })
            ->leftjoin('user_message_uploads as uf', function ($join) {
                        $join->on('uf.user_message_id', '=', 'um.id');
            })
            ->latest()->get();

        return response()->json([
            "payload" => $message,
            "isSuccessfull" => true,
        ]);
    }
    
    public static function communityMessage($request)
    {
        $receive = DB::table('users')
                   ->select('id')
                   ->where('username','=',$request->message_to)
                   ->first();
        
        $message = new CommunityMessage();
        $message->send_by = $request->message_from;
        $message->receive_by = $receive->id;
        $message->message_subject = $request->message_subject;
        $message->message = $request->message_body;

        $now = date('Y-m-d H:i:s');
        $message->created_at = $now;
        $message->updated_at = $now;
        $message->created_ip = $_SERVER['REMOTE_ADDR'];
        
        try {
            Log::info("Message successfully send");
            $message->save();
            return response()->json([
                'payload' => $message,
                'isSucessfull' => true
            ]);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    public static function reply($request, $messageId)
    {
        if (isset($messageId) && $messageId != "") {

            $isMessageExist = self::isValidMessage($messageId);

            if (!$isMessageExist) {

                return response()->json(["error" => "Invalid Message"]);

            } else {

                $message = new Message();
                Log::info("Message communication start");

                $bo = json_decode($request->messageObj);

                $userId = JWTAuth::parseToken()->getPayload()->get('id');
                $message->lkp_service_id = $bo->lkp_service_id;

                $message->sender_id = $bo->message_from;
                $message->recepient_id = $bo->message_to;
                $message->parent_message_id = $messageId;

                $message->actual_parent_message_id = self::getParentMessageid($messageId);

                // $message->post_id=$bo->postId;
                // $message->post_item_id=$bo->postItemId;
                // $message->order_id=$bo->orderId;
                // $message->contract_id=$bo->contractId;
                // $message->quote_id=$bo->buyer_quote;
                $message->quote_item_id = $bo->buyer_quote_item;
                // $message->enquiry_id=$bo->enquiryId;
                // $message->lead_id=$bo->leadId;
                $message->lkp_message_type_id = 1; //$bo->messageType;
                $message->message_no = self::generateMessageNumber($bo->lkp_service_id);
                $message->subject = $bo->message_subject;
                $message->message = $bo->message_body;
                $message->is_read = 0;
                $message->is_draft = 0;
                $message->is_reminder = 0;
                $message->is_notified = 0;
                $message->is_general = 0;
                $message->is_term = 0;
                // $message->parent_message_id=$bo->parentMessageId;
                // $message->actual_parent_message_id=$bo->actualParentMessageId;

                $now = date('Y-m-d H:i:s');
                $message->created_at = $now;
                $message->updated_at = $now;
                $message->created_by = $userId;
                $message->created_ip = $_SERVER['REMOTE_ADDR'];
                $message->updated_ip = $_SERVER['REMOTE_ADDR'];

                try {
                    Log::info("Message successfully send");
                    $message->save();
                    return response()->json([
                        'payload' => $message,
                        'isSucessfull' => true
                    ]);
                } catch (Exception $e) {
                    LOG::error($e->getMessage());
                    return $this->errorResponse($e);
                }

            }
        }
    }

    public static function isValidMessage($messageId)
    {
        try {
            $data = \DB::table('user_messages')->where('id', $messageId)->first();
            if (count($data) > 0) {
                return true;
            } else
                return false;

        } catch (Exception $ex) {

        }
    }

    public static function getParentMessageid($messageId)
    {
        try {
            $data = \DB::table('user_messages')->where('id', $messageId)->select('parent_message_id', 'actual_parent_message_id')->first();
            if ($data->parent_message_id != 0) {
                return $data->actual_parent_message_id;
            } else
                return $messageId;
        } catch (Exception $ex) {

        }
    }

    public static function getMessageNotification($request) 
    {
       try {  //user_message_uploads
          $userId = JWTAuth::parseToken()->getPayload()->get('id');
          $data = \DB::table('user_messages as um')

                    ->select('um.id', 'um.subject', 'um.message', 'um.lkp_service_id', 'um.created_at','uf.filepath')
                    ->where('sender_id', $userId)
                    ->leftjoin('user_message_uploads as uf', function ($join) {
                        $join->on('uf.user_message_id', '=', 'um.id');
                    })
                    ->orderBy('um.id', 'DESC')
                    ->limit(3)
                    ->get(); 
                    
           return $data;         
       } catch (Exception $ex) {

       }
    }

    public static function updateMessage($request)
    {
        $id = $request->id;
        try {
           $update = \DB::table('user_messages')
                        ->where('id', $id)
                        ->update(['is_read' => 1]);

            return $update;            
        } catch (Exception $ex) {
            
        }
    }

    public static function messageCount($request)
    {
        try {
            $userId = JWTAuth::parseToken()->getPayload()->get('id');
            $query = \DB::table('user_messages')
                        ->where('is_read', 0)
                        ->where('sender_id', $userId)
                        ->count();
            return $query;            
        } catch (Exception $ex) {

        }
    }


    public static function getOrderMessages($request)
    {
        try {
            $message = \DB::table('user_messages as um')
                ->select(\DB::raw("date_format(um.created_at, '%d-%m-%Y') as created_date"),"um.lkp_service_id","um.post_item_id","um.sender_id","um.created_at", "um.subject", "users.username")
                ->where([
                    'lkp_service_id' => $request['data']['serviceId'],
                    'post_item_id' => $request['data']['routeId'],
                    'recepient_id'=> $request['data']['userId']
                ])
                ->join('users', function ($join) {
                    $join->on('users.id', '=', 'sender_id');
                })
                ->get();

                return $message;
        } catch (Exception $ex) {

        }
    }

}
