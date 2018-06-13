<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 4/11/17
 * Time: 1:50 PM
 */

namespace ApiV2\Services;

use ApiV2\BusinessObjects\BuyerPostBO;
use ApiV2\BusinessObjects\ContractBO;
use ApiV2\BusinessObjects\MessageBO;
use ApiV2\BusinessObjects\NotificationBO;
use ApiV2\BusinessObjects\SellerPostBO;
use ApiV2\Services\SendSmsService;
use ApiV2\Model\Notification;
use App\User;
use Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Auth;
use Session;
use ApiV2\Services\AbstractNotificationService;
class NotificationService extends AbstractNotificationService implements INotificationService
{
    public static function getUserRole()
    {
        if ((isset(Auth::user()->id) && Auth::user()->lkp_role_id == BUYER && Auth::user()->mail_sent == 1 && Session::get('last_login_role_id') == 0) || (Session::get('last_login_role_id') == BUYER)) {
            return BUYER;
        }
        return SELLER;
    }

    public static function notifySellerPostCreated($bo)
    {
        LOG::info('notifySellerPostCreated Start');
        $notification = new Notification();
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $notificationbo = self::castinputbo2eventbo($bo, $notification);

        $notification = self::bo2model($notificationbo, $notification);
        $notification->save((array)$notificationbo);
    }

    private static function castinputbo2eventbo($bo, $notification)
    {
        $now = date('Y-m-d H:i:s');
        $role = JWTAuth::parseToken()->getPayload()->get('active_role_id');
        $model = new NotificationBO();
        $c = get_class($bo);
        $bo->role = $role;
        LOG::info('bo2model Start '. $bo->visible_to_seller);        
        Log::info('CLass '.$c);
        if ($c == "ApiV2\Model\IntraHyperSellerPost") {

            $counts = parent::notificationCounterService($c, $bo);
            $model->postLeads = $counts['leads'];
            $model->postMessages = $counts['message'];
            
            $model->postId = $bo->id;
            $model->postTitle = $bo->title;
            $model->postNumber = $bo->post_transaction_id;
            $model->routeLevel = '';
            $model->postStatus = 1;
            $model->service = $bo->lkp_service_id;
            $model->type = 1;
            $model->postType = 1;
            $model->role = $role;
            $model->createdBy = $bo->posted_by;
            $model->isPublic = (int)$bo->is_private_public;
            $model->userId = $bo->assign_buyer;
            if ($bo->is_active == 'open' || $bo->is_active == _OPEN_) {
                $model->event = 'Seller Ratecard Draft';
            } else {
                $model->event = 'Seller Ratecard Confirm';
            }
            $model->viewCount = 0;
            $model->isActive = 1;
            if (!empty($bo->updated_at)) {
                //  $model->updated_at = $bo->updatedAt;
            } else {
                //  $model->created_at = date('Y-m-d H:i:s');
            }
            $user = User::where("id",$model->createdBy)->first(["username"]);
            $params = array(
                "randnumber"=>$model->postNumber,
                "users"=> $model->userId,//SendSMSToBuyer
                "sellername"=>isset($user) && !empty($user)?$user->username:'Prince',
                "servicename"=>$model->service == _HYPERLOCAL_ ?"Hyperlocal":$model->service == _INTRACITY_?"Intracity":'',
            );
            SendSmsService::sendSms($model,2,$params);

        } else if ($c == "ApiV2\Model\IntraHyperBuyerPost" || $c=="ApiV2\Model\IntraHyperBuyerPostTerm") {
            $type = 1;
            $isSpot = 'SPOT';
            if ($bo->lead_type == INTRA_HYPER_TERM) {
                $type = 2;
                $isSpot = 'TERM';
            }
            $counts = parent::notificationCounterService($c, $bo);
            $model->postLeads = $counts['leads'];
            $model->postMessages = $counts['message'];

            $model->postId = $bo->id;
            $model->postTitle = $bo->title;
            $model->postNumber = $bo->post_transaction_id;
            $model->routeLevel = '';
            $model->postStatus = 1;
            $model->service = $bo->lkp_service_id;
            $model->type = 1;
            $model->postType = $type;
            $model->role = $role;
            $model->isSpotTerm = $isSpot;
            $model->createdBy = $bo->posted_by; //$bo->created_by;
            
            $model->viewCount = 0;
            $model->isActive = 1;
            if (!empty($bo->updated_at)) {
                 $model->updated_at = $bo->updatedAt;
            } else {
                 $model->updated_at = date('Y-m-d H:i:s');
            }
            $user = User::where("id",$model->createdBy)->first(["username"]);
            $params = array(
                "randnumber"=>$model->postNumber,
                "users"=> isset($bo->visible_to_seller)?$bo->visible_to_seller:'',//SendSMSToSeller
                "buyername"=>isset($user) && !empty($user)?$user->username:'Prince',
                "servicename"=>$model->service == _HYPERLOCAL_ ?"Hyperlocal":$model->service == _INTRACITY_?"Intracity":'',
            );

            SendSmsService::sendSms($model,3,$params);

        } else if($c == "ApiV2\Model\BlueCollar\Post"){
            Log::info('ApiV2\Model\BlueCollar\Post '.$bo);
            $type = $bo->post_type;
            
            $model->postId = $bo->id;
            $model->postTitle = $bo->title;
            $model->postNumber = $bo->post_transaction_id;
            $model->routeLevel = '';
            $model->postStatus = 1;
            $model->service = $bo->service;
            $model->type = 1;
            $model->postType = 2;
            $model->role = $role;
            $model->createdBy = $bo->posted_by; //$bo->created_by;
            $model->isPublic = (int)$bo->is_private_public;
            $model->userId = (string) $bo->visible_to_seller;
           
            if ($bo->is_active == 'open' || $bo->is_active == _OPEN_) {
                $model->event = 'Buyer ' . $type . ' Post & get confirm';
            } else {
                $model->event = 'Buyer ' . $type . ' Post & get draft creation';
            }
            $model->viewCount = 0;
            $model->isActive = 1;
            if (!empty($bo->updated_at)) {
                 $model->updated_at = $bo->updatedAt;
            } else {
                 $model->updated_at = date('Y-m-d H:i:s');
            }
            $user = User::where("id",$model->createdBy)->first(["username"]);
            $params = array(
                "randnumber"=>$model->postNumber,
                "users"=> isset($bo->visible_to_seller)?$bo->visible_to_seller:'',//SendSMSToSeller
                "buyername"=>isset($user) && !empty($user)?$user->username:'Prince',
                "servicename"=>$model->service == _HYPERLOCAL_ ?"Hyperlocal":$model->service == _INTRACITY_?"Intracity":'',
            );
            SendSmsService::sendSms($model,3,$params);

        } else if ($c == "ApiV2\BusinessObjects\MessageBO") {
            $model->event = 'User Notification Messages';
            $model->viewCount = 0;
            $model->isActive = 1;
        } else if ($c == "ApiV2\Model\Order") {
            Log::info('User Order Notification '.$bo->id);
            $model->event = 'User Order Notification';
            $model->postId = $bo->id;
            $model->postTitle = $bo->title;
            $model->postNumber = $bo->order_no;
            $model->routeLevel = '';
            $model->postStatus = 1;
            $model->service = $bo->lkp_service_id;
            $model->type = 3;
            $model->postType = 1;
            $model->role = $bo->roleId;
            $model->createdBy = $bo->createdBy;
            $model->orderId = $bo->id;
            $model->orderStatus = 1;
            if ($bo->is_active == 'open' || $bo->is_active == _OPEN_) {
                $model->event = 'Seller Ratecard Draft';
            } else {
                $model->event = 'Seller Ratecard Confirm';
            }
            $model->viewCount = 0;
            $model->isActive = 1;
            if (!empty($bo->updated_at)) {
                 $model->updated_at = $bo->updatedAt;
            } else {
                 $model->Updated_at = date('Y-m-d H:i:s');
            }
            $user = User::where("id",$model->createdBy)->first(["username"]);
            $params = array(
                "ordernumber"=>$model->postNumber,
                "users"=>"",
                "buyername"=>isset($user) && !empty($user)?$user->username:'Prince',
                "servicename"=>$model->service == _HYPERLOCAL_ ?"Hyperlocal":$model->service == _INTRACITY_?"Intracity":'',
            );

            
            SendSmsService::sendSms($model,5,$params);
        }
        
        return $model;
    }

    private static function bo2model(NotificationBO $bo, $model)
    {
        LOG::info('bo2model Start');
        $now = date('Y-m-d H:i:s');
        $model->post_id = $bo->postId;
        $model->post_title = $bo->postTitle;
        $model->post_number = $bo->postNumber;
        $model->route_level = $bo->routeLevel;
        $model->post_status = $bo->postStatus;
        $model->service = $bo->service;
        $model->type = $bo->type;
        $model->message_body = $bo->messageBody;
        $model->post_type = $bo->postType;
        $model->message_type = $bo->messageType;
        $model->role = $bo->role;
        $model->created_by = $bo->createdBy;
        $model->event = $bo->event;
        $model->post_enquiries = $bo->postEnquiries;
        $model->post_leads = $bo->postLeads;
        $model->post_offers = $bo->postOffers;
        $model->post_messages = $bo->postMessages;
        $model->documents = $bo->documents;
        $model->order_messages = $bo->orderMessages;
        $model->order_indents = $bo->orderIndents;
        $model->order_status = $bo->orderStatus;
        $model->order_billing = $bo->orderBilling;
        $model->order_docs = $bo->orderDocs;
        $model->order_id = $bo->orderId;
        $model->view_count = $bo->viewCount;
        $model->is_active = $bo->isActive;
        $model->is_public = $bo->isPublic;
        $model->user_id = $bo->userId;
        $model->is_spot_term = $bo->isSpotTerm;

        if (!empty($bo->updatedAt)) {
            $model->Updated_at = ''; //$bo->updatedAt;
        } else {
            $model->Updated_at = ''; //date('Y-m-d H:i:s');
        }
        
        return $model;
    }

    public static function notifyBuyerPostTermCreated(BuyerPostBO $bo)
    {
        LOG::info('notifyBuyerPostTermCreated');
    }

    public static function notifyMessagePostCreated(MessageBO $bo)
    {
        $notification = new NotificationBO();
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $notification = new Notification();
        $notificationbo = self::castinputbo2eventbo($bo, $notification);
        $notification = self::bo2model($notificationbo, $notification);
        $notification->save((array)$notificationbo);
    }


    //===========================================================================================================

    public static function notifyBuyerPostCreated($bo)
    {
        Log::info('notifyBuyerPostCreated:::'.$bo);
        $notification = new Notification();
        $notificationbo = self::castinputbo2eventbo($bo, $notification);
        $notification = self::bo2model($notificationbo, $notification);
        $notification->save((array)$notificationbo);
    }

    public static function notifyPrivateSellerEdit()
    {
        // TODO: Implement notifyPrivateSellerEdit() method.
    }

    public static function notifyCounterOfferAgainstOffer()
    {
        // TODO: Implement notifyCounterOfferAgainstOffer() method.
    }

    public static function notifyMatchingCountChange()
    {
        // TODO: Implement notifyMatchingCountChange() method.
    }

    public static function notifySellerSubmitQuote()
    {
        // TODO: Implement notifySellerSubmitQuote() method.
    }

    public static function notifySellerAcceptedOffer()
    {
        // TODO: Implement notifySellerAcceptedOffer() method.
    }

    public static function notifyMessageSent(MessageBO $bo)
    {
        // TODO: Implement notifyMessageSent() method.
    }

    public static function notifyBookNow($bo)
    {
        // Log::info('notifyBookNow:: '. get_class($bo));
        // LOG::info('notifyBookNow Start'. $bo);
        $notification = new Notification();
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $notificationbo = self::castinputbo2eventbo($bo, $notification);
        // Log::info(__FILE__.'   '. print_r($notificationbo));
        $notification = self::bo2model($notificationbo, $notification);
        $notification->save((array)$notificationbo);
    }

    public static function notifyIndentRaised()
    {
        // TODO: Implement notifyIndentRaised() method.
    }

    public static function notifyAcceptGSA()
    {
        // TODO: Implement notifyAcceptGSA() method.
    }

    public static function notifyPlacedTruck()
    {
        // TODO: Implement notifyPlacedTruck() method.
    }

    public static function notifyConsignmentPickedUp()
    {
        // TODO: Implement notifyConsignmentPickedUp() method.
    }

    public static function notifyRealtimeMilestoneTracking()
    {
        // TODO: Implement notifyRealtimeMilestoneTracking() method.
    }

    public static function notifyDeliveryDetails()
    {
        // TODO: Implement notifyDeliveryDetails() method.
    }

    public static function notifyContractGenerated(ContractBO $bo)
    {
        // TODO: Implement notifyContractGenerated() method.
    }

    public static function notifyContractAccepted(ContractBO $bo)
    {
        // TODO: Implement notifyContractAccepted() method.
    }

    public static function notifyContractCancelled(ContractBO $bo)
    {
        // TODO: Implement notifyContractCancelled() method.
    }

    public static function notifyDocumentEmail($bo)
    {
        Log::info('notifyDocumentEmail::'.$bo);
        $notification = new Notification();
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $role = JWTAuth::parseToken()->getPayload()->get('primary_role_id');
        $notification->service = $bo->serviceId;
        $notification->post_id = (int)$bo->postId;
        $notification->post_title = $bo->title;
        $notification->post_number = $bo->orderNo;
        $notification->post_type = (int)$bo->postType;
        $notification->created_by = $userId;
        $notification->role = $role;
        
        $notification->event = 'Document email event';
        $notification->order_id = $bo->order_id;
        
        
        $notification->save();       

    }

    public static function createNotification($bo) {
        $notification = new Notification();
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $notificationbo = self::castinputbo2eventbo($bo, $notification);
        $notification = self::bo2model($notificationbo, $notification);
        $notification->save((array)$notificationbo);       
    }
    


}