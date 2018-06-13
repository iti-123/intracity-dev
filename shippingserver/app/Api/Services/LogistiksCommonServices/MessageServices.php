<?php

namespace Api\Services\LogistiksCommonServices;

use Api\Model\Message;
use Api\Services\BlueCollar\BaseServiceProvider;
use Api\Utils\CommonComponents;
use Log;
use Storage;
use Tymon\JWTAuth\Facades\JWTAuth;


class MessageServices extends BaseServiceProvider
{
    public static function send($data)
    {
        $message = new Message();
        Log::info("Message communication start");

        $bo = json_decode($data->getContent());

        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $message->lkp_service_id = $bo->lkp_service_id;
        $message->sender_id = $bo->message_from;
        $message->recepient_id = $bo->message_to;
        //dd($message->recepient_id);
        // $message->post_id=$bo->postId;
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
                'lkp_service_id' => $request->serviceId,
                'recepient_id' => $request->user_id,
            ])
            // ->orWhere('sender_id',$request->user_id)
            ->join('users', function ($join) {
                $join->on('users.id', '=', 'sender_id');
            })
            ->groupBy('sender_id')
            ->get();

        return response()->json([
            "payload" => $message,
            "isSuccessfull" => true,
        ]);
    }

    public static function getMessageBySenderId($senderId)
    {
        $message = \DB::table('user_messages as um')
            ->select("um.*", "senderUser.username as fromName", "receiverUser.username as toName")
            ->where([
                'sender_id' => $senderId,
            ])
            ->join('users as senderUser', function ($join) {
                $join->on('senderUser.id', '=', 'sender_id');
            })
            ->join('users as receiverUser', function ($join) {
                $join->on('receiverUser.id', '=', 'recepient_id');
            })
            ->latest()->get();

        return response()->json([
            "payload" => $message,
            "isSuccessfull" => true,
        ]);
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


}
