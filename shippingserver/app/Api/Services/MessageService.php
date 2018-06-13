<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 4/5/17
 * Time: 8:34 PM
 */

namespace Api\Services;

use Api\BusinessObjects\MessageBO;
use Api\BusinessObjects\MessageSearchBO;
use Api\Model\Message;
use DB;
use Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class MessageService implements IMessageService
{

    public static function createMessage(MessageBO $bo, $docId)
    {
        $result = "";
        $message = new Message();
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $created_year = date('Y');


        $model = self::bo2model($bo, $message);
        $result = $message->save((array)$model);
        if (isset($docId) && $docId != '') {
            $result = DocumentService::link($docId, 'm', $message->id);

        }

        return $result;
    }

    private static function bo2model(MessageBO $bo, $model)
    {
        $now = date('Y-m-d H:i:s');
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $model->lkp_service_id = $bo->serviceId;
        $model->sender_id = $userId;
        $model->recepient_id = $bo->recepientID;
        //     $model->post_id=$bo->postId;
        //     $model->post_item_id=$bo->postItemId;
        //     $model->order_id=$bo->orderId;
//      $model->contract_id=$bo->contractId;
        //      $model->quote_id=$bo->quoteId;
        //       $model->quote_item_id=$bo->quoteItemId;
        //      $model->enquiry_id=$bo->enquiryId;
        //      $model->lead_id=$bo->leadId;
        $model->lkp_message_type_id = 1; //$bo->messageType;
        $model->message_no = $bo->messageNo;
        $model->subject = $bo->subject;
        $model->message = $bo->message;
        $model->is_read = $bo->isRead;
        $model->is_draft = $bo->isDraft;
        $model->is_reminder = $bo->isReminder;
        $model->is_notified = $bo->isNotified;
        $model->is_general = $bo->isGeneral;
        $model->is_term = $bo->isTerm;
        $model->parent_message_id = $bo->parentMessageId;
        $model->actual_parent_message_id = $bo->actualParentMessageId;

        // dd($bo);
        if (!empty($bo->id)) {
            //    $model->updated_by= $bo->senderId;
            //    $model->updated_ip= $_SERVER['REMOTE_ADDR'];
            //      $model->updated_at= $now;
        } else {
            //   $model->created_by= $bo->senderId;
            //    $model->created_ip= $_SERVER['REMOTE_ADDR'];
            //   $model->created_at= $now;
        }
        $now = date('Y-m-d H:i:s');
        $model->created_at = $now;
        $model->updated_at = $now;
        $model->created_by = $userId;
        $model->created_ip = $_SERVER['REMOTE_ADDR'];
        $model->updated_ip = $_SERVER['REMOTE_ADDR'];
        $model->save();
        return $model;
    }

    public static function postReply(MessageBO $bo)
    {
        $result = "";
        $message = new Message();
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $created_year = date('Y');
        $model = self::bo2model($bo, $message);
        $message->save((array)$model);
        //   $result = self::messages(FCL);
        // LOG::info('Message Saved', (array) $message);
        return $result;
    }

    public static function NotificationMessages()
    {
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $getAllMessages = DB::table('user_messages as um')
            ->leftjoin('lkp_message_types as tmy', 'tmy.id', '=', 'um.lkp_message_type_id')
            ->leftjoin('users as u', 'u.id', '=', 'um.sender_id')
            ->where('um.recepient_id', '=', $userId)
            ->select('um.id',
                'um.subject as message_subject', 'um.message as message_body', 'um.created_at',
                'um.is_read', 'u.username', 'u.id as sender_id')
            //  ->take(3)
            ->get();

        $messagesList = array();
        foreach ($getAllMessages as $val) {
            $messages = new \stdClass();
            $messages->id = $val->id;
            $messages->created_at = $val->created_at;
            $messages->date = date('M d', strtotime($val->created_at));
            $messages->message_subject = $val->message_subject;
            $messages->subject = self::getMessageShortBody($val->message_subject, 15);
            $messages->message_body = $val->message_body;
            $messages->shorttext = self::getMessageShortBody($val->message_body, 15);
            $messages->is_read = $val->is_read;
            $messages->username = $val->username;
            $messages->sender_id = $val->sender_id;

            $messagesList[] = $messages;

        }


        //   dd($messagesList);
        return $messagesList;
    }

    public static function getMessageShortBody($string, $length = 100)
    {
        $string = strip_tags($string);
        if (isset($length) && strlen($string) > $length) {
            $string = substr($string, 0, strrpos(substr($string, 0, $length), ' ')) . " ...";
        } else {
            //  return $string;
        }

        //   dd($string);
        return $string;
    }

    public static function listAllMessages()
    {
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $getAllMessages = DB::table('user_messages as um')
            ->leftjoin('lkp_message_types as tmy', 'tmy.id', '=', 'um.lkp_message_type_id')
            ->leftjoin('users as u', 'u.id', '=', 'um.sender_id')
            ->where('um.sender_id', '!=', $userId)
            ->select('um.id',
                'um.subject', 'um.message', 'um.created_at',
                'um.is_read', 'u.username')
            ->get();
        return $getAllMessages;
    }

    public static function filter(MessageSearchBO $criteria)
    {
        $userId = JWTAuth::parseToken()->getPayload()->get('id');

        $getAllMessagesQuery = DB::table('user_messages as um');
        $getAllMessagesQuery->leftjoin('lkp_message_types as lmy', 'lmy.id', '=', 'um.lkp_message_type_id');
        $getAllMessagesQuery->leftjoin('users as u', 'u.id', '=', 'um.recepient_id');

        $getAllMessagesQuery->where('um.sender_id', $userId);

        if (!empty($criteria->serviceId) && $criteria->serviceId != '0' && $criteria->serviceId != '') {
            $getAllMessagesQuery->where('um.lkp_service_id', $criteria->serviceId);
        }

        if (!empty($criteria->message_type) && $criteria->message_type != '0' && $criteria->message_type != '') {
            //   if ($criteria->message_types == POSTMESSAGETYPE)
            //       $getAllMessagesQuery->whereRaw('(um.lkp_message_type_id =' . POSTQUOTEMESSAGETYPE . ' or `um`.`lkp_message_type_id` = ' . POSTENQURYMESSAGETYPE . ' or `um`.`lkp_message_type_id` = ' . LEADSMESSAGETYPE . ')');
            //   else
            $getAllMessagesQuery->where('um.lkp_message_type_id', $criteria->message_type);
        }


        if (!empty($criteria->message_keywords)) {

            $str = '%' . $criteria->message_keywords . '%';
            $getAllMessagesQuery->where(function ($getAllMessagesQuery) use ($str) {
                $getAllMessagesQuery->where('um.subject', 'LIKE', $str)
                    ->orWhere('um.message', 'LIKE', $str);
            });
        }

        if (isset ($criteria->from_date) && $criteria->from_date != '') {
            $getAllMessagesQuery->where('um.created_at', '>=', CommonComponent::convertDateTimeForDatabase($criteria->from_date, '00:00:00'));

        }
        if (isset ($criteria->to_date) && $criteria->to_date != '') {
            $getAllMessagesQuery->where('um.created_at', '<=', CommonComponent::convertDateTimeForDatabase($criteria->from_date, '23:59:59'));

        }

        $getAllMessagesQuery->select('um.id', 'um.lkp_service_id', 'um.recepient_id', 'um.sender_id', 'um.post_id', 'lmy.message_type',
            'um.order_id', 'um.lkp_message_type_id', 'um.subject', 'um.message', 'um.created_at',
            'um.is_read', 'um.is_reminder', 'um.is_notified', 'um.is_general', 'u.username', 'um.is_term');

        $getAllMessages = $getAllMessagesQuery->get();

        $sql = $getAllMessagesQuery->toSql();
        //  dd($sql);
        //    dd(DB::getQueryLog());
        return ((array)$getAllMessages);

        /*  $userId = JWTAuth::parseToken()->getPayload()->get('id');
          $getAllMessages = DB::table('shp_user_messages as um')
              ->leftjoin('shp_lkp_message_types as tmy', 'tmy.id', '=', 'um.lkp_message_type_id')
              ->leftjoin('users as u', 'u.id', '=', 'um.sender_id')
              ->where('um.recepient_id', '=',$userId)
              ->select('um.id', 'um.lkp_service_id','um.recepient_id','um.sender_id','um.post_id','tmy.message_type',
                  'um.order_id', 'um.lkp_message_type_id', 'um.subject','um.message','um.created_at',
                  'um.is_read', 'um.is_reminder', 'um.is_notified','um.is_general','u.username')
              ->get();
          return $getAllMessages;*/
    }

    public static function getMessage($messageId)
    {
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $ordid = 0;
        $term = 0;
        try {


            if ($messageId != 0) {
                $updatefinal = DB::table('user_messages')
                    ->where('user_messages.id', '=', $messageId)
                    ->where('user_messages.sender_id', '=', $userId)
                    ->update(array('is_read' => 1));
            }

            $getMessagesQuery = DB::table('user_messages as um');
            $getMessagesQuery->leftjoin('lkp_message_types as lmy', 'lmy.id', '=', 'um.lkp_message_type_id');
            //   $getMessagesQuery->leftjoin('user_message_uploads as umu', 'umu.user_message_id', '=', 'um.id');
            $getMessagesQuery->leftjoin('users as u', 'u.id', '=', 'um.sender_id');
            $getMessagesQuery->where('um.id', '=', $messageId);
            /*      $getMessagesQuery->select('um.id', 'um.lkp_service_id', 'um.recepient_id', 'um.sender_id', 'um.post_id', 'lmy.message_type',
                      'um.order_id', 'um.lkp_message_type_id', 'um.subject', 'um.message', 'um.created_at',
                      'um.is_read', 'um.is_reminder', 'um.is_notified', 'um.is_general', 'u.username as from', 'ur.username as to', 'um.message_no', 'umu.filepath', 'umu.name', 'um.actual_parent_message_id');
      */

            $sql = $getMessagesQuery->toSql();
            dd($sql);
            $messageDetails = $getMessagesQuery->get();

            $messageDetails = ((array)$messageDetails);
            LOG::info('response  $messageDetails ==> ', (array)$messageDetails);
            return $messageDetails;


        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }

    public static function getMessageDetails($messageId)
    {
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $ordid = 0;
        $term = 0;
        try {


            if ($messageId != 0) {
                $updatefinal = DB::table('user_messages')
                    ->where('user_messages.id', '=', $messageId)
                    ->where('user_messages.sender_id', '=', $userId)
                    ->update(array('is_read' => 1));
            }
            //    $messageId = 72;
            //   $userId = 100001;
            $getMessagesQuery = DB::table('user_messages as um');
            $getMessagesQuery->leftjoin('lkp_message_types as lmy', 'lmy.id', '=', 'um.lkp_message_type_id');
            $getMessagesQuery->leftjoin('shp_upload_files as umu', function ($join) {
                $join->on('umu.entity_id', '=', 'lmy.id');
                //   ->on("umu.entity","=","\"m\"");
            });
            $getMessagesQuery->leftjoin('users as u', 'u.id', '=', 'um.sender_id');
            $getMessagesQuery->where('um.id', '=', $messageId);
            //   $getMessagesQuery->where('u.id', '=', $userId);
            $getMessagesQuery->select('um.id', 'um.lkp_service_id', 'um.recepient_id', 'um.sender_id', 'um.post_id', 'lmy.message_type',
                'um.order_id', 'um.lkp_message_type_id', 'um.subject', 'um.message', 'um.created_at',
                'um.is_read', 'um.is_reminder', 'um.is_notified', 'um.is_general'
                , 'um.message_no', 'um.actual_parent_message_id');


            $sql = $getMessagesQuery->toSql();

            $messageDetails = $getMessagesQuery->get();

            $messageDetails = ((array)$messageDetails);
            $messageDetails = $messageDetails[0];
            $message_recepient = self::getUserNameById($messageDetails->recepient_id);
            $message_sender = self::getUserNameById($messageDetails->sender_id);


            $messageDetails->recepient = $message_recepient;
            $messageDetails->message_sender = $message_sender;

            $m_thread = self::getThread($messageId);

            if (count($m_thread) > 0) {
                $messageDetails->m_thread = $m_thread;
            } else {

                $messageDetails->m_thread = [];
            }

            // dd($messageDetails);
            return ((array)$messageDetails);
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }

    public static function getUserNameById($id)
    {
        try {
            $post = DB::table('users')->select('username')->orderBy('id', 'desc')->first();
            if (!empty($post)) {
                return $post->username;

            } else
                return '';
        } catch (\Exception $e) {
            //return $e->message;
        }
    }

    public static function getThread($messageId)
    {
        $getAllMessagesQuery = DB::table('user_messages as um');
        $getAllMessagesQuery->where('um.actual_parent_message_id', $messageId);

        //    dd($getAllMessagesQuery->toSql());

        $resultSet = $getAllMessagesQuery->get();


        return $resultSet;
    }

    public static function markAsRead($messageId)
    {
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $result = DB::table('user_messages')
            ->where('user_messages.id', '=', $messageId)
            ->where('user_messages.recepient_id', '=', $userId)
            ->update(array('is_read' => 1));
        return $result;
    }

    public static function notifyMessage()
    {
        // TODO: Implement notifyMessage() method.
    }

    public static function getParentMessageid($messageId)
    {
        try {
            $data = DB::table('user_messages')->where('id', $messageId)->select('parent_message_id', 'actual_parent_message_id')->first();
            if ($data->parent_message_id != 0) {
                return $data->actual_parent_message_id;
            } else
                return $messageId;

        } catch (Exception $ex) {

        }
    }

    public static function getMessageTypes($userId, $roleId)
    {
        try {
            $messageTypes = [];
            $messageTypes[0] = 'Messages (ALL)';
            $messageType = DB::table('lkp_message_types');
            if ($roleId == BUYER) {
                $messageType->where('is_buyer', '1');
            } else if ($roleId == SELLER) {
                $messageType->where('is_seller', '1');
            }
            $messageType->where('is_active', '1');
            $messageType->orderBy('message_type', 'asc');
            $messageType->where('is_active', '1');
            $messageType = $messageType->lists('message_type', 'id');
            foreach ($messageType as $id => $message) {
                $messageTypes[$id] = $message;
            }
            return $messageTypes;
        } catch (Exception $ex) {

        }
    }

    public static function isValidMessage($messageId)
    {
        try {
            $data = DB::table('user_messages')->where('id', $messageId)->first();
            if (count($data) > 0) {
                return true;
            } else
                return false;

        } catch (Exception $ex) {

        }
    }


}