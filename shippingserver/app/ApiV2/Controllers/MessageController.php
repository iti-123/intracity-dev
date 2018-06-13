<?php

namespace ApiV2\Controllers;

use ApiV2\BusinessObjects\MessageBO;
use ApiV2\BusinessObjects\MessageSearchBO;
use ApiV2\Requests\BaseShippingResponse as ShipRes;
use ApiV2\Services\MessageService;
use ApiV2\Utils\CommonComponents;
use App\Exceptions\ApplicationException;
use DB;
use Illuminate\Http\Request;
use Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class MessageController extends BaseController
{

    public static function CreateMessage(Request $request)
    {
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $roleId = JWTAuth::parseToken()->getPayload()->get('role');


        $payload = $request->getContent();

        $parsedJson = json_decode($payload);

        $docId = '';

        if (isset($parsedJson->docId) && $parsedJson->docId != '') {
            $docId = $parsedJson->docId;
        }

        $bo = self::ui2bo_save($parsedJson);

        //Basic Data

        $sender = $parsedJson->message_from;
        $recipient = $parsedJson->message_to;

        //check multi or single user
        $recipientArray = [];
        $index = strpos($recipient, ',', 0);

        if ($index) {
            $recipientArray = explode(',', $recipient);
        }

        //message_subject,message_body,message_subject,message_id

        $bo->parentMessageId = 0;
        $bo->actualParentMessageId = 0;

        //Service Type

        $serviceid = $bo->serviceId;

        //generate Message No Using Service Type
        $bo->messageNo = self::generateMessageNumber($bo->serviceId);

        //Message Type
        $messageType = $bo->messageType;

        if ($bo->messageType == 1) {
            $bo->isGeneral = 1;
        }
        $bo = self::setAdditionalAttrs($bo, "");

        //Send single message if Only 1 recipient
        if (count($recipientArray) == 0) {
            $bo->recepientID = $recipient;
            $created_at = date('Y-m-d H:i:s');
            $results = MessageService::createMessage($bo, $docId);

        }
        //   dd($bo);
        //to send to multiple recipients
        if (count($recipientArray) > 1) {
            for ($i = 0; $i < count($recipientArray); $i++) {
                $bo->recepientID = $recipientArray[$i];
                if (isset($bo->recepientID) && $bo->recepientID != '') {
                    $results = MessageService::createMessage($bo, $docId);
                }
            }
        }

        try {
            Log::info("In Save Message");

            return ShipRes::ok($results);

        } catch (ApplicationException $ae) {

            return ShipRes::nok3($ae);

        } catch (\Exception $e) {

            LOG::info('Failed ', (array)$e);

            return ShipRes::fail("Unhandled exception occured", []);

        }
    }

    public static function ui2bo_save($data)
    {

        $bo = new MessageBO();

        $userId = JWTAuth::parseToken()->getPayload()->get('id');

        //service_id
        if (isset($data->lkp_service_id) && $data->lkp_service_id != '') {
            $bo->serviceId = $data->lkp_service_id; // Temporarily set
        } else {
            $bo->serviceId = FCL; // Temporarily set
        }


        if (isset($data->message_id) && $data->message_id != '') {
            $bo->messageId = $data->message_id; // Temporarily set
        } else {
            $bo->messageId = 0; // Temporarily set General
        }

        //sender_id
        if ($userId == $data->message_from) {
            $bo->senderId = $data->message_from;
        }

        //recipient_id to be set later in the Main function
        $bo->messageNo = self::generateMessageNumber($bo->serviceId, 0);

        // dd($bo);
        //lkp_message_type_id not set then Message Type = 0 [General]
        if (isset($data->lkp_message_type) && $data->lkp_message_type != '') {
            $bo->messageType = self::getMessageTypeId($data->lkp_message_type);
        } else {
            $bo->messageType = 0;
        }

        if (!isset($data->is_read)) {
            $bo->isRead = 0;
        } else {
            $bo->isRead = $data->is_read;
        }
        if (!isset($data->is_reminder)) {
            $bo->isReminder = 0;
        } else {
            $bo->isReminder = $data->is_reminder;
        }
        if (!isset($data->is_notified)) {
            $bo->isNotified = 0;
        } else {
            $bo->isNotified = $data->is_notified;
        }
        if (isset($data->seller_post)) {
            $bo->postId = 1;
        } else {
            $bo->postId = 0;
        }
        if (isset($data->buyer_quote_item) && $data->buyer_quote_item != "") {
            $bo->quoteId = 1;
        }
        if (!isset($data->is_term)) {
            $bo->isTerm = 0;
        } else {
            $bo->isTerm = $data->is_term;
        }
        if (!isset($data->lead_id)) {
            $bo->leadId = 0;
        } else {
            $bo->leadId = $data->lead_id;
        }
        if (!isset($data->enquiry_id)) {
            $bo->enquiryId = 0;
        } else {
            $bo->enquiryId = $data->enquiry_id;
        }
        if (isset($data->parent_message_id) && $data->parent_message_id != "") {

            $bo->parentMessageId = $data->parent_message_id;
        }
        if (isset($data->actual_parent_message_id)) {

            $bo->actualParentMessageId = $data->actual_parent_message_id;
        }
        //subject
        $bo->subject = $data->message_subject;

        //message
        $bo->message = $data->message_body;

        if (isset($data->save_as_draft) && $data->save_as_draft == 1) {
            $bo->isDraft = 1; //if login then use this method
        } else if (isset($data->send_message) && $data->send_message == 1) {
            $bo->isDraft = 0;
        } else {
            $bo->isDraft = 0;
        }
        return $bo;
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
            default:
                $randString = 'GENERAL/' . $created_year . '/' . str_pad($msgid, 6, "0", STR_PAD_LEFT);
                $message_no = $randString;
                break;
        }
        return $message_no;
    }

    public static function getMessageTypeId($type)
    {
        /**
         *   //New Message Types Needed to be created and inserted in table shp_lkp_message_types
         *      0 => GENERAL
         *      1 => BuyerPostTerm Enquiry
         *      2 => BuyerPostSpot Enquiry
         *      3 => Buyer Ratecard Enquiry
         *      4 => Buyer Order Enquiry
         */

        $lkp_message_type_id = 0;  // 0 => General

        if (!isset($type) && ($type == "")) {
            $lkp_message_type_id = 0;
        } else {
            $result = DB::table('lkp_message_types')
                ->where('message_type', $type)
                ->first();
            if (count($result) >= 1) {
                $lkp_message_type_id = $result->id;
            }
        }
        return $lkp_message_type_id;
    }

    public static function setAdditionalAttrs(MessageBO $bo, $messageId = "")
    {
        $roleId = JWTAuth::parseToken()->getPayload()->get('role');
        $postItemId = 0;
        $str = '';
        $postOrOrderId = '';

        if (!empty($bo->quoteItemId)) {
            $postOrOrderId = $bo->quoteItemId;
            $postItemId = $bo->quoteId;
        } elseif (!empty($bo->postItemId)) {
            $postOrOrderId = $bo->postItemId;
            $postItemId = $bo->postId;
        } elseif (!empty($bo->orderId)) {
            $postOrOrderId = $bo->orderId;
        } elseif (!empty($bo->contractId)) {
            $postOrOrderId = $bo->contractId;
        } elseif (!empty($bo->postId)) {
            $postOrOrderId = $bo->postId;
            $postItemId = $bo->postItemId;
        } elseif (!empty($bo->leadId)) {
            $postOrOrderId = $bo->leadId;
            $postItemId = $bo->postItemId;
        }
        if ($messageId != "" && !isset($messageId)) {

            if ($roleId == BUYER) {
                if ($bo->leadId != "" && $bo->leadId != 0) {
                    $bo->quoteItemId = $bo->leadId;
                    $bo->leadId = $postItemId;
                }
                if ($bo->enquiryId != "" && $bo->enquiryId != 0) {
                    $bo->quoteItemId = $bo->enquiryId;
                    $bo->enquiryId = $postItemId;
                }


            } elseif ($roleId == SELLER) {
                if ($bo->leadId != "" && $bo->leadId != 0) {
                    $bo->leadId = $bo->quoteItemId;
                    $bo->postItemId = $bo->leadId;
                }
                if ($bo->enquiry_id != "" && $bo->enquiryId != 0) {
                    $bo->enquiryId = $bo->quoteItemId;
                    $bo->postItemId = $bo->enquiryId;
                }

            }
        } else {
            if ($bo->messageType == LEADSMESSAGETYPE) {
                if ($roleId == BUYER) {
                    $bo->leadId = $postOrOrderId;
                    $bo->quoteItemId = $postItemId;
                } elseif ($roleId == SELLER) {
                    $bo->leadId = $postOrOrderId;
                    $bo->postItemId = $postItemId;
                }
            } else if ($bo->messageType == POSTQUOTEMESSAGETYPE) {
                $bo->enquiryId = $postOrOrderId;
                $bo->quoteItemId = $postItemId;
            } else if ($bo->messageType == ORDERMESSAGETYPE) {
                $bo->orderId = $postOrOrderId;
            } else if ($bo->messageType == CONTRACTMESSAGETYPE) {
                $bo->contractId = $postOrOrderId;
            } else if ($bo->messageType == POSTENQURYMESSAGETYPE) {
                $bo->enquiryId = $postOrOrderId;
                $bo->postItemId = $postItemId;
            }
        }


        /*   if ($userMessage->save()) {

               $insertedMessageId = $userMessage->id;
               if (!empty($insertedMessageId)) {
                   //CommonComponent::auditLog($userMessage->id, 'cart_items');
                   if (isset($fileDetails) && $fileDetails != '') {
                       $certificationDocumentDirectory = 'uploads/message_attachments/';
                       CommonComponent::createDirectory($certificationDocumentDirectory);
                       //echo "<pre>".count($fileDetails);print_r($fileDetails);exit;
                       for ($i = 0; $i < count($fileDetails['name']); $i++) {
                           $error = $fileDetails['error'][$i];
                           $uploadDocument = $fileDetails['name'][$i];
                           if ($error == 0 && !is_array($uploadDocument)) {
                               $uploadedDocumentNameWithoutExtension = pathinfo($uploadDocument, PATHINFO_FILENAME);
                               $fileExtension = pathinfo($uploadDocument, PATHINFO_EXTENSION);
                               $fileNameWithoutSpecialCharacter = CommonComponent::removeSpecialCharacter($uploadedDocumentNameWithoutExtension);
                               $microTimeWithoutSpecialCharacter = CommonComponent::removeSpecialCharacter(microtime());
                               $uniqueFileName = $microTimeWithoutSpecialCharacter . "_" . $fileNameWithoutSpecialCharacter . '.' . $fileExtension;
                               $moveUploadedFile = move_uploaded_file($fileDetails['tmp_name'][$i], $certificationDocumentDirectory . $uniqueFileName);
                               $uploadedFileUrl = $certificationDocumentDirectory . $uniqueFileName;

                               //attatchments saving
                               $userMessageUpload = new UserMessageUpload();
                               $userMessageUpload->user_message_id = $insertedMessageId;
                               $userMessageUpload->name = $uploadDocument;
                               $userMessageUpload->type = $fileExtension;
                               $userMessageUpload->filepath = $uploadedFileUrl;
                               $userMessageUpload->created_by = Auth::user()->id;
                               $userMessageUpload->created_at = $created_at;
                               $userMessageUpload->created_ip = $createdIp;
                               $userMessageUpload->updated_at = $created_at;
                               $userMessageUpload->save();

                           }
                           //                                else{
                           //                                    $uploadedFileUrl = '';
                           //                                    $uploadDocument = '';
                           //                                    $fileExtension = '';
                           //                                }
                       }
                   }
//            else {
//                $uploadedFileUrl = '';
//                $uploadDocument = '';
//                $fileExtension = '';
//            }


               }
           }
       }
    //   dd($bo);


   } catch (Exception $ex) {

                }

   }
*/

        return $bo;
    }

    public static function replies(Request $request, $messageId)
    {
        if (isset($messageId) && $messageId != "") {

            $isMessageExist = MessageService::isValidMessage($messageId);

            if (!$isMessageExist) {

                return shipres::fail('Message Does Bot Exist', ["Invalid Message"]);

            } else {

                $payload = $request->getContent();
                $parsedJson = json_decode($payload);
                $n_bo = self::ui2bo_save($parsedJson);
                $n_bo->messageId = $messageId;
                //Basic Data
                $sender = $parsedJson->message_from;
                $recipient = $parsedJson->message_to;

                //check multi or single user
                $recipientArray = [];
                $index = strpos($recipient, ',', 0);

                if ($index) {
                    $recipientArray = explode(',', $recipient);
                }

                //message_subject,message_body,message_subject,message_id

                $n_bo->parentMessageId = $messageId;
                $n_bo->actualParentMessageId = MessageService::getParentMessageid($messageId);

                //Service Type

                $serviceid = $n_bo->serviceId;

                //generate Message No Using Service Type
                $n_bo->messageNo = self::generateMessageNumber($n_bo->serviceId);

                //Message Type
                $messageType = $n_bo->messageType;

                if (isset($n_bo->messageType) && $n_bo->messageType == 0) {
                    $n_bo->isGeneral = 1;
                } else {
                    $n_bo->isGeneral = 0;
                }

                $n_bo = self::setAdditionalAttrs($n_bo, $messageId);


                //Send single message if Only 1 recipient
                if (count($recipientArray) == 0) {
                    $n_bo->recepientID = $recipient;
                    $results = MessageService::postReply($n_bo);
                }
                //   dd($bo);
                //to send to multiple recipients
                if (count($recipientArray) > 1) {
                    for ($i = 0; $i < count($recipientArray); $i++) {
                        $n_bo->recepientID = $recipientArray[$i];
                        if (isset($n_bo->recepientID) && $n_bo->recepientID != '') {
                            $results = MessageService::postReply($n_bo);
                        }
                    }
                }


            }

        }
        return ShipRes::ok($results);
    }

    public static function getNotificationMessages()
    {
        try {
            LOG::info("Getting Message Types");

            $userId = JWTAuth::parseToken()->getPayload()->get('id');

            $results = MessageService::NotificationMessages();
//dd($results);
            return ShipRes::ok($results);

        } catch (ApplicationException $ae) {

            LOG::info('Failed to get Result ', (array)$ae);

            return ShipRes::nok3($ae);

        } catch (\Exception $e) {

            return ShipRes::fail("Unhandled exception occured ", []);

        }

    }

    public static function getAllMessages()
    {
        try {
            LOG::info("Getting Message Types");

            $userId = JWTAuth::parseToken()->getPayload()->get('id');

            $results = MessageService::listAllMessages();

            return ShipRes::ok($results);

        } catch (ApplicationException $ae) {

            LOG::info('Failed to get Result ', (array)$ae);

            return ShipRes::nok3($ae);

        } catch (\Exception $e) {

            return ShipRes::fail("Unhandled exception occured ", []);

        }

    }

    public static function filter(Request $request)
    {
        try {
            LOG::info("Getting Message Types");

            $userId = JWTAuth::parseToken()->getPayload()->get('id');

            $payload = $request->getContent();

            $parsedJson = json_decode($payload);

            $bo = new MessageSearchBO();

            //  $bo = FCL;

            if (!empty($parsedJson->message_services) && $parsedJson->message_services != '0' && $parsedJson->message_services != '') {
                $bo->message_services = $parsedJson->message_services;
            }
            if (!empty($parsedJson->message_type) && $parsedJson->message_type != '0' && $parsedJson->message_type != '') {
                $bo->message_type = $parsedJson->message_type;
            }
            if (!empty($parsedJson->message_keywords) && $parsedJson->message_keywords != '0' && $parsedJson->message_keywords != '') {
                $bo->message_keywords = $parsedJson->message_keywords;
            }
            if (!empty($parsedJson->from_date) && $parsedJson->from_date != '0' && $parsedJson->from_date != '') {
                $bo->from_message = $parsedJson->from_message;
            }
            if (!empty($parsedJson->to_date) && $parsedJson->to_date != '0' && $parsedJson->to_date != '') {
                $bo->to_message = $parsedJson->to_message;
            }


            $results = MessageService::filter($bo);

            return ShipRes::ok($results);

        } catch (ApplicationException $ae) {

            LOG::info('Failed to get Result ', (array)$ae);

            return ShipRes::nok3($ae);

        } catch (\Exception $e) {

            return ShipRes::fail("Unhandled exception occured ", []);

        }

    }

    //Helper Functions

    public static function getMessageTypes()
    {
        try {
            LOG::info("Getting Message Types");

            $userId = JWTAuth::parseToken()->getPayload()->get('id');

            $role = JWTAuth::parseToken()->getPayload()->get('role');

            $results = MessageService::getMessageTypes($userId, $role);

            return ShipRes::ok($results);

        } catch (ApplicationException $ae) {

            LOG::info('Failed to get Result ', (array)$ae);

            return ShipRes::nok3($ae);

        } catch (\Exception $e) {

            return ShipRes::fail("Unhandled exception occured ", []);

        }

    }


    //Helper Functions

    public static function getThread($messageId)
    {
        $results = MessageService::getThread($messageId);

        return ShipRes::ok($results);
    }

    public static function markAsRead($messageId)
    {
        try {
            $results = MessageService::markAsRead($messageId);
        } catch (ApplicationException $ae) {

            LOG::info('Failed', (array)$ae);
            return ShipRes::nok3($ae);

        } catch (\Exception $e) {

            LOG::info('Failed', (array)$e);

            return ShipRes::fail("Unhandled exception occured", []);

        }
    }

    public static function getMessage($messageId)
    {

        try {
            if ($messageId != 0) {
                // $results = MessageService::markAsRead($messageId);
            }
            $results = MessageService::getMessageDetails($messageId);

        } catch (ApplicationException $ae) {

            LOG::info('Failed', (array)$ae);
            return ShipRes::nok3($ae);

        } catch (\Exception $e) {

            LOG::info('Failed', (array)$e);

            return ShipRes::fail("Unhandled exception occured", []);

        }
        return ShipRes::ok($results);

    }

    public static function getmessagebyid($messageId)
    {

        try {
            if ($messageId != 0) {
                // $results = MessageService::markAsRead($messageId);
            }
            $results = MessageService::getMessage($messageId);

        } catch (ApplicationException $ae) {

            LOG::info('Failed', (array)$ae);
            return ShipRes::nok3($ae);

        } catch (\Exception $e) {

            LOG::info('Failed', (array)$e);

            return ShipRes::fail("Unhandled exception occured", []);

        }


    }

    public static function model2bo_save($data)
    {

        return $data;
    }

}
