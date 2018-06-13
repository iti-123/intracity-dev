<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 2/22/2017
 * Time: 6:16 PM
 */

namespace Api\Modules\LCL;

use Api\BusinessObjects\BuyerPostBO;
use Api\BusinessObjects\BuyerPostSearchBO;
use Api\Framework\IBuyerPostIndexer;
use Api\Model\FCLBuyerPostIndex;
use Api\Services\SolrSearchService;
use Api\Services\UserDetailsService;
use Api\Utils\DateUtils;
use App\Exceptions\ApplicationException;
use Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class LCLBuyerPostIndexer implements IBuyerPostIndexer
{
    public $serviceName;
    public $service;

    public function rebuildIndex(BuyerPostBO $bo)
    {


        $serviceName = unserialize(SHIPPING_MODULES);
        $service = $serviceName[$bo->serviceId];

        LOG:
        info("Rebuilding search index for buyerpost " . $bo->postId);

        try {
            $now = time();
            $fclSearchRec = new FCLBuyerPostIndex(); //Eloquent Model

            //$fclSearchRec->entity = BUYER_ADDED_NEW_QUOTE;
            $fclSearchRec->entity = "buyerpost";
            $fclSearchRec->postId = $bo->postId;
            $fclSearchRec->serviceId = $bo->serviceId;
            $fclSearchRec->serviceName = $service;
            $fclSearchRec->buyerId = $bo->buyerId;
            $fclSearchRec->buyerName = JWTAuth::parseToken()->getPayload()->get('firstname');
            $fclSearchRec->leadType = $bo->leadType;
            $fclSearchRec->title = $bo->title;
            $fclSearchRec->lastDateTimeForQuote = $bo->lastDateTimeOfQuoteSubmission;
            $fclSearchRec->status = $bo->status;
            $fclSearchRec->isDeleted = 0;
            $fclSearchRec->validFrom = DateUtils::unixNow();
            $fclSearchRec->validTo = $bo->lastDateTimeOfQuoteSubmission;
            $fclSearchRec->isPublic = $bo->isPublic;
            $visibleToSellers = $bo->visibleToSellers;
            if ($bo->isPublic) {

                $fclSearchRec->visibleToSellerId = 0;
                $fclSearchRec->visibleToSellerName = "";
                $lclBuyerPostAttributes = $bo->attributes;
                if (!empty($lclBuyerPostAttributes)) {
                    if ($bo->leadType == "spot") {
                        $this->fillSpotRoutes($lclBuyerPostAttributes, $fclSearchRec);
                    } else {

                        $this->fillTermServiceType($lclBuyerPostAttributes, $fclSearchRec);
                    }
                }
            } else {
                foreach ($visibleToSellers as $seller) {
                    $fclSearchRec->visibleToSellerId = $seller;
                    $fclSearchRec->visibleToSellerName = UserDetailsService::getUserDetails($seller)->username;
                    $lclBuyerPostAttributes = $bo->attributes;
                    if (!empty($lclBuyerPostAttributes)) {
                        if ($bo->leadType == "spot") {

                            $this->fillSpotRoutes($lclBuyerPostAttributes, $fclSearchRec);
                        } else {
                            $this->fillTermServiceType($lclBuyerPostAttributes, $fclSearchRec);
                        }
                    }
                }
            }

            $service = new SolrSearchService();
            $service->deltaImport("buyerposts");

            return true;

        } catch (\Exception $e) {

            LOG::error($e);

            throw new ApplicationException([], ["Failed posting buyerpost to Search Store"]);
        }
    }


    function fillSpotRoutes(LCLBuyerPostAttributes $attributes, FCLBuyerPostIndex $fclSearchRec)
    {
        LOG::info("Filling routing information");
        if (!empty($attributes)) {

            $route = $attributes->route;

            $fclSearchRec->loadPort = $route->loadPort;
            $fclSearchRec->dischargePort = $route->dischargePort;
            $fclSearchRec->serviceSubType = $route->serviceSubType;
            $fclSearchRec->originLocation = $route->originLocation;
            $fclSearchRec->destinationLocation = $route->destinationLocation;
            $fclSearchRec->commodity = $route->commodity;
            $fclSearchRec->cargoReadyDate = $route->cargoReadyDate;
            $fclSearchRec->priceType = $route->priceType;

            $this->fillPackages($route, $fclSearchRec);
        }

    }

    function fillPackages(Route $routeBo, FCLBuyerPostIndex $fclSearchRec)
    {
        // print_r($fclSearchRec);exit;

        LOG::info("Filling Package  information");
        $lclBuyerPostIndex = array();
        $now = date('Y-m-d H:i:s');

        if (!empty($routeBo)) {

            $lclPackages = $routeBo->packageDimensions;
            if (is_array($lclPackages)) {
                foreach ($lclPackages as $package) {

                    $fclSearchRec->containerType = $package->packagingType;
                    $fclSearchRec->noOfPackages = $package->noOfPackages;
                    $fclSearchRec->weightUnit = $package->weightUnit;
                    $fclSearchRec->grossWeight = $package->grossWeight;
                    $fclSearchRec->created_at = $now;
                    $fclSearchRec->updated_at = $now;
                    $lclBuyerPostIndex[] = json_decode($fclSearchRec, true);
                }
            } else {
                $lclBuyerPostIndex[] = json_decode($fclSearchRec, true);

            }

            Log::info("asdfasdfsdfsdfasdfasdfsd");
            FCLBuyerPostIndex::insert($lclBuyerPostIndex);
            Log::info("Data Sync successfull");

        }

    }

    function fillTermServiceType(LCLTermBuyerPostAttributes $attributes, FCLBuyerPostIndex $fclSearchRec)
    {

        LOG::info("Filling serviceType information");

        if (!empty($attributes)) {
            //print_r($attributes);exit;
            $serviceType = $attributes->serviceType;
            // $fclSearchRec->priceType=$attributes->awardCriteria;
            foreach ($serviceType as $service) {
                $fclSearchRec->serviceSubType = $service->serviceSubType;
                $fclSearchRec->originLocation = $service->originLocation;
                $fclSearchRec->destinationLocation = $service->destinationLocation;
                //$routes = $service->routes;
                $this->fillTermRoutes($service, $fclSearchRec);
            }
        }
    }

    function fillTermRoutes(ServiceType $service, FCLBuyerPostIndex $fclSearchRec)
    {
        LOG::info("Filling TermRoute information");
        if (!empty($service)) {
            $routeBo = $service->routes;

            foreach ($routeBo as $route) {
                $fclSearchRec->loadPort = $route->loadPort;
                $fclSearchRec->dischargePort = $route->dischargePort;
                $fclSearchRec->commodity = $route->commodity;
                $fclSearchRec->cargoReadyDate = $route->cargoReadyDate;
                $this->fillTermPackages($route, $fclSearchRec);
                //Now we have flattened the hierarchical buyer. Let us add this single record to SOLR.
                /*$service = new SolrSearchService();
                $service->add($fclSearchRec);*/

            }

        }

    }


    function fillTermPackages(TermRoute $routeBo, FCLBuyerPostIndex $fclSearchRec)
    {


        LOG::info("Filling Package  information");
        $lclBuyerPostIndex = array();
        $now = date('Y-m-d H:i:s');

        if (!empty($routeBo)) {

            $lclPackages = $routeBo->packageDimensions;
            if (is_array($lclPackages)) {
                foreach ($lclPackages as $package) {

                    $fclSearchRec->packagingType = $package->packagingType;
                    $fclSearchRec->noOfPackages = $package->noOfPackages;
                    $fclSearchRec->weightUnit = $package->weightUnit;
                    $fclSearchRec->grossWeight = $package->grossWeight;
                    $fclSearchRec->created_at = $now;
                    $fclSearchRec->updated_at = $now;
                    $lclBuyerPostIndex[] = json_decode($fclSearchRec, true);
                }
            } else {
                $lclBuyerPostIndex[] = json_decode($fclSearchRec, true);

            }


            Log::info("-----");
            FCLBuyerPostIndex::insert($lclBuyerPostIndex);
            Log::info("Data Sync successfull");

        }

    }

    /* function fillTermContainers(TermRoute $routeBo, FCLBuyerPostIndex $fclSearchRec) {
         LOG::info("Filling Containers information");
         $fclBuyerPostIndex = array();
         $now = date('Y-m-d H:i:s');
         if (!empty($routeBo)) {

             $fclContainers = $routeBo->containers;
             foreach ($fclContainers as $container ) {
                 $fclSearchRec->containerType  = $container->containerType;
                 $fclSearchRec->containerQuantity = $container->quantity;
                 $fclSearchRec->weightUnit = $container->weightUnit;
                 $fclSearchRec->grossWeight = $container->grossWeight;
                 $fclSearchRec->created_at = $now;
                 $fclSearchRec->updated_at = $now;
                 //Now we have flattened the hierarchical buyer. Let us add this single record to SOLR.
                 //$service = new SolrSearchService();
                 //$service->add ($fclSearchRec);
                 $fclBuyerPostIndex[] = json_decode($fclSearchRec, true);
             }



             FCLBuyerPostIndex::insert($fclBuyerPostIndex);

         }

     }*/


    /**
     * @param $postId
     * @throws ApplicationException
     */
    public function dropIndex($postId)
    {
        LOG::info('Dropping existing search index for buyerpost = ' . $postId);
        try {

            $delQuery = "{'delete': {'query': 'entity:buyerpost and buyerPostId:$postId'}}";

            $service = new SolrSearchService();
            $service->remove($delQuery);

        } catch (Exception $e) {

            LOG::error($e);

            throw new ApplicationException(["postid" => $postId], ["Error deleting existing buyerposts from search store"]);
        }
    }

    public function searchIndex(BuyerPostSearchBO $bo)
    {

        $jsonResponse = null;

        LOG::info('Searching index for buyerposts');

        try {

            $fq = $this->generateSearchQuery($bo);

            LOG::info("filter query generated is " . $fq);
            $facets = ["fields" => ["buyer", "commodity", "containerType", "cargoReadyDate", "lastDateTimeForQuote"], "ranges" => []];

            $service = new SolrSearchService();

            $jsonResponse = $service->search(null, $fq, $facets, $bo->start, $bo->rows);

        } catch (Exception $e) {

            LOG::error($e);

            throw new ApplicationException([], ["Error searching buyerposts from search store"]);
        }

        return json_decode($jsonResponse);

    }


    private function generateSearchQuery(LCLBuyerPostSearchBO $bo)
    {

        $serviceName = unserialize(SHIPPING_MODULES);
        // $service = $bo->serviceId.'-'.$serviceName[$bo->serviceId];
        // $service = '23-LCL';

        $fq = "entity:buyerpost AND service:" . LCL;

        if (isset($bo->leadType)) {
            $fq .= " AND leadType:(" . $bo->leadType . ")";
        }

        if (isset($bo->loadPort) && count($bo->loadPort) > 0) {
            $fq .= " AND loadPort:(";
            for ($i = 0; $i < sizeof($bo->loadPort); $i++) {
                if ($i == 0)
                    $fq .= str_replace(" ", "*", $bo->loadPort[$i]);
                else
                    $fq .= " OR " . str_replace(" ", "*", $bo->loadPort[$i]);
            }
            $fq .= ") ";
        }


        if (isset($bo->dischargePort) && count($bo->dischargePort) > 0) {

            $fq .= " AND dischargePort:(";
            for ($i = 0; $i < sizeof($bo->dischargePort); $i++) {
                if ($i == 0)
                    $fq .= str_replace(" ", "*", $bo->dischargePort[$i]);
                else
                    $fq .= " OR " . str_replace(" ", "*", $bo->dischargePort[$i]);
            }
            $fq .= ") ";
        }

        if (isset($bo->cargoReadyDate) && count($bo->cargoReadyDate) > 0) {
            $fq .= " AND cargoReadyDate:(";
            for ($i = 0; $i < sizeof($bo->cargoReadyDate); $i++) {
                if ($i == 0)
                    $fq .= $bo->cargoReadyDate[$i];
                else
                    $fq .= " OR " . $bo->cargoReadyDate[$i];
            }
            $fq .= ") ";
        }

        if (isset($bo->commodity) && count($bo->commodity) > 0) {
            $fq .= " AND commodity:(";
            for ($i = 0; $i < sizeof($bo->commodity); $i++) {
                if ($i == 0)
                    $fq .= str_replace(" ", "*", $bo->commodity[$i]);
                else
                    $fq .= " OR " . str_replace(" ", "*", $bo->commodity[$i]);
            }
            $fq .= ") ";
        }

        if (count($bo->packagingType) > 0) {
            $fq .= " AND containerType:(";
            for ($i = 0; $i < sizeof($bo->packagingType); $i++) {
                if ($i == 0)
                    $fq .= str_replace(" ", "*", $bo->packagingType[$i]);
                else
                    $fq .= " OR " . str_replace(" ", "*", $bo->packagingType[$i]);
            }
            $fq .= ") ";
        }

        //TODO: Check if CargoReadyDate should be an exact match or a lesser than match
        /*if(isset($bo->cargoReadyDate)){
            $fq .= "AND cargoReadyDate:" . $bo->cargoReadyDate;
        }*/
//echo $fq;exit;
        return $fq;

    }

    public function postMasterIndex(BuyerPostSearchBO $bo)
    {

        $jsonResponse = null;

        LOG::info('PostMaster index for Seller posts');

        try {

            $fq = $this->generatePostMasterQuery($bo);

            LOG::info("filter query generated is " . $fq);

            $service = new SolrSearchService();

            //TODO: Add additional facet field named Delivery date. But what is this field?
            $facets = ["fields" => ["discountBuyer", "containerType", "tracking", "loadPort", "dischargePort"], "ranges" => []];

            $groups = ["postId"];

            $jsonResponse = $service->search(null, $fq, $facets, $bo->start, $bo->rows, $groups);
            $jsonResponse = $this->addAditionalData($jsonResponse);

        } catch (Exception $e) {

            LOG::error($e);

            throw new ApplicationException([], ["Error searching Seller posts from search store"]);
        }

        return $jsonResponse;
    }

    public function generatePostMasterQuery(LCLBuyerPostMasterOutboundBO $bo)
    {

        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $fq = "entity:sellerpost AND service:23-LCL AND seller:" . $userId . "*";

        if (isset($bo->isPublic)) {
            if ($bo->isPublic == "public")
                $fq .= " AND isPublic:true";
            else
                $fq .= " AND isPublic:false";
        }
        if (isset($bo->visibleToBuyer) && sizeof($bo->visibleToBuyer) > 0) {
            $fq .= " AND visibleToBuyer:(";
            for ($i = 0; $i < sizeof($bo->visibleToBuyer); $i++) {
                if ($i == 0)
                    $fq .= $bo->visibleToBuyer[$i] . "*";
                else
                    $fq .= " OR " . $bo->visibleToBuyer[$i] . "*";
            }
            $fq .= ") ";
        }

        if (isset($bo->containerType) && sizeof($bo->containerType) > 0) {
            $fq .= " AND containerType:(";
            for ($i = 0; $i < sizeof($bo->containerType); $i++) {
                if ($i == 0)
                    $fq .= $bo->containerType[$i];
                else
                    $fq .= " OR " . $bo->containerType[$i];
            }
            $fq .= ") ";
        }
        if (isset($bo->loadPort) && sizeof($bo->loadPort) > 0) {
            $fq .= " AND loadPort:(";
            for ($i = 0; $i < sizeof($bo->loadPort); $i++) {
                if ($i == 0)
                    $fq .= $bo->loadPort[0];
                else
                    $fq .= " OR " . $bo->loadPort[0];
            }
            $fq .= ") ";
        }
        if (isset($bo->dischargePort) && sizeof($bo->dischargePort) > 0) {
            $fq .= " AND dischargePort:(";
            for ($i = 0; $i < sizeof($bo->loadPort); $i++) {
                if ($i == 0)
                    $fq .= $bo->dischargePort[$i];
                else
                    $fq .= " OR " . $bo->dischargePort[$i];
            }
            $fq .= ") ";
        }
        if (isset($bo->status) && sizeof($bo->status) > 0) {
            $fq .= " AND status:(";
            for ($i = 0; $i < sizeof($bo->status); $i++) {
                if ($i == 0)
                    $fq .= $bo->status[$i];
                else
                    $fq .= " OR " . $bo->status[$i];
            }
            $fq .= ") ";
        }

        Log::info($fq);
        return $fq;
    }

    public function addAditionalData($jsonResponse)
    {
        $buyerPostData = array();
        $jsonResponse = json_decode($jsonResponse);
        /*$groups = $jsonResponse->grouped->postId->groups;
        foreach ($groups as $group){
            $docs = $group->doclist->docs;

            if(JWTAuth::parseToken()->getPayload()->get('role') == "Seller"){
                $docs[0]->sellerQuoteStatus = $this->getSellerQuoteStauts($docs[0]->postId);
            }

            $docs[0]->notificationCounts = $this->getNotificationCounts($docs[0]->postId);
        }*/
        return $jsonResponse;
    }


}