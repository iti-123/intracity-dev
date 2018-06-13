<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 4/11/17
 * Time: 12:01 PM
 */

namespace ApiV2\Services;

use ApiV2\BusinessObjects\BuyerPostBO;
use ApiV2\BusinessObjects\ContractBO;
use ApiV2\BusinessObjects\MessageBO;
use ApiV2\BusinessObjects\SellerPostBO;


/**
 * Interface INotificationService
 * @package Api\Services
 */
interface INotificationService
{

    //Functional
    public static function notifyBuyerPostTermCreated(BuyerPostBO $bo);

    public static function notifyMessagePostCreated(MessageBO $bo);


    /* ------------------------------------------
       BUYER POST EVENTS
    ------------------------------------------ */

    public static function notifyBuyerPostCreated($bo);


    public static function notifyPrivateSellerEdit();


    public static function notifyCounterOfferAgainstOffer();


    /**
     * TODO NOT DOING THIS
     * @return mixed
     */
    public static function notifyMatchingCountChange();


    /* ------------------------------------------
            SELLER POST EVENTS
       ------------------------------------------ */

//Implementated and Functonal
    public static function notifySellerPostCreated($bo);


    public static function notifySellerSubmitQuote();


    public static function notifySellerAcceptedOffer();


    /* ------------------------------------------
        MESSAGE EVENTS
        TODO : There are many message events.
        TODO : Can one handler handle all of them
        TODO : For example order message, post messages etc..
     ------------------------------------------ */


    public static function notifyMessageSent(MessageBO $bo);


    /* ------------------------------------------
        ORDER CREATION EVENTS
     ------------------------------------------ */

    public static function notifyBookNow($bo);


    public static function notifyIndentRaised();


    /* ------------------------------------------
        ORDER PROCESSING EVENTS
     ------------------------------------------ */


    public static function notifyAcceptGSA();


    public static function notifyPlacedTruck();


    public static function notifyConsignmentPickedUp();


    public static function notifyRealtimeMilestoneTracking();


    public static function notifyDeliveryDetails();


    /* ------------------------------------------
            ADDITIONAL TERM EVENTS
     ------------------------------------------ */


    public static function notifyContractGenerated(ContractBO $bo);


    public static function notifyContractAccepted(ContractBO $bo);


    public static function notifyContractCancelled(ContractBO $bo);

    /*--------------------------------------------
        Order Document Notification Service
    ---------------------------------------------*/

    public static function notifyDocumentEmail($bo);

    public static function createNotification($bo);

}