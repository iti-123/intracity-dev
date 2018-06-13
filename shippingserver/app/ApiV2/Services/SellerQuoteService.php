<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 2/18/2017
 * Time: 12:13 PM
 */

namespace ApiV2\Services;

use ApiV2\BusinessObjects\SellerQuoteBO;
use ApiV2\Model\BuyerContract;
use ApiV2\Model\BuyerPost;
use ApiV2\Model\SelectedSellers;
use ApiV2\Model\SellerQuotes;
use ApiV2\Utils\LoggingServices;
use App\Exceptions\ApplicationException;
use App\Jobs\SendBuyerPostSMSAlert;
use App\Jobs\SendEmailAlert;
use App\Jobs\SyncSellerQuotes2SearchStore;
use DB;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class SellerQuoteService extends BaseService implements ISellerQuoteService
{

    use DispatchesJobs;

    static $SVCERRORS = array(
        1 => "One or more validation errors prevented saving this SellerQuote. Please check the internal errors and try again",
        2 => "Failed to save Seller Quote"
    );
    /**
     * @var ISellerQuote
     */
    private $serviceFactory;

    public function __construct()
    {
    }

    private static function getContainersList($items)
    {
        $postId = $items->buyerPostId;
        $buyerId = JWTAuth::parseToken()->getPayload()->get('id');
        $containers = $containersArray = [];
        $buyerContractedContainers = BuyerContract::where('buyerPostId', $postId)
            ->where('sellerId', $items->sellerId)
            ->where('buyerId', $buyerId)->get()->toArray();

        if (sizeof($buyerContractedContainers) > 0) {
            $portPairs = json_decode($buyerContractedContainers[0]["attributes"])->portPairs;
            foreach ($portPairs as $portPair) {
                if ($items->quoteId == $portPair->quoteId) {
                    foreach ($items['attributes']->containers as $container) {
                        if ($portPair->containerType == $container->containerType) {
                            $containers["containerType"] = $container->containerType;
                            $containers["initialOffer"] = $container->initialOffer;
                            $containers["counterOffer"] = $container->counterOffer;
                            $containers["finalOffer"] = $container->finalOffer;
                            $containers["quantity"] = $portPair->quantity;
                        }
                        if (sizeof($containers) > 0)
                            array_push($containersArray, $containers);
                        $containers = [];
                    }
                }
                /*else if(!in_array((int)$items["quoteId"], (array)$portPairs)){
                    dd($items["quoteId"]);
                }*/
            }
            return $containersArray;
        }
        return json_decode($items->attributes)->containers;
    }

    public function setServiceFactory($factory)
    {
        $this->serviceFactory = $factory;
    }

    public function saveOrUpdateTermQuotes(array $bos)
    {
        Log::info("In saveOrUpdateTerm");
        $response = array();

        //start a transaction here
        DB::beginTransaction();

        foreach ($bos as $bo) {
            $response[] = $this->saveOrUpdateTermQuote($bo, false);
        }

        DB::commit();

        return $response;
    }

    public function saveOrUpdateTermQuote(SellerQuoteBO $bo)
    {
        Log::info("In SellerTermQuote");
        $serviceName = unserialize(SHIPPING_MODULES);

        $response = null;
        $isNewPost = false;
        $notify = array();
        try {
            if (!trim($bo->quoteId)) {
                $isNewPost = true;
                LOG::info('Creating new SellerQuote, for buyer post ID:  [' . $bo->buyerPostId . ']');
            } else {
                LOG::info('Updating existing SellerQuote, for buyer post ID: [' . $bo->buyerPostId . ']');
            }
            if ($bo->awardType != 'negotiable') {
                LOG::info('Validate the request');
                $errors = $this->serviceFactory->makeValidator()->validateSave($bo, 'term');

                LOG::info('Validation errors found => ' . sizeof($errors));
                LOG::info($errors);

                if (sizeof($errors) > 0) {
                    //atleast one validation error is found.
                    throw new ApplicationException(["buyerPostId" => $bo->buyerPostId, "buyerId" => $bo->buyerId], $errors);
                }
            }

            LOG::info('Transform to the model');

            //Save model object
            if ($isNewPost) {
                $model = new SellerQuotes();
                $logginEntity = FCL_SELLER_ADDED_NEW_QUOTE;
                LOG::info('Before Seller Quote save()');
                $model = $this->bo2model($bo, $model);
            } else {
                $model = SellerQuotes::findOrFail($bo->quoteId);
                $logginEntity = FCL_SELLER_QUOTE_UPDATE;
                LOG::info('Model Update');
                if ($bo->awardType == 'negotiable') {
                    $model = $this->termBo2model($bo, $model);
                } else {
                    $model = $this->bo2model($bo, $model);
                }
            }

            //$model->post_visible_to= $bo->visibleToSellers;
            $isSaved = $model->save();

            LOG::info('PostId Generated  =>' . $model->id);
            LOG::info($model);

            if (trim($isSaved)) {

                // LoggingServices::auditLog($model->id, BUYER_ADDED_NEW_QUOTE, $model);
                Log::info("SellerQuote Saved Successfully.");
                $notify = $bo->buyerId;
                $bo->quoteId = $model->id;
                $bo->updatedBy = $model->createdBy;
                $bo->updatedBy = $model->updatedBy;
                $bo->totalFreightCharges = $model->totalFreightCharges;

                $response = $bo;
                //Save Selected sellers
                LoggingServices::activityLog($logginEntity, $logginEntity, 0, HTTP_REFERRER, CURRENT_URL, $model->serviceId);
                LoggingServices::auditLog($model->id, $logginEntity, json_encode($response));


                //Send email to buyer
                if ($bo->status == "Initial Offer") {
                    $event = SELLER_GIVEN_INITAL_QUOTE;
                    $emailInfo = $this->getEmailInfo('seller');
                } else if ($bo->status == "Final Offer" || $bo->status == "L1 Offer") {
                    $event = SELLER_GIVEN_FINAL_QUOTE;
                    $emailInfo = $this->getEmailInfo('seller');
                } else if ($bo->status == "Firm Offer") {
                    $event = SELLER_ACCEPTED_FIRM_OFFER;
                    $emailInfo = $this->getEmailInfo('seller');
                } else if ($bo->status == "Counter Offer") {
                    $event = BUYER_GIVEN_COUNTER_OFFER;
                    $emailInfo = $this->getEmailInfo('buyer');
                    $notify = $bo->sellerId;
                }


                $job = new SendEmailAlert((array)$notify, $emailInfo, $event);
                $this->dispatch($job);
                LOG::info('Dispatched Email Notification');

                /*$job = new SendEmailAlert( [ $notify ] );
                $this->dispatch($job);*/

                //*******Send Sms to the private Sellers***********************//
                $trans_randid = $serviceName[$bo->serviceId] . '/' . date('Y') . '/' . str_pad($model->id, 6, 0, STR_PAD_LEFT);
                $msg_params = array(
                    'randnumber' => $trans_randid,
                    'buyername' => JWTAuth::parseToken()->getPayload()->get('firstname'),
                    'servicename' => $serviceName[$bo->serviceId]
                );
                Log::info($notify);

                $mobileNumbers = SendSmsService::getMobleNumber((array)$bo->buyerId);
                // $smsArray  =   SendSmsService::getSellerMobileNumbers((array) $notify);

                if (sizeof($mobileNumbers) > 0) {
                    $userID = JWTAuth::parseToken()->getPayload()->get('id');
                    $job = new SendBuyerPostSMSAlert($mobileNumbers, BUYER_CREATED_POST_FOR_SELLERS_SMS, $msg_params, $userID);
                    $this->dispatch($job);
                    // SendSmsService::shpSendSMS($getMobileNumber,BUYER_CREATED_POST_FOR_SELLERS_SMS,$msg_params);
                }
                //*******Send Sms to the private Sellers***********************//

            } else {
                throw new ApplicationException(["BuyerPostId" => $bo->buyerPostId, "buyerId" => $bo->buyerId], [2, self::$SVCERRORS[2]]);
            }
            $response = $bo;


        } catch (Exception $e) {
            echo 'Exception from : ', $e->getMessage(), "\n";
            throw new ApplicationException(["Problem while saving Buyer Post"], [$e->getMessage() . 'for postId =>  ' . $bo->postId]);
        }
        return $response;
    }

    private function bo2model(SellerQuoteBO $bo, $model)
    {
        LOG::info('bo2model Start');
        $now = date('Y-m-d H:i:s');
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        if (!empty($bo->quoteId)) {
            $model->updatedBy = $userId;
            $model->updatedIP = $_SERVER['REMOTE_ADDR'];
            $model->updated_at = $now;
        } else {
            $model->createdBy = $userId;
            $model->createdIp = $_SERVER['REMOTE_ADDR'];
            $model->created_at = $now;
        }
        $model->status = $bo->status;
        switch ($bo->status) {
            case 'Initial Offer':
            case 'initial_offer':
                $model->initialQuoteAt = time();
                break;
            case 'Counter Offer':
            case 'counter_offer':
                $model->counterQuoteAt = time();
                break;
            case 'Final Offer':
            case 'Firm Offer':
            case 'L1 Offer':
            case 'final_offer':
            case 'firm_offer':
            case 'l1_offer':
                $model->finalQuoteAt = time();
                break;
        }
        $model->buyerId = $bo->buyerId;
        $model->sellerId = $bo->sellerId;
        $model->buyerPostId = $bo->buyerPostId;
        $model->serviceId = $bo->serviceId;
        if ($bo->sellerId == $userId)
            $model->isSellerAccepted = $bo->isSellerAccepted;
        if ($bo->buyerId == $userId)
            $model->isBuyerAccepted = $bo->isBuyerAccepted;
        //$model->isBooked= $bo->isBooked;
        $model->awardType = $bo->awardType;
        $model->loadPort = $bo->loadPort;
        $model->dischargePort = $bo->dischargePort;
        $model->validTill = $bo->validTill;
        $model->totalFreightCharges = $this->getTotalFreightCharges($bo);
        $model->attributes = json_encode((array)$bo->attributes);
        return $model;
    }

    public function getTotalFreightCharges($bo)
    {
        $freightCharge = 0;
        if ($bo->awardType == "l1" || $bo->awardType == "negotiable") {
            //Delegate request to sellerPostService
            foreach ($bo->attributes->containers as $container) {
                $freightCharge += $container->finalOffer->freightCharges->amount;
            }
            return $freightCharge;
        }
        return;
    }

    private function termBo2model(SellerQuoteBO $bo, $model)
    {
        LOG::info('bo2model Start');
        $now = date('Y-m-d H:i:s');
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        if (!empty($bo->quoteId)) {
            $model->updatedBy = $userId;
            $model->updatedIP = $_SERVER['REMOTE_ADDR'];
            $model->updated_at = $now;
        }
        $model->status = $bo->status;
        switch ($bo->status) {
            case 'Initial Offer':
            case 'initial_offer':
                $model->initialQuoteAt = time();
                break;
            case 'Counter Offer':
            case 'counter_offer':
                $model->counterQuoteAt = time();
                break;
            case 'Final Offer':
            case 'Firm Offer':
            case 'L1 Offer':
            case 'final_offer':
            case 'firm_offer':
            case 'l1_offer':
                $model->finalQuoteAt = time();
                break;
        }
        $model->attributes = json_encode((array)$bo->attributes);
        return $model;
    }

    private static function getEmailInfo($user)
    {

        if ($user == 'seller') {
            $emailInfo = ['sellername' => JWTAuth::parseToken()->getPayload()->get('firstname')];

        } else if ($user == 'buyer') {
            $emailInfo = ['buyername' => JWTAuth::parseToken()->getPayload()->get('firstname')];

        }

        return $emailInfo;
    }

    public function saveOrUpdate(SellerQuoteBO $bo)
    {
        Log::info("in SellerQuote");

        $response = null;
        $isNewPost = false;
        $notify = array();
        try {
            if (!trim($bo->quoteId)) {
                $isNewPost = true;
                LOG::info('Creating new SellerQuote, for buyer post ID:  [' . $bo->buyerPostId . ']');
            } else {
                LOG::info('Updating existing SellerQuote, for buyer post ID: [' . $bo->buyerPostId . ']');
            }

            LOG::info('Validate the request');
            $errors = $this->serviceFactory->makeValidator()->validateSave($bo);

            LOG::info('Validation errors found => ' . sizeof($errors));
            LOG::info($errors);

            if (sizeof($errors) > 0) {
                //atleast one validation error is found.
                throw new ApplicationException(["buyerPostId" => $bo->buyerPostId, "buyerId" => $bo->buyerId], $errors);
            }

            LOG::info('Transform to the model');

            //Save model object
            if ($isNewPost) {
                $model = new SellerQuotes();
                $logginEntity = FCL_SELLER_ADDED_NEW_QUOTE;
            } else {
                $model = SellerQuotes::findOrFail($bo->quoteId);
                $logginEntity = FCL_SELLER_QUOTE_UPDATE;
                LOG::info('Model Update');
            }
            LOG::info('Before Seller Quote save()');
            $model = $this->bo2model($bo, $model);
            //$model->post_visible_to= $bo->visibleToSellers;
            $isSaved = $model->save();

            LOG::info('PostId Generated  =>' . $model->id);
            LOG::info($model);

            if (trim($isSaved)) {

                // LoggingServices::auditLog($model->id, BUYER_ADDED_NEW_QUOTE, $model);
                Log::info("SellerQuote Saved Successfully.");
                $notify = $bo->buyerId;
                $bo->quoteId = $model->id;
                $response = $bo;
                //Save Selected sellers
                LoggingServices::activityLog($logginEntity, $logginEntity, 0, HTTP_REFERRER, CURRENT_URL, $model->serviceId);
                LoggingServices::auditLog($model->id, $logginEntity, json_encode($response));

                // $serviceName  = unserialize(SHIPPING_MODULES);
                // $trans_randid = $serviceName[$bo->serviceId].'/'.date('Y').'/'.str_pad($model->id, 6, 0, STR_PAD_LEFT);

                //Send email to buyer
                if ($bo->status == "Initial Offer") {
                    $event = SELLER_GIVEN_INITAL_QUOTE;
                    $emailInfo = $this->getEmailInfo('seller');
                } else if ($bo->status == "Final Offer" || $bo->status == "L1 Offer") {
                    $event = SELLER_GIVEN_FINAL_QUOTE;
                    $emailInfo = $this->getEmailInfo('seller');
                } else if ($bo->status == "Firm Offer") {
                    $event = SELLER_ACCEPTED_FIRM_OFFER;
                    $emailInfo = $this->getEmailInfo('seller');
                } else if ($bo->status == "Counter Offer") {
                    $event = BUYER_GIVEN_COUNTER_OFFER;
                    $emailInfo = $this->getEmailInfo('buyer');
                    $notify = $bo->sellerId;
                }


                // $emailInfo = ['sellername'=>JWTAuth::parseToken()->getPayload()->get('firstname')];

                $job = new SendEmailAlert((array)$notify, $emailInfo, $event);
                $this->dispatch($job);
                LOG::info('Dispatched Email Notification');

                //*******Send Sms to the private Sellers***********************//
                //$trans_randid = 'FCL/'.date('Y').'/00000'.$bo->quoteId;
                $serviceName = unserialize(SHIPPING_MODULES);
                $trans_randid = $serviceName[$bo->serviceId] . '/' . date('Y') . '/' . str_pad($model->id, 6, 0, STR_PAD_LEFT);
                $msg_params = array(
                    'randnumber' => $trans_randid,
                    'buyername' => JWTAuth::parseToken()->getPayload()->get('firstname'),
                    'servicename' => $serviceName[$bo->serviceId]
                );

                Log::info($notify);
                $mobileNumbers = SendSmsService::getMobleNumber([$notify]);
                if (sizeof($mobileNumbers) > 0) {
                    $userID = JWTAuth::parseToken()->getPayload()->get('id');
                    $job = new SendBuyerPostSMSAlert($mobileNumbers, BUYER_CREATED_POST_FOR_SELLERS_SMS, $msg_params, $userID);
                    $this->dispatch($job);
                    //  SendSmsService::shpSendSMS($getMobileNumber,BUYER_CREATED_POST_FOR_SELLERS_SMS,$msg_params);
                }
                //*******Send Sms to the private Sellers***********************//

            } else {
                throw new ApplicationException(["BuyerPostId" => $bo->buyerPostId, "buyerId" => $bo->buyerId], [2, self::$SVCERRORS[2]]);
            }
            $response = $bo;
            LOG::info('Save To Search Store');
            if ($bo->status == "Final Offer" || $bo->status == "'Firm Offer" || $bo->status == "'L1 Offer" || $bo->status == "final_offer" || $bo->status == "firm_offer" || $bo->status == "l1_offer")
                $this->dispatch(new SyncSellerQuotes2SearchStore($bo));

        } catch (Exception $e) {
            echo 'Exception from : ', $e->getMessage(), "\n";
            throw new ApplicationException(["Problem while saving Buyer Post"], [$e->getMessage() . 'for postId =>  ' . $bo->postId]);
        }
        return $response;
    }

    public function getSellerOffersByBuyerPostId($id)
    {
        $response = array();
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        if (JWTAuth::parseToken()->getPayload()->get('role') == "Seller") {
            $sellerPost = SellerQuotes::where('buyerPostId', '=', $id)
                ->where('sellerId', '=', $userId)
                ->get()->first();
        } else {
            //Load model object
            $sellerPost = SellerQuotes::where('buyerPostId', '=', $id)->get()->toArray();

        }


        //Get Transformer and transform retrieved object to bo from model
        $transformer = $this->serviceFactory->makeTransformer();
        $bo = $transformer->model2boGet($sellerPost);

        $response = $bo;
        return $response;
    }

    public function getTermSellerOffersByBuyerPostId($id)
    {
        $response = array();
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        //Get Transformer and transform retrieved object to bo from model
        $transformer = $this->serviceFactory->makeTransformer();
        if (JWTAuth::parseToken()->getPayload()->get('role') == "Seller") {
            $sellerPost = SellerQuotes::where('buyerPostId', '=', $id)
                ->where('sellerId', '=', $userId)
                ->get()->toArray();
            $bo = $transformer->model2boGet($sellerPost);
        } else {
            //Load model object

            //$sellerPost = SellerQuotes::where('buyerPostId', '=', $id)->get()->toArray();
            $sellerPost = $this->computeSellerRanks($id);
            if (sizeof($sellerPost) > 0)
                $bo = $sellerPost;
            else
                $bo = [];
        }

        $response = $bo;
        return $response;
    }

    public static function computeSellerRanks($buyerPostId)
    {

        $querySellerOffers = "
        
          select *, loadPort, dischargePort, sellerId, convert(totalFreightCharges, unsigned) totalFreightCharges 
          from shp_seller_quotes 
          where buyerPostId = ? and (loadPort, dischargePort) NOT IN 
          (SELECT loadPort, dischargePort FROM shp_contract_items i, shp_contract c where i.contractId = c.id and c.buyerPostId = ?)
          order by loadPort asc, dischargePort asc, convert(totalFreightCharges, unsigned) desc
        
        ";

        $rows = DB::select($querySellerOffers, [$buyerPostId, $buyerPostId]);

        $counter = 0;
        $rank = 1;
        $loadPort = "";
        $dischargePort = "";
        $sellerId = 0;
        $totalFreightCharges = 0;

        // [ Seller1 => [ L1 => [ Port Pair Array ], L2 => [ Port Pair Array ] ], Seller2 => [ L1 => [ Port Pair Array ], L2 => [ Port Pair Array ] ]
        $allSellerRanks = [];
        $totalPortPairs = '';
        foreach ($rows as $row) {

            if ($counter == 0) {

                //initialize all variables
                $loadPort = $row->loadPort;
                $dischargePort = $row->dischargePort;
                $sellerId = $row->sellerId;
                $totalFreightCharges = $row->totalFreightCharges;

                $rank = 1;
                $counter = $counter + 1;

            }

            if ($loadPort !== $row->loadPort || $dischargePort !== $row->dischargePort) {

                //The port pair has changed. Create new rankings.
                $rank = 1;

            } elseif ($totalFreightCharges !== $row->totalFreightCharges) {

                //Freight charges have changed. Set a new rank.
                $rank = $rank + 1;
            }


            //Store computed rank against the seller

            $allSellerRanks = self::captureRank($rank, $row->sellerId, $row->loadPort,
                $row->dischargePort, $allSellerRanks, $row);

            //reset all variables
            $loadPort = $row->loadPort;
            $dischargePort = $row->dischargePort;
            $sellerId = $row->sellerId;
            $totalFreightCharges = $row->totalFreightCharges;


        }

        //$allSellerRanks = self::captureRank($rank, $sellerId, $loadPort, $dischargePort, $allSellerRanks);

        return $allSellerRanks;

    }

    private static function captureRank($rank, $sellerId, $loadPort, $dischargePort, $allSellerRanks, $row)
    {

        //Capture ranks and arrange in the form below.
        // [ Seller1 => [ L1 => [ Port Pair Array ], L2 => [ Port Pair Array ] ], Seller2 => [ L1 => [ Port Pair Array ], L2 => [ Port Pair Array ] ]
        $quoteInfo = [];
        $rankLabel = 'L' . $rank;
        $totalPortPairs = 0;
        $quotedPortPairs = SellerQuotes::where('buyerPostId', $row->buyerPostId)
            ->where('sellerId', $sellerId)
            ->get()->toArray();
        $result = BuyerPost::where('id', '=', $row->buyerPostId)->firstOrFail();

        if (!empty($result)) {
            $sellerPost = json_decode($result->attributes)->serviceType[0]->routes;
            $totalPortPairs = sizeof($sellerPost);
        }
        if (!isset($allSellerRanks[$sellerId])) {
            //This seller does not exists. Append a new element for this seller
            $allSellerRanks[$sellerId] = [];
        }
        //dd($allSellerRanks);
        $sellerRanks = $allSellerRanks[$sellerId];

        if (!isset($sellerRanks['attributes'][$rankLabel])) {
            //This rank label does not exist for this seller. Append a new element for this seller
            $sellerRanks['attributes'][$rankLabel] = [];
        }

        $portPairs = $sellerRanks['attributes'][$rankLabel];

        $quoteInfo['quoteId'] = $row->id;
        $quoteInfo['isContractGenerated'] = $row->isContractGenerated;
        $quoteInfo['loadPort'] = $row->loadPort;
        $quoteInfo['dischargePort'] = $row->dischargePort;
        $quoteInfo['attributes'] = json_decode($row->attributes); //For Negotiable Term Counter Offer.
        $quoteInfo['status'] = $row->status;
        $quoteInfo['containers'] = json_decode($row->attributes)->containers; //self::getContainersList($row);//
        $quoteInfo['commodity'] = json_decode($row->attributes)->commodity;
        $quoteInfo['totalFreightCharges'] = $row->totalFreightCharges;
        $quoteInfo['rank'] = $rankLabel;
        //$quoteInfo['totalFreightCharges'] = $row->status;

        array_push($portPairs, $quoteInfo);
        $sellerRanks['buyerPostId'] = $row->buyerPostId;
        $sellerRanks['serviceId'] = $row->serviceId;
        $sellerRanks['sellerId'] = $row->sellerId;
        $sellerRanks['sellerName'] = UserDetailsService::getUserDetails($row->sellerId)->username;
        $sellerRanks["isSellerAccepted"] = $row->isSellerAccepted;
        $sellerRanks["isBuyerAccepted"] = $row->isBuyerAccepted;
        $sellerRanks["contractStatus"] = self::getContractStatuts($row);//$value["isBuyerAccepted"];
        $sellerRanks["status"] = $row->status;
        $sellerRanks["awardType"] = $row->awardType;
        $sellerRanks["quotedPortPairs"] = sizeof($quotedPortPairs) . "/" . $totalPortPairs;
        $sellerRanks["percentage"] = (sizeof($quotedPortPairs) / $totalPortPairs) * 100;
        $sellerRanks['attributes'][$rankLabel] = $portPairs;

        //$sellerRanks["attributes"] = [];

        $allSellerRanks[$sellerId] = $sellerRanks;

        return $allSellerRanks;


    }

    private static function getContractStatuts($items)
    {
        $postId = $items->buyerPostId;
        $buyerId = JWTAuth::parseToken()->getPayload()->get('id');
        $buyerContractedContainers = BuyerContract::where('buyerPostId', $postId)
            ->where('sellerId', $items->sellerId)
            ->where('buyerId', $buyerId)->select('status')->get()->toArray();
        if (sizeof($buyerContractedContainers) > 0)
            return $buyerContractedContainers[0]["status"];
        else
            return "";
    }

    public function getQuoteDetailsByQuoteId($quoteId)
    {

        $sellerQuoteDetails = SellerQuotes::find($quoteId);
        if (!$sellerQuoteDetails) {
            throw new ApplicationException(
                ["quoteId" => $quoteId],
                ["Quote details doesnt exists"]
            );
        }
        $bo = $sellerQuoteDetails->toArray();
        $bo["attributes"] = json_decode($bo["attributes"]);

        $response = $bo;
        return $response;
    }

    public function getSellerInboundBuyerPosts()
    {
        $buyerIds = $response = array();

        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        //Load model object
        $buyerPosts = SelectedSellers::where('seller_id', '=', $userId)->select('post_id')->get()->toArray();

        foreach ($buyerPosts as $val) {
            $buyerIds[] = $val['post_id'];
        }

        //Get Transformer and transform retrieved object to bo from model
        $transformer = $this->serviceFactory->makeTransformer();
        $bo = $transformer->model2boGetBuyerPost($buyerIds);

        $response = $bo;
        return $response;
    }


}