<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 01-02-2017
 * Time: 16:44
 */

namespace ApiV2\Services;

use ApiV2\BusinessObjects\BuyerPostBO;
use ApiV2\BusinessObjects\BuyerPostMasterInboundGroup;
use ApiV2\BusinessObjects\BuyerPostMasterInboundResults;
use ApiV2\BusinessObjects\BuyerPostSearchBO;
use ApiV2\BusinessObjects\ContractBO;
use ApiV2\BusinessObjects\PostMasterGroup;
use ApiV2\Model\BuyerContract;
use ApiV2\Model\BuyerPost;
use ApiV2\Model\ContractItems;
use ApiV2\Model\SelectedSellers;
use ApiV2\Model\SellerQuotes;
use ApiV2\Repositories;
use ApiV2\Requests\Containers;
use ApiV2\Requests\Routes;
use ApiV2\Services\IBuyerPost;
use ApiV2\Utils\LoggingServices;
use App\ApiV2\Events\BuyerPostCreated;
use App\Exceptions\ApplicationException;
use App\Exceptions\ServiceException;
use App\Jobs\SendBuyerPostSMSAlert;
use App\Jobs\SendEmailAlert;
use DB;
use Illuminate\Foundation\Bus\DispatchesJobs;
use League\Flysystem\Exception;
use Log;
use Tymon\JWTAuth\Facades\JWTAuth;

//use ApiV2\Services\EmailService;

class BuyerPostService extends BaseService implements IBuyerPostService
{

    use DispatchesJobs;

    /**
     * @var IBuyerPost
     */
    private $serviceFactory;

    public function __construct()
    {
    }

    public function setServiceFactory($factory)
    {
        $this->serviceFactory = $factory;
    }

    /*
     * Get All Buyer Posts
     *
     */
    public function getAllPosts()
    {
        $response = array();

        //get Authorizer
        $authorizer = $this->serviceFactory->makeAuthorizer();

        //Load model object
        $userId = JWTAuth::parseToken()->getPayload()->get('id');

        $sellerPost = BuyerPost::where('buyerId', '=', $userId)->get()->toArray();

        //Get Transformer and transform retrieved object to bo from model
        $transformer = $this->serviceFactory->makeTransformer();
        $bo = $transformer->model2boGetAll($sellerPost);
        $response = $bo;
        return $response;

    }

    /**
     * @param $id
     * @return array
     */
    public function getPostById($id)
    {
        $response = array();

        //get Authorizer
        $authorizer = $this->serviceFactory->makeAuthorizer();
        $userId = JWTAuth::parseToken()->getPayload()->get('id');

        //Load model object
        $buyerPost = BuyerPost::where('id', '=', $id)->firstOrFail();

        if ($userId != $buyerPost["attributes"]["buyerId"]) {
            //TODO : How do we ensure the viewcount is not increment when the same buyer watches again and again
            //TODO : View count may be wrong. This gets increments whenever seller or buyer watches this.
            $buyerPost->viewCount = (int)$buyerPost->viewCount + 1;
            $buyerPost->save();
        }

        //Get Transformer and transform retrieved object to bo from model
        $transformer = $this->serviceFactory->makeTransformer();
        $bo = $transformer->model2boGet($buyerPost);

        $response = $bo;
        return $response;
    }

    public function getGeneratedContractsByPostId($id)
    {
        $response = array();

        $userId = JWTAuth::parseToken()->getPayload()->get('id');

        //Load model object
        //TODO: This is under development
        $buyerContract = BuyerContract::where('buyerPostId', '=', $id)
            //->where('isSellerAccepted', '=', 1)
            ->where('sellerId', '=', $userId)
            ->select('*')->get()->toArray();

        if (sizeof($buyerContract) > 0) {
            for ($i = 0; $i < sizeof($buyerContract); $i++) {
                $buyerContract[$i]["attributes"] = json_decode($buyerContract[$i]["attributes"]);
            }
        }

        $bo = $buyerContract;

        $response = $bo;
        return $response;
    }

    public function getGeneratedContractsByTermContractId($id)
    {
        $buyerPostContractObj = new BuyerContract();
        //Load model object
        $buyerContract = $buyerPostContractObj->getContractsById($id);
        if (count($buyerContract)) {
            $buyerContract = $buyerContract->toArray();
            $buyerContract["attributes"] = json_decode($buyerContract["attributes"]);
            if (
                isset($buyerContract["post_details"])
                && count($buyerContract["post_details"])
                && isset($buyerContract["post_details"]['attributes'])
                && count($buyerContract["post_details"]['attributes'])
            ) {
                $buyerContract["post_details"]["attributes"] = json_decode($buyerContract["post_details"]["attributes"]);
            }
            if (
                isset($buyerContract["order_details"])
                && count($buyerContract["order_details"])
            ) {
                foreach ($buyerContract["order_details"] as $key => $value) {
                    if (isset($buyerContract["order_details"][$key]['attributes'])
                        && count($buyerContract["order_details"][$key]['attributes'])
                    ) {
                        $buyerContract["order_details"][$key]["attributes"] = json_decode($buyerContract["order_details"][$key]["attributes"]);
                    }
                }
            }
        }

        if (isset($buyerContract["order_details"])) {
            foreach ($buyerContract["attributes"]->portPairs as $eachPortPairsKey => $eachPortPairs) {
                $buyerContract["attributes"]->portPairs[$eachPortPairsKey]->noOfContainersPlaced = 0;
                foreach ($buyerContract["order_details"] as $eachOrder) {
                    if ($eachOrder['load_port'] == $eachPortPairs->loadPort
                        && $eachOrder['discharge_port'] == $eachPortPairs->dischargePort
                    ) {
                        foreach ($eachOrder["attributes"]->containers as $eachContainers) {
                            if ($eachContainers->containerType == $eachPortPairs->containerType) {
                                $buyerContract["attributes"]->portPairs[$eachPortPairsKey]->noOfContainersPlaced +=
                                    $buyerContract["attributes"]->portPairs[$eachPortPairsKey]->quantity;
                            }
                        }
                    }
                }
            }
        }
        unset($buyerContract["order_details"]);

        $bo = $buyerContract;
        return $bo;
    }

    /**
     * Retreive All Spot Posts By Current Buyer.
     *
     */
    public function getAllSpotPosts()
    {
        $response = array();

        //get Authorizer
        $authorizer = $this->serviceFactory->makeAuthorizer();

        //Load model object
        $userId = JWTAuth::parseToken()->getPayload()->get('id');

        $sellerPost = BuyerPost::where('buyerId', '=', $userId)
            ->where('leadType', '=', "spot")
            ->get()->toArray();

        //Get Transformer and transform retrieved object to bo from model
        $transformer = $this->serviceFactory->makeTransformer();
        $bo = $transformer->model2boGetAll($sellerPost);
        $response = $bo;
        return $response;
    }

    /**
     * Retreive All Term Posts By Current Buyer.
     *
     */
    public function getAllTermPosts()
    {

        $response = array();

        //get Authorizer
        $authorizer = $this->serviceFactory->makeAuthorizer();

        //Load model object
        $userId = JWTAuth::parseToken()->getPayload()->get('id');

        $sellerPost = BuyerPost::where('buyerId', '=', $userId)
            ->where('leadType', '=', "term")
            ->get()->toArray();

        //Get Transformer and transform retrieved object to bo from model
        $transformer = $this->serviceFactory->makeTransformer();
        $bo = $transformer->model2boGetAll($sellerPost);
        $response = $bo;
        return $response;

    }

    /**
     * Retreive All Posts By Post Privacy.
     *
     */
    public function getAllPostsByPostPrivacy($postType)
    {

        $response = array();
        $postType = ($postType == 'public') ? 1 : 0;

        //get Authorizer
        $authorizer = $this->serviceFactory->makeAuthorizer();

        //Load model object
        $userId = JWTAuth::parseToken()->getPayload()->get('id');


        $sellerPost = BuyerPost::where('buyerId', '=', $userId)
            ->where('isPublic', '=', $postType)
            ->get()->toArray();

        //Get Transformer and transform retrieved object to bo from model
        $transformer = $this->serviceFactory->makeTransformer();
        $bo = $transformer->model2boGetAll($sellerPost);
        $response = $bo;
        return $response;
    }

    /**
     * Methos either to create or update the term post
     * @param BuyerPostBO $bo
     * @param bool $transactional
     * @return BuyerPostBO|null
     * @throws ApplicationException
     */
    public function saveOrUpdateTerm(BuyerPostBO $bo, $transactional = true)
    {

        Log::info("Saving BuyerPost Term");
        $serviceName = unserialize(SHIPPING_MODULES);
        Log::info($serviceName[$bo->serviceId]);

        $response = null;
        $isNewPost = false;

        try {
            if (!trim($bo->postId)) {
                $isNewPost = true;
                LOG::info('Creating new buyer post with title [' . $bo->title . '] for buyer [' . $bo->buyerId . ']');
            } else {
                LOG::info('Updating existing buyer post [' . $bo->postId . '] for buyer [' . $bo->buyerId . ']');
            }

            LOG::info('$bo->postId => ' . $bo->postId);

            LOG::info('Verifying if request is authorized');
            $authorizer = $this->serviceFactory->makeAuthorizer()->authorizeSave($bo);

            LOG::info('Validate the request');
            $this->serviceFactory->makeValidator()->validateSave($bo);

            if ($transactional) {
                //no parent transaction. start a new one here
                DB::beginTransaction();
            }

            //Save model object
            if ($isNewPost) {
                $model = new BuyerPost();
                $logginEntity = $serviceName[$bo->serviceId] . BUYER_ADDED_NEW_QUOTE;
            } else {
                $model = BuyerPost::findOrFail($bo->postId);
                $logginEntity = $serviceName[$bo->serviceId] . BUYER_UPDATE_QUOTE;
                LOG::debug('Model Update');
            }

            LOG::debug('Before Buyer Post save()');

            $model = $this->bo2model($bo, $model);
            $isSaved = $model->save();

            LOG::debug('Model Buyer Post Object Saved  =>' . $isSaved);
            LOG::info('PostId Generated  =>' . $model->id);
            LOG::debug($model);

            if (trim($isSaved)) {

                Log::info("BuyerPost Saved Successfully.");
                $trans_randid = $serviceName[$bo->serviceId] . '/' . date('Y') . '/' . str_pad($model->id, 6, 0, STR_PAD_LEFT);
                $bo->postId = $model->id;
                $bo->version = $model->version;
                $bo->transactionId = $trans_randid;
                $visibleToSellers = $bo->visibleToSellers;
                $model->transactionId = $trans_randid;

                //Save Selected sellers
                if (sizeof($visibleToSellers) > 0) {
                    LOG::info('Inserting Selected Sellers into mapping table');
                    $this->saveSelectedSellers($visibleToSellers, $model->id);
                }
                $model->save();
                $response = $bo;

                //Call Indexer to commit buyerposts into Buyer Post Indexer
                $indexer = $this->serviceFactory->makeIndexer();
                $isRebuild = $indexer->rebuildIndex($bo);
                LOG::info('Saved To BuyerPost Index table');

                if ($transactional) {
                    DB::commit();
                }
                //Push to notifications table
                event(new BuyerPostCreated($bo));

                //Push to log tables activity and audit logs
                LoggingServices::activityLog($logginEntity, $logginEntity, 0, HTTP_REFERRER, CURRENT_URL, $model->serviceId);
                LoggingServices::auditLog($model->id, $logginEntity, json_encode($response));

                //Send  private emails to buyer selected sellers
                $event = SELLER_CREATED_POST_FOR_BUYERS;
                $emailInfo = ['buyername' => JWTAuth::parseToken()->getPayload()->get('firstname'), 'randnumber' => $trans_randid];

                $job = new SendEmailAlert((array)$visibleToSellers, $emailInfo, $event);
                $this->dispatch($job);
                LOG::info('Dispatched Email Notification');

                //Send Sms to the private Sellers
                $msg_params = array(
                    'randnumber' => $trans_randid,
                    'buyername' => JWTAuth::parseToken()->getPayload()->get('firstname'),
                    'servicename' => $serviceName[$bo->serviceId]
                );

                $mobileNumbers = SendSmsService::getSellerMobileNumbers((array)$visibleToSellers);

                if (sizeof($mobileNumbers) > 0) {
                    $userID = JWTAuth::parseToken()->getPayload()->get('id');
                    $job = new SendBuyerPostSMSAlert($mobileNumbers, BUYER_CREATED_POST_FOR_SELLERS_SMS, $msg_params, $userID);
                    $this->dispatch($job);
                    LOG::info('Dispatched SMS Notification');
                }

                //Call Sorl Delta Import
                $service = new SolrSearchService();
                $service->deltaImport("buyerposts");

                LOG::info('Called Solr Delta Import to push Term post to Solr');

            } else {
                throw new ServiceException("Buyerpost could not be saved");
            }

            $response = $bo;

        } catch (Exception $e) {

            LOG::error("Buyerpost could not be saved", (array)$e->getMessage());

            if ($transactional) {
                DB::rollBack();
            }

            $this->handle($e);

        }

        return $response;

    }

    private function bo2model(BuyerPostBO $bo, $model)
    {
        $now = date('Y-m-d H:i:s');
        if (!empty($bo->postId)) {
            $model->updatedBy = JWTAuth::parseToken()->getPayload()->get('id');
            $model->updatedIP = $_SERVER['REMOTE_ADDR'];
            $model->updated_at = $now;
            $model->version = (int)$model->version + 1;
        } else {
            $model->createdBy = JWTAuth::parseToken()->getPayload()->get('id');//SecurityPrincipal::getUserId();
            $model->updatedBy = $_SERVER['REMOTE_ADDR'];
            $model->created_at = $now;
            $model->version = 1;
        }
        $model->status = $bo->status;
        $model->buyerId = $bo->buyerId;
        $model->title = $bo->title;
        $model->serviceId = $bo->serviceId;
        //$model->serviceSubType= $bo->serviceSubType;
        $model->leadType = $bo->leadType;
        $model->lastDateTimeOfQuoteSubmission = $bo->lastDateTimeOfQuoteSubmission;
        $model->isPublic = $bo->isPublic;
        $model->isTermAccepted = $bo->isTermAccepted;
        //$model->originLocation= $bo->originLocation;
        //$model->destinationLocation= $bo->destinationLocation;
        $model->syncSearch = false;
        $model->syncLeads = false;
        $model->attributes = json_encode((array)$bo->attributes);
        return $model;
    }

    private function saveSelectedSellers($visibleToSellers, $buyerPost_id)
    {
        $selectedSeller = array();
        $now = date('Y-m-d H:i:s');

        if (SelectedSellers::where('post_id', '=', $buyerPost_id)->exists()) {
            SelectedSellers::where('post_id', '=', $buyerPost_id)->delete();
        }
        for ($i = 0; $i < sizeof($visibleToSellers); $i++) {
            $selectedSeller[$i]['post_id'] = $buyerPost_id;
            $selectedSeller[$i]['seller_id'] = $visibleToSellers[$i];
            $selectedSeller[$i]['created_at'] = $now;
            $selectedSeller[$i]['updated_at'] = $now;
        }
        SelectedSellers::insert($selectedSeller);
        return true;

    }

    /**
     * @param array $bos
     * @return array
     */
    public function saveOrUpdateSpots(array $bos)
    {

        Log::info("Saving many buyerpost spots");

        $response = array();

        try {

            //start a transaction here
            DB::beginTransaction();

            foreach ($bos as $bo) {
                $response[] = $this->saveOrUpdateSpot($bo);
            }

            DB::commit();

        } catch (\Exception $e) {

            DB::rollBack();

            $this->handle($e);

        }


        return $response;
    }

    /*
     * Buyer Contract Save
     *
     */

    /**
     * @param BuyerPostBO $bo
     * @param bool $transactional
     * @return BuyerPostBO|null
     * @throws ApplicationException
     */
    public function saveOrUpdateSpot(BuyerPostBO $bo)
    {
        $serviceName = unserialize(SHIPPING_MODULES);
        $response = null;
        $isNewPost = false;

        if (!trim($bo->postId)) {
            $isNewPost = true;
            LOG::info('Creating new buyer post with title [' . $bo->title . '] for buyer [' . $bo->buyerId . ']');
        } else {
            LOG::info('Updating existing buyer post [' . $bo->postId . '] for buyer [' . $bo->buyerId . ']');
        }

        LOG::info('$bo->postId => ' . $bo->postId);

        LOG::info('Verifying if request is authorized');
        $authorizer = $this->serviceFactory->makeAuthorizer()->authorizeSave($bo);

        LOG::info('Validate the request');
        $errors = $this->serviceFactory->makeValidator()->validateSave($bo);

        //Save model object
        if ($isNewPost) {
            $model = new BuyerPost();
            $logginEntity = $serviceName[$bo->serviceId] . BUYER_ADDED_NEW_QUOTE;
        } else {
            $model = BuyerPost::findOrFail($bo->postId);
            $logginEntity = $serviceName[$bo->serviceId] . BUYER_UPDATE_QUOTE;
            LOG::info('Model Update');
        }
        LOG::info('Before Saving Buyer Post');
        $model = $this->bo2model($bo, $model);
        $isSaved = $model->save();

        LOG::debug('Model Buyer Post Object Saved  =>' . $isSaved);
        LOG::info('PostId Generated  =>' . $model->id);
        LOG::debug($model);

        if (trim($isSaved)) {
            Log::info("BuyerPost Saved successfully.");
            //Save Selected sellers
            $trans_randid = $serviceName[$bo->serviceId] . '/' . date('Y') . '/' . str_pad($model->id, 6, 0, STR_PAD_LEFT);
            $bo->postId = $model->id;
            $bo->version = $model->version;
            $bo->transactionId = $trans_randid;
            $visibleToSellers = $bo->visibleToSellers;
            $model->transactionId = $trans_randid;
            if (sizeof($visibleToSellers) > 0) {
                LOG::info('Inserting Selected Sellers into mapping table');
                $this->saveSelectedSellers($visibleToSellers, $model->id);
            }
            $model->save();
            Log::info("Saved Selected Sellers =>");
            Log::info($visibleToSellers);
            $response = $bo;

            //Call Indexer to commit buyerposts into Buyer Post Indexer
            $indexer = $this->serviceFactory->makeIndexer();
            $isRebuild = $indexer->rebuildIndex($bo);
            LOG::info('Saved To BuyerPostIndex table');

            //All other operations such as Notifications, Mail sending etc to be after the Commit().
            LoggingServices::activityLog($logginEntity, ENTITY_BUYER_POST, 0, HTTP_REFERRER, CURRENT_URL, $model->serviceId);
            LoggingServices::auditLog($model->id, ENTITY_BUYER_POST, json_encode($response));

            LOG::info('Entry made to ActivityLog and AuditLog.');

            //TODO Notifications
            event(new BuyerPostCreated($bo));
            LOG::info('Entry made to Notification table');

            //Send email privately to buyer selected sellers
            $event = BUYER_CREATED_POST_FOR_SELLERS;
            $emailInfo = ['buyername' => JWTAuth::parseToken()->getPayload()->get('firstname'), 'randnumber' => $trans_randid];

            $job = new SendEmailAlert((array)$visibleToSellers, $emailInfo, $event);
            $this->dispatch($job);

            LOG::info('Dispatched Email Notification');

            //*******Send Sms to the private Sellers***********************//
            $msg_params = array(
                'randnumber' => $trans_randid,
                'buyername' => JWTAuth::parseToken()->getPayload()->get('firstname'),
                'servicename' => $serviceName[$bo->serviceId]
            );

            $mobileNumbers = SendSmsService::getSellerMobileNumbers((array)$visibleToSellers);
            // dd($mobileNumbers);
            if (sizeof($mobileNumbers) > 0) {
                $userID = JWTAuth::parseToken()->getPayload()->get('id');
                $job = new SendBuyerPostSMSAlert($mobileNumbers, BUYER_CREATED_POST_FOR_SELLERS_SMS, $msg_params, $userID);
                $this->dispatch($job);
                LOG::info('Dispatched SMS Notification ');
            }
            LOG::info('No SMS Notification dispatched');

            //Call Sorl Delta Import
            $service = new SolrSearchService();
            $service->deltaImport("buyerposts");

            LOG::info('Called Solr Delta Import to push buyerpost(s) to Solr');

        } else {

            LOG::error('Error while saving Buyer Spot Post');

            throw new ServiceException("Buyerpost spot could not be saved");
        }

        $response = $bo;
        return $response;
    }

    public function saveGenerateContract(ContractBO $bo)
    {
        $serviceName = unserialize(SHIPPING_MODULES);
        $response = null;
        $isNewPost = false;

        try {
            if (!trim($bo->id)) {
                $isNewPost = true;
                LOG::info('Creating new buyer Contract with title [' . $bo->title . '] for buyer [' . $bo->buyerId . ']');
            }

            LOG::info('Validate the request');
            $errors = $this->serviceFactory->makeValidator()->validateContractSave($bo);

            DB::beginTransaction(); //Start transaction!DB::beginTransaction();
            //Save model object
            if ($isNewPost) {
                $model = new BuyerContract();
                $logginEntity = $serviceName[$bo->serviceId] . BUYER_ADDED_NEW_Contract;
            } else {
                $model = BuyerContract::findOrFail($bo->id);
                $logginEntity = $serviceName[$bo->serviceId] . BUYER_UPDATE_QUOTE;
                LOG::info('Model Update');
            }

            LOG::debug('Before Buyer Contract Save()');
            $model = $this->contractBo2model($bo, $model);
            $isSaved = $model->save();

            LOG::info('Model Buyer Contract Object Saved  =>' . $isSaved);
            LOG::info('ContractId Generated  =>' . $model->id);

            if (trim($isSaved)) {
                $bo->id = $model->id;
                $this->saveContractItems($bo->portPairs, $model->id);
                // LoggingServices::auditLog($model->id, BUYER_ADDED_NEW_QUOTE, $model);
                $this->updateQuoteIds($bo);
                Log::info("BuyerContract Saved Successfully.");
                if ($bo->documentId != "")
                    DocumentService::link($bo->documentId, ENTITY_CONTRACT, $model->id);

                DB::commit();
                $response = $bo;

                LoggingServices::activityLog(ENTITY_CONTRACT, $logginEntity, 0, HTTP_REFERRER, CURRENT_URL, $model->serviceId);
                LoggingServices::auditLog($model->id, ENTITY_CONTRACT, json_encode($response));
                $trans_randid = $serviceName[$bo->serviceId] . '/' . date('Y') . '/' . str_pad($model->id, 6, 0, STR_PAD_LEFT);


                //Send private email
                /*$event = BUYER_CONTRACT_SAVED;
                $emailInfo = ['buyername'=>JWTAuth::parseToken()->getPayload()->get('firstname')];
                $sendTo = $bo->sellerId;
                $job = new SendEmailAlert( (array) $sendTo,$emailInfo,$event);
                $this->dispatch($job);

                LOG::info('Dispatched Email Notification');
*/

                //*******Send Sms to the private Sellers***********************//
                /*  $msg_params = array(
                      'randnumber' => $trans_randid,
                      'buyername' => JWTAuth::parseToken()->getPayload()->get('firstname'),
                      'servicename' => $serviceName[$bo->serviceId]
                  );

                  $mobileNumbers  =   SendSmsService::getSellerMobileNumbers((array) $sendTo);
                  if(sizeof($mobileNumbers)>0){
                     $userID = JWTAuth::parseToken()->getPayload()->get('id');
                      $job = new SendBuyerPostSMSAlert($mobileNumbers, BUYER_CREATED_POST_FOR_SELLERS_SMS, $msg_params, $userID);
                      $this->dispatch($job);
                      LOG::info('Dispatched SMS Notification ');
                  }*/

            } else {
                throw new ServiceException("Contract could not be created");
            }

            $response = $bo;

        } catch (Exception $e) {

            LOG::error("Failed creating contract", (array)$e->getMessage());

            DB::rollBack();

            $this->handle($e);
        }


        return $response;


    }

    private function contractBo2model(ContractBO $bo, $model)
    {
        LOG::info('ContractBo2model Start');
        $now = date('Y-m-d H:i:s');
        if (!empty($bo->id)) {
            $model->updatedBy = JWTAuth::parseToken()->getPayload()->get('id');
            $model->updatedIP = $_SERVER['REMOTE_ADDR'];
            $model->updated_at = $now;
        } else {
            $model->createdBy = JWTAuth::parseToken()->getPayload()->get('id');//SecurityPrincipal::getUserId();
            $model->updatedBy = $_SERVER['REMOTE_ADDR'];
            $model->created_at = $now;
        }
        $model->title = $bo->title;
        $model->buyerPostId = $bo->buyerPostId;
        $model->serviceId = $bo->serviceId;
        $model->buyerId = $bo->buyerId;
        $model->sellerId = $bo->sellerId;
        $model->validFrom = $bo->validFrom;
        $model->validTo = $bo->validTo;
        //$model->isSellerAccepted= $bo->isSellerAccepted;
        $model->status = $bo->status;
        $model->awardType = $bo->awardType;
        //$model->attributes = json_encode((array)$bo->attributes);
        LOG::info('ContractBo2model End ');
        return $model;
    }

    private function saveContractItems($portPairs, $contractId)
    {

        $contractArray = [];

        for ($i = 0; $i < sizeof($portPairs); $i++) {
            $contractArray[$i]['contractId'] = $contractId;
            $contractArray[$i]['commodity'] = $portPairs[$i]->commodity;
            $contractArray[$i]['loadPort'] = $portPairs[$i]->loadPort;
            $contractArray[$i]['dischargePort'] = $portPairs[$i]->dischargePort;
            $contractArray[$i]['containerType'] = $portPairs[$i]->containerType;
            $contractArray[$i]['quantity'] = $portPairs[$i]->quantity;
            $contractArray[$i]['attributes'] = json_encode((array)$portPairs[$i]->charges);
        }

        ContractItems::insert($contractArray);
        return true;

    }


    /*
     * Save Buyer sellected Sellers
     */

    private function updateQuoteIds(ContractBO $bo)
    {

        $portPairs = $bo->portPairs;
        $quoteIds = [];

        foreach ($portPairs as $portPair) {
            SellerQuotes::where('buyerPostId', $bo->buyerPostId)->where('loadPort', $portPair->loadPort)->where('dischargePort', $portPair->dischargePort)->update(['isContractGenerated' => true]);
        }

        return true;
    }

    public function filterPost(BuyerPostSearchBO $bo)
    {

        LOG::info('Buyer post search invoked with criteria');

        LOG::debug((array)$bo);

        //Get the searcher and delegate the search request
        $results = $this->serviceFactory->makeIndexer()->searchIndex($bo);

        return $results;

        LOG::info('Buyer post search finished');

    }


    public function postMasterFilters(BuyerPostSearchBO $bo)
    {

        LOG::info('Buyer Post Master invoked with criteria');

        //Get the searcher and delegate the search request
        $results = $this->serviceFactory->makeIndexer()->postMasterIndex($bo);
        return $results;


    }

    public function filterPostMasterInbound(BuyerPostSearchBO $bo)
    {

        LOG::info('Buyer Post Master Inbound invoked with criteria');

        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $millis6MonthsAgo = (new \DateTime("-6 months"))->getTimestamp();

        try {

            $relatedSpotEnquiriesSql = "
                  select  
                   spi.postId 
                  ,group_concat(distinct spi.sellerName) seller_name
                  ,group_concat(distinct spi.loadPort) load_port
                  ,group_concat(distinct spi.dischargePort) discharge_port
                  ,group_concat(distinct spi.containerType) container_type 
                  from intra_hp_order_items oi, shp_seller_post_index spi
                  where oi.buyer_id = spi.discountBuyerId
                    and oi.lead_type = 'spot'
                    and oi.load_port = spi.loadPort
                    and oi.discharge_port = spi.dischargePort
                    and oi.created_at >= ?
                    and oi.buyer_id = ?
                    group by spi.postId
                 ";

            $partlyRelatedSpotEnquiriesSql = "
                  select
                     spi.postId 
                  ,group_concat(distinct spi.sellerName) seller_name
                  ,group_concat(distinct spi.loadPort) load_port
                  ,group_concat(distinct spi.dischargePort) discharge_port
                  ,group_concat(distinct spi.containerType) container_type                  
                  from intra_hp_order_items oi, shp_seller_post_index spi
                  where oi.buyer_id = spi.discountBuyerId
                    and oi.lead_type = 'spot'
                    and oi.load_port = spi.loadPort
                    and oi.discharge_port != spi.dischargePort
                    and oi.created_at >= ?
                    and oi.buyer_id = ?
                    group by spi.postId
                 ";

            $unrelatedSpotEnquiriesSql = "
                  select
                       spi.postId 
                  ,group_concat(distinct spi.sellerName) seller_name
                  ,group_concat(distinct spi.loadPort) load_port
                  ,group_concat(distinct spi.dischargePort) discharge_port
                  ,group_concat(distinct spi.containerType) container_type                   
                  from intra_hp_order_items oi, shp_seller_post_index spi
                  where oi.buyer_id = spi.discountBuyerId
                    and oi.lead_type = 'spot'
                    and oi.load_port != spi.loadPort
                    and oi.discharge_port != spi.dischargePort
                    and oi.created_at >= ?
                    and oi.buyer_id = ?
                    group by spi.postId
                 ";

            $relatedSpotLeadsSql = "
                  select
                   spi.postId 
                  ,group_concat(distinct spi.sellerName) seller_name
                  ,group_concat(distinct spi.loadPort) load_port
                  ,group_concat(distinct spi.dischargePort) discharge_port
                  ,group_concat(distinct spi.containerType) container_type                  
                    from intra_hp_order_items oi, shp_seller_post_index spi
                    where spi.discountBuyerId is NULL
                      and oi.lead_type = 'spot'
                      and oi.load_port = spi.loadPort
                      and oi.discharge_port = spi.dischargePort
                      and oi.created_at >= ?
                      and oi.buyer_id = ?
                      group by spi.postId
                      ";

            $partlyRelatedSpotLeadsSql = "
                  select
                      spi.postId 
                  ,group_concat(distinct spi.sellerName) seller_name
                  ,group_concat(distinct spi.loadPort) load_port
                  ,group_concat(distinct spi.dischargePort) discharge_port
                  ,group_concat(distinct spi.containerType) container_type                  
                    from intra_hp_order_items oi, shp_seller_post_index spi
                    where spi.discountBuyerId is NULL
                      and oi.lead_type = 'spot'
                      and oi.load_port = spi.loadPort
                      and oi.discharge_port != spi.dischargePort
                      and oi.created_at >= ?
                      and oi.buyer_id = ?
                      group by spi.postId
                      ";

            $unrelatedSpotLeadsSql = "
                  select
                  spi.postId 
                  ,group_concat(distinct spi.sellerName) seller_name
                  ,group_concat(distinct spi.loadPort) load_port
                  ,group_concat(distinct spi.dischargePort) discharge_port
                  ,group_concat(distinct spi.containerType) container_type                   
                    from intra_hp_order_items oi, shp_seller_post_index spi
                    where spi.discountBuyerId is NULL
                      and oi.lead_type = 'spot'
                      and oi.load_port != spi.loadPort
                      and oi.discharge_port != spi.dischargePort
                      and oi.created_at >= ?
                      and oi.buyer_id = ?
                      group by spi.postId
                      ";

            $queries = [
                "Related Spot Enquiries" => $relatedSpotEnquiriesSql,

                "Partly Related Spot Enquiries" => $partlyRelatedSpotEnquiriesSql,

                "Unrelated Spot Enquiries" => $unrelatedSpotEnquiriesSql,

                "Related Spot Leads" => $relatedSpotLeadsSql,

                "Partly Related Spot Leads" => $partlyRelatedSpotLeadsSql,

                "Unrelated Spot Leads" => $unrelatedSpotLeadsSql,

                "Related Spot Enquiries" => $relatedSpotEnquiriesSql
            ];


            $results = $this->getInboundResults($queries, [$userId, $millis6MonthsAgo]);

            //Get the searcher and delegate the search request
            //$results = $this->serviceFactory->makeIndexer()->postMasterIndex($bo);

            return $results;

        } catch (\Exception $e) {

            LOG::info('Buyer post search finished with failures' . $e);
            throw new ServiceException("Inbound post master cannot be displayed now");

        }

    }


    private function getInboundResults(array $queries = [], array $args = [])
    {

        $results = new BuyerPostMasterInboundResults();
        $sellerNames = [];
        $postId = [];
        $loadPorts = [];
        $dischargePorts = [];
        $commodity = [];
        $containerTypes = [];

        foreach ($queries as $key => $query) {

            LOG::debug("Building post-master-inbound for => " . $key);
            LOG::debug($query);
            $rows = DB::select($query, $args);
            $group = new BuyerPostMasterInboundGroup();
            $group->groupName = $key;

            $count = count($rows);
            LOG::debug('count =>');
            LOG::debug($count);
            if ($count > 0) {

                $postId = [];

                foreach ($rows as $row) {

                    //  var_dump($row);
                    array_push($postId, $row->postId);

                    array_merge($sellerNames, str_split($row->seller_name));

                    array_merge($loadPorts, str_split($row->load_port));

                    array_merge($dischargePorts, str_split($row->discharge_port));

                    array_merge($commodity, str_split($row->commodity));

                    array_merge($containerTypes, str_split($row->container_type));

                }

                $group->postId = $postId;
                $group->countOfPosts = count($postId);

                $rows = DB::select("select count(*) as documentCount from shp_codelist where field='code' and entity='FCLBuyerPostDocs'");
                $countOfDocuments = $rows[0]->documentCount;

                $group->countOfDocuments = $countOfDocuments;


                $rows = DB::select("select count(*) as messageCount from messages where post_id = ?", [postId]);

                $countOfMessages = $rows[0]->messageCount;

                $group->countOfMessages = $countOfMessages;


                array_push($results->groups, $group);

            }

        }

        $postId = array_unique($postId);
        $sellerNames = array_unique($sellerNames);
        $loadPorts = array_unique($loadPorts);
        $dischargePorts = array_unique($dischargePorts);
        $commodity = array_unique($commodity);
        $containerTypes = array_unique($containerTypes);

        $results->facets["posts"] = $postId;
        $results->facets["sellerNames"] = $sellerNames;
        $results->facets["loadPorts"] = $loadPorts;
        $results->facets["dischargePorts"] = $dischargePorts;
        $results->facets["commodity"] = $commodity;
        $results->facets["containerTypes"] = $containerTypes;

        return $results;

    }


}