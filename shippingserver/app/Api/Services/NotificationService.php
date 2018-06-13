<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 4/11/17
 * Time: 1:50 PM
 */

namespace Api\Services;


use Api\BusinessObjects\BuyerPostBO;
use Api\BusinessObjects\ContractBO;
use Api\BusinessObjects\MessageBO;
use Api\BusinessObjects\NotificationBO;
use Api\BusinessObjects\SellerPostBO;
use Api\Model\Notification;
use Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class NotificationService implements INotificationService
{

    public static function notifySellerPostCreated(SellerPostBO $bo)
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
        //     LOG::info('bo2model Start');
        $now = date('Y-m-d H:i:s');
        $role = JWTAuth::parseToken()->getPayload()->get('id');
        $model = new NotificationBO();
        $c = get_class($bo);
        if ($c == "Api\Modules\FCL\FCLSellerPostBO") {
            $model->postId = $bo->postId;
            $model->postTitle = $bo->title;
            $model->postNumber = $bo->transactionId;
            $model->routeLevel = '';
            $model->postStatus = 1;
            $model->service = $bo->serviceId;
            $model->type = 1;
            $model->postType = 1;
            $model->role = $role;
            $model->createdBy = $bo->sellerId;
            if ($bo->status == 'open') {
                $model->event = 'Seller Ratecard Draft';
            } else {
                $model->event = 'Seller Ratecard Confirm';
            }
            $model->viewCount = 0;
            $model->isActive = 1;
            if (!empty($bo->updatedAt)) {
                //  $model->updated_at = $bo->updatedAt;
            } else {
                //  $model->created_at = date('Y-m-d H:i:s');
            }
        } else if ($c == "Api\Modules\FCL\FCLSpotBuyerPostBO" || "Api\Modules\FCL\FCLTermBuyerPostBO") {
            $type = "Spot";
            if ($bo->leadType == 'term') {
                $type = "Term";
            }
            $model->postId = $bo->postId;
            $model->postTitle = $bo->title;
            $model->postNumber = $bo->transactionId;
            $model->routeLevel = '';
            $model->postStatus = 1;
            $model->service = $bo->serviceId;
            $model->type = 1;
            $model->postType = 2;
            $model->role = $role;
            $model->createdBy = $bo->buyerId;
            if ($bo->status == 'open' || $bo->status == OPEN) {
                $model->event = 'Buyer ' . $type . ' Post & get confirm';
            } else {
                $model->event = 'Buyer ' . $type . ' Post & get draft creation';
            }
            $model->viewCount = 0;
            $model->isActive = 1;
            if (!empty($bo->updatedAt)) {
                //  $model->updated_at = $bo->updatedAt;
            } else {
                //  $model->created_at = date('Y-m-d H:i:s');
            }

        } else if ($c == "Api\BusinessObjects\MessageBO") {
            $model->event = 'User Notification Messages';
            $model->viewCount = 0;
            $model->isActive = 1;
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

    public static function notifyBuyerPostCreated(BuyerPostBO $bo)
    {
        $notification = new NotificationBO();
        // $userId = JWTAuth::parseToken()->getPayload()->get('id');
        //$model = self::castinputbo2eventbo($bo, $notification);
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

    public static function notifyBookNow()
    {
        // TODO: Implement notifyBookNow() method.
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


}