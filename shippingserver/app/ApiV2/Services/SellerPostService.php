<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 01-02-2017
 * Time: 16:44
 */

namespace ApiV2\Services;

use ApiV2\BusinessObjects\AbstractSearchBO;
use ApiV2\BusinessObjects\SellerPostBO;
use ApiV2\BusinessObjects\SellerPostSearchBO;
use ApiV2\Model\SellerPost;
use ApiV2\Modules\FCL\FCLSellerPostAttributes;
use ApiV2\Repositories;
use ApiV2\Requests\Containers;
use ApiV2\Requests\Routes;
use ApiV2\Services\ISellerPost;
use ApiV2\Utils\LoggingServices;
use App\ApiV2\Events\SellerPostCreated;
use App\Exceptions\ServiceException;
use App\Exceptions\ValidationBuilder;
use App\Jobs\SendBuyerPostSMSAlert;
use App\Jobs\SendEmailAlert;
use DB;
use Illuminate\Foundation\Bus\DispatchesJobs;
use League\Flysystem\Exception;
use Log;
use Tymon\JWTAuth\Facades\JWTAuth;

//use ApiV2\Services\EmailService;

class SellerPostService extends BaseService implements ISellerPostService
{

    use DispatchesJobs;
    private $serviceFactory;

    public function __construct()
    {
    }

    public function setServiceFactory($factory)
    {
        $this->serviceFactory = $factory;
    }

    public function getPostById($id)
    {
        $response = null;

        //get Authorizer
        $authorizer = $this->serviceFactory->makeAuthorizer();

        //Load model object
        $sellerPost = SellerPost::find($id);

        if ($sellerPost == null) {
            ValidationBuilder::create()->error("sellerpost", "post not found")->raise();
        }

        LOG::debug('Seller post retrieved  =>', (array)$sellerPost);

        //Get Transformer and transform retrieved object to bo from model
        $transformer = $this->serviceFactory->makeTransformer();

        $jsonContent = $transformer->model2boGet($sellerPost);
        $response = $jsonContent;

        LOG::debug('Seller post from SellerPostService.getPostById()  =>', (array)$response);
        return $response;
    }


    /**
     * Retrieve All Posts By Post Privacy.
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

        $sellerPost = SellerPost::where('seller_id', '=', $userId)
            ->where('isPublic', '=', $postType)
            ->get()->toArray();

        //Get Transformer and transform retrieved object to bo from model
        $transformer = $this->serviceFactory->makeTransformer();
        $bo = $transformer->model2boGetAll($sellerPost);
        $response = $bo;

        return $response;
    }


    public function saveOrUpdate(SellerPostBO $bo, $transactional = true)
    {

        Log::info("Initiating Seller Post Save");
        $serviceName = unserialize(SHIPPING_MODULES);

        $response = null;
        $isNewPost = false;

        $bo->sellerId = JWTAuth::parseToken()->getPayload()->get('id');

        try {

            if (!isset($bo->postId)) {
                $isNewPost = true;
                LOG::info('Creating new seller post with title [' . $bo->title . '] for seller [' . $bo->sellerId . ']');
            } else {
                LOG::info('Updating existing seller post [' . $bo->sellerId . '] for seller [' . $bo->sellerId . ']');
            }
            LOG::info((array)$bo);

            $this->serviceFactory->makeValidator()->validateSave($bo);

            //TODO State validations
            LOG::info('Transform to the model');

            if ($transactional) {
                DB::beginTransaction();
            }

            //Save model object
            if ($isNewPost) {
                $model = new SellerPost();
                Log::info("After SellerPost Save");
                $logginEntity = SELLER_CREATED_POSTS;
            } else {
                $model = SellerPost::findOrFail($bo->postId);
                $logginEntity = SELLER_UPDATED_POSTS;
                LOG::info('Model Update');
            }

            LOG::info('Before Seller Post save()');
            $model = $this->bo2model($bo, $model);

            $isSaved = $model->save();
            LOG::info('PostId Generated  =>' . $model->id);

            if (trim($isSaved)) {

                Log::info("SellerPost Saved Successfully.");
                $trans_randid = $serviceName[$bo->serviceId] . '/' . date('Y') . '/' . str_pad($model->id, 6, 0, STR_PAD_LEFT);
                $bo->postId = $model->id;
                $bo->version = $model->version;

                $bo->transactionId = $trans_randid;
                $model->transactionId = $trans_randid;

                //Save discounted Buyers at rate card level

                Log::debug("Discounted Buyers =>");

                $discountToBuyers = $this->getDiscountToBuyerIds($bo->attributes, $model->id);

                LOG::info('Inserting Selected Buyers into mapping table');
                /* if(sizeof($discountToBuyers) > 0){
                     LOG::info('Inserting Selected Buyers into seller selected Buyers table');
                     SelectedBuyer::insert($discountToBuyers);

                 }*/
                LOG::info('After discounted Buyers insertion');
                $response = $bo;

                //Call Indexer to commit sellerposts into Seller Post Indexer
                $indexer = $this->serviceFactory->makeIndexer();
                $isRebuild = $indexer->rebuildIndex($bo);
                LOG::info('Saved To SellerPostIndex table');

                if ($transactional) {
                    DB::commit();
                }

                //All other operations such as Notifications, Mail sending etc to be after the Commit()
                LoggingServices::activityLog(ENTITY_SELLER_POST, $logginEntity, 0, HTTP_REFERRER, CURRENT_URL, "1");
                LoggingServices::auditLog($model->id, ENTITY_SELLER_POST, json_encode($response));


                Log::info("Send Emails to discounted Buyers");

                $event = SELLER_CREATED_POST_FOR_BUYERS;
                $emailInfo = ['sellername' => JWTAuth::parseToken()->getPayload()->get('firstname'), 'randnumber' => $trans_randid];

                $job = new SendEmailAlert($discountToBuyers, $emailInfo, $event);
                $this->dispatch($job);
                LOG::info('Dispatched Email Notification');


                //Send Sms to the private Sellers

                //*******Send Sms to the private Sellers***********************//
                LOG::info("Sending SMS to discounted Buyers ...");

                $msg_params = array(
                    'buyername' => JWTAuth::parseToken()->getPayload()->get('username'),
                    'servicename' => $serviceName[$bo->serviceId]
                );
                LOG::info("Send SMS to discounted Buyer" . $msg_params['buyername']);

                $mobileNumbers = SendSmsService::getBuyerMobileNumbers((array)$discountToBuyers);
                //*******Send Sms to the private Sellers***********************//
                if (sizeof($mobileNumbers) > 0) {
                    $userID = JWTAuth::parseToken()->getPayload()->get('id');
                    $job = new SendBuyerPostSMSAlert($mobileNumbers, BUYER_CREATED_POST_FOR_SELLERS_SMS, $msg_params, $userID);
                    $this->dispatch($job);
                }

                event(new SellerPostCreated($bo));


                LOG::info("Trigger SOLR pull");
                //Call Sorl Delta Import
                $service = new SolrSearchService();
                $service->deltaImport("sellerposts");

                LOG::info('Finished creating seller post');

            } else {

                throw new ServiceException("Seller post could not be saved");
            }

            $response = $bo;

        } catch (Exception $e) {

            if ($transactional) {
                DB::rollBack();
            }

            LOG::error("Seller post could not be saved", $e->getMessage());

            $this->handle($e);

        }

        return $response;
    }

    private function bo2model(SellerPostBO $bo, $model)
    {
        //print_r($bo);exit;
        $now = date('Y-m-d H:i:s');
        if (!empty($bo->postId)) {
            $model->updated_by = JWTAuth::parseToken()->getPayload()->get('id');
            $model->updated_ip = $_SERVER['REMOTE_ADDR'];
            $model->updated_at = $now;
            $model->version = (int)$model->version + 1;
        } else {
            $model->created_by = JWTAuth::parseToken()->getPayload()->get('id');
            $model->created_ip = $_SERVER['REMOTE_ADDR'];
            $model->created_at = $now;
            $model->version = 1;
        }
        $model->status = $bo->status;
        $model->view_count = $bo->viewCount;
        $model->valid_to = $bo->validTo;
        $model->service_id = $bo->serviceId;
        $model->valid_from = $bo->validFrom;
        $model->seller_id = $bo->sellerId;
        $model->title = $bo->title;
        //   $model->access_id = $bo->accessId;
        $model->service_subcategory = $bo->serviceSubType;
        //$model->tracking= $bo->tracking;
        $model->isPublic = $bo->isPublic;
        $model->terms_accepted = $bo->isTermAccepted;
        $model->terms_conditions = $bo->termsConditions;


        $attributes = [];
        // array_push($attributes, array('payments' => $bo->payment,'routes' => $bo->routes,'discount' => $bo->discount));
        $model->attributes = json_encode($bo->attributes);

        return $model;
    }

    public function getDiscountToBuyerIds(FCLSellerPostAttributes $attributes, $postId)
    {

        $discountedBuyer = $discountedArray = array();
        foreach ($attributes->discount as $discounte) {
            // $discountedBuyer['postId'] = $postId;
            $discountedBuyer[] = $discounte->buyerId;
            /*$discountedBuyer['discountType'] = $discounte->discountType;
            $discountedBuyer['discount'] = $discounte->discount;
            $discountedBuyer['creditDays'] = $discounte->creditDays;
            $discountedArray =array_merge($discountedArray, $this->getBuyerIdsPortPairs($attributes, $discountedBuyer));*/
        }

        return $discountedBuyer;
    }

    public function getBuyerIdsPortPairs(FCLSellerPostAttributes $attributes, $discountedBuyer)
    {
        $discountedBuyers = $portLevelDiscount = array();
        foreach ($attributes->portPair as $portPair) {
            if (sizeof($portPair->discount) > 0) {
                foreach ($portPair->discount as $discounte) {
                    $portLevelDiscount[] = $discounte->buyerId;
                }
                $discountedBuyers = array_merge($discountedBuyers, $portLevelDiscount);
            }
            $discountedBuyers = array_merge($discountedBuyers, $discountedBuyer);
        }
        return $discountedBuyers;
    }

    public function filterPost(SellerPostSearchBO $bo)
    {
        LOG::info('Buyer post search invoked');
        LOG::debug((array)$bo);

        //Get the searcher and delegate the search request
        $results = $this->serviceFactory->makeIndexer()->searchIndex($bo);
        return $results;

    }

    public function getAllPosts()
    {
        $response = array();

        //get Authorizer
        $authorizer = $this->serviceFactory->makeAuthorizer();

        //Load model object
        $sellerPost = SellerPost::all();
        //Get Transformer and transform retrieved object to bo from model
        $transformer = $this->serviceFactory->makeTransformer();
        $bo = $transformer->model2boGetAll($sellerPost);
        $response = $bo;
        return $response;
    }

    public function postMasterFilters(SellerPostSearchBO $bo)
    {
        LOG::info('Seller Post Master invoked with criteria');
        $results = $this->serviceFactory->makeIndexer()->postMasterIndex($bo);
        return $results;

    }

    public function postMasterInbound(AbstractSearchBO $filter)
    {

        LOG::info('Seller PostMasterInbound invoked with criteria');
        LOG::info((array)$filter);

        $sellerId = JWTAuth::parseToken()->getPayload()->get('id');
        //Get the recommendar and delegate the search request
        $results = $this->serviceFactory->makeRecommender()->filterSellerInboundPostMaster($sellerId, $filter);
        return $results;

    }

    public function getDiscountToBuyers(FCLSellerPostAttributes $attributes)
    {

        $discountedBuyer = $discountedArray = array();
        foreach ($attributes->discount as $discounte) {
            $discountedBuyer['buyerId'] = $discounte->buyerId;
            $discountedBuyer['discountType'] = $discounte->discountType;
            $discountedBuyer['discount'] = $discounte->discount;
            $discountedBuyer['creditDays'] = $discounte->creditDays;
            $discountedArray = array_merge($discountedArray, $this->getBuyersPortPairs($attributes, $discountedBuyer));
        }
        return $discountedArray;
    }

    public function getBuyersPortPairs(FCLSellerPostAttributes $attributes, $discountedBuyer)
    {

        $discountedBuyers = $portLevelDiscount = array();
        foreach ($attributes->portPair as $portPair) {
            $discountedBuyer['loadPort'] = $portPair->loadPort;
            $discountedBuyer['dischargePort'] = $portPair->dischargePort;
            if (sizeof($portPair->discount) > 0) {
                foreach ($portPair->discount as $discounte) {
                    $portLevelDiscount['buyerId'] = $discounte->buyerId;
                    $portLevelDiscount['discountType'] = $discounte->discountType;
                    $portLevelDiscount['discount'] = $discounte->discount;
                    $portLevelDiscount['creditDays'] = $discounte->creditDays;
                    $portLevelDiscount['loadPort'] = $portPair->loadPort;
                    $portLevelDiscount['dischargePort'] = $portPair->dischargePort;
                }
                array_push($discountedBuyers, $portLevelDiscount);
            }
            $discountedBuyers[] = $discountedBuyer;
        }
        return $discountedBuyers;
    }

}