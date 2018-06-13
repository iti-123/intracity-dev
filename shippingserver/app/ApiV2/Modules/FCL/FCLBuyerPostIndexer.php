<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 2/9/2017
 * Time: 9:09 PM
 */

namespace ApiV2\Modules\FCL;

use ApiV2\BusinessObjects\BuyerPostBO;
use ApiV2\BusinessObjects\BuyerPostSearchBO;
use ApiV2\Framework\IBuyerPostIndexer;
use ApiV2\Model\BuyerContract;
use ApiV2\Model\FCLBuyerPostIndex;
use ApiV2\Model\SellerQuotes;
use ApiV2\Services\SolrSearchService;
use ApiV2\Services\UserDetailsService;
use ApiV2\Utils\DateUtils;
use App\Exceptions\ApplicationException;
use DB;
use Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class FCLBuyerPostIndexer implements IBuyerPostIndexer
{

    public function rebuildIndex(BuyerPostBO $bo)
    {

        LOG:
        info("Rebuilding search index for buyerpost " . $bo->postId);

        try {
            $now = time();
            $fclSearchRec = new FCLBuyerPostIndex();

            $fclSearchRec->entity = "buyerpost";
            $fclSearchRec->postId = $bo->postId;
            $fclSearchRec->serviceId = $bo->serviceId;
            $fclSearchRec->serviceName = "FCL";
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
                $fclBuyerPostAttributes = $bo->attributes;
                if (!empty($fclBuyerPostAttributes)) {
                    if ($bo->leadType == "spot") {
                        $this->fillSpotRoutes($fclBuyerPostAttributes, $fclSearchRec);
                    } else {
                        $this->fillTermServiceType($fclBuyerPostAttributes, $fclSearchRec);
                    }
                }
            } else {
                foreach ($visibleToSellers as $seller) {
                    $fclSearchRec->visibleToSellerId = $seller;
                    $fclSearchRec->visibleToSellerName = UserDetailsService::getUserDetails($seller)->username;
                    $fclBuyerPostAttributes = $bo->attributes;
                    if (!empty($fclBuyerPostAttributes)) {
                        if ($bo->leadType == "spot") {
                            $this->fillSpotRoutes($fclBuyerPostAttributes, $fclSearchRec);
                        } else {
                            $this->fillTermServiceType($fclBuyerPostAttributes, $fclSearchRec);
                        }
                    }
                }
            }
            return true;

        } catch (\Exception $e) {

            LOG::error($e);

            throw new ApplicationException([], ["rebuildIndex() -> Failed posting buyerpost to Search Store"]);
        }
    }

    function fillSpotRoutes(FCLBuyerPostAttributes $attributes, FCLBuyerPostIndex $fclSearchRec)
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
            $this->fillContainers($route, $fclSearchRec);
        }

    }

    function fillContainers(Route $routeBo, FCLBuyerPostIndex $fclSearchRec)
    {
        LOG::info("Filling Containers information");
        $fclBuyerPostIndex = array();
        $now = date('Y-m-d H:i:s');
        if (!empty($routeBo)) {

            $fclContainers = $routeBo->containers;
            foreach ($fclContainers as $container) {
                $fclSearchRec->containerType = $container->containerType;
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
            LOG::info("Inserting into BuyerPostIndex table &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&");
            LOG::info($fclBuyerPostIndex);
            FCLBuyerPostIndex::insert($fclBuyerPostIndex);

        }

    }

    function fillTermServiceType(FCLTermBuyerPostAttributes $attributes, FCLBuyerPostIndex $fclSearchRec)
    {

        LOG::info("Filling serviceType information");

        if (!empty($attributes)) {

            $serviceType = $attributes->serviceType;
            $fclSearchRec->priceType = $attributes->awardCriteria;
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
                $this->fillTermContainers($route, $fclSearchRec);
                //Now we have flattened the hierarchical buyer. Let us add this single record to SOLR.
                /*$service = new SolrSearchService();
                $service->add($fclSearchRec);*/

            }

        }

    }

    function fillTermContainers(TermRoute $routeBo, FCLBuyerPostIndex $fclSearchRec)
    {
        LOG::info("Filling Containers information");
        $fclBuyerPostIndex = array();
        $now = date('Y-m-d H:i:s');
        if (!empty($routeBo)) {

            $fclContainers = $routeBo->containers;
            foreach ($fclContainers as $container) {
                $fclSearchRec->containerType = $container->containerType;
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

    }

    /**
     * @param $postId
     * @throws ApplicationException
     */
    public function dropIndex($postId)
    {
        LOG::info('Dropping/Updating existing search index for buyerpost = ' . $postId);
        try {

            //$delQuery = "{'delete': {'query': 'entity:buyerpost AND buyerPostId:$postId'}}";
            if (FCLBuyerPostIndex::where('postId', '=', $postId)->exists()) {
                FCLBuyerPostIndex::where('postId', '=', $postId)->update(['isDeleted' => 1]);
            }
            //$service = new SolrSearchService();
            //$service->remove($delQuery);

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

            $service = new SolrSearchService();

            //TODO: Add additional facet field named Delivery date. But what is this field?
            $facets = ["fields" => ["buyer", "commodity", "containerType", "cargoReadyDate", "lastDateTimeForQuote"], "ranges" => []];

            $jsonResponse = $service->search(null, $fq, $facets, $bo->start, $bo->rows, "updated_at%20desc");
            $jsonResponse = $this->addAditionalDataForSearch($jsonResponse);
            //$jsonResponse = json_decode($jsonResponse, true);

        } catch (Exception $e) {

            LOG::error($e);

            throw new ApplicationException([], ["Error searching buyerposts from search store"]);
        }

        return $jsonResponse;

    }

    private function generateSearchQuery(FCLBuyerPostSearchBO $bo)
    {

        //Generated query should look like this.
        $userId = JWTAuth::parseToken()->getPayload()->get('id');

        $fq = "entity:buyerpost AND service:" . FCL;
        $fq .= " AND (isPublic:true";
        $fq .= " OR (isPublic:false AND visibleToSeller:" . $userId . "*))";
        if (isset($bo->leadType)) {
            $fq .= " AND leadType:(" . $bo->leadType . ")";
        }

        if (isset($bo->loadPort) && sizeof($bo->loadPort) > 0) {
            $fq .= " AND loadPort:(";
            for ($i = 0; $i < sizeof($bo->loadPort); $i++) {
                if ($i == 0)
                    $fq .= str_replace(" ", "*", $bo->loadPort[$i]);
                else
                    $fq .= " OR " . str_replace(" ", "*", $bo->loadPort[$i]);
            }
            $fq .= ") ";
        }


        if (isset($bo->dischargePort) && sizeof($bo->dischargePort) > 0) {
            $fq .= " AND dischargePort:(";
            for ($i = 0; $i < sizeof($bo->dischargePort); $i++) {
                if ($i == 0)
                    $fq .= str_replace(" ", "*", $bo->dischargePort[$i]);
                else
                    $fq .= " OR " . str_replace(" ", "*", $bo->dischargePort[$i]);
            }
            $fq .= ") ";
        }

        if (isset($bo->cargoReadyDate) && sizeof($bo->cargoReadyDate) > 0) {
            $fq .= " AND cargoReadyDate:(";
            for ($i = 0; $i < sizeof($bo->cargoReadyDate); $i++) {
                if ($i == 0)
                    $fq .= $bo->cargoReadyDate[$i];
                else
                    $fq .= " OR " . $bo->cargoReadyDate[$i];
            }
            $fq .= ") ";
        }

        if (isset($bo->commodity) && sizeof($bo->commodity) > 0) {
            $fq .= " AND commodity:(";
            for ($i = 0; $i < sizeof($bo->commodity); $i++) {
                if ($i == 0)
                    $fq .= str_replace(" ", "*", $bo->commodity[$i]);
                else
                    $fq .= " OR " . str_replace(" ", "*", $bo->commodity[$i]);
            }
            $fq .= ") ";
        }

        if (isset($bo->containerType) && sizeof($bo->containerType) > 0) {
            $fq .= " AND containerType:(";
            for ($i = 0; $i < sizeof($bo->containerType); $i++) {
                if ($i == 0)
                    $fq .= str_replace(" ", "*", $bo->containerType[$i]);
                else
                    $fq .= " OR " . str_replace(" ", "*", $bo->containerType[$i]);
            }
            $fq .= ") ";
        }

        return $fq;
    }

    public function addAditionalDataForSearch($jsonResponse)
    {
        $buyerPostData = array();
        $jsonResponse = json_decode($jsonResponse);
        $docs = $jsonResponse->response->docs;

        for ($i = 0; $i < sizeof($docs); $i++) {
            $sellerQuote = $this->getSellerQuoteStauts($docs[$i]->postId);
            if (sizeof($sellerQuote) > 0) {
                $docs[$i]->sellerQuoteId = $sellerQuote['id'];
                $docs[$i]->sellerQuoteStatus = $sellerQuote['status'];
                $docs[$i]->isContractGenerated = $sellerQuote['isContractGenerated'];
            } else {
                $docs[$i]->sellerQuoteId = '';
                $docs[$i]->sellerQuoteStatus = '';
                $docs[$i]->isContractGenerated = '';
            }
            $docs[$i]->notificationCounts = $this->getNotificationCounts($docs[$i]->postId);
        }
        return $jsonResponse;
    }

    public function getSellerQuoteStauts($postId = '')
    {
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        if (SellerQuotes::where('buyerPostId', '=', $postId)->where('sellerId', '=', $userId)->exists()) {
            $sellerQuoteStatus = SellerQuotes::where('buyerPostId', '=', $postId)
                ->where('sellerId', '=', $userId)
                ->select('status', 'id', 'isContractGenerated')->get()->toArray();
        } else
            $sellerQuoteStatus = array();
        if (sizeof($sellerQuoteStatus) > 0) {
            return $sellerQuoteStatus[0];
        } else
            return [];
    }

    public function getNotificationCounts($postId = '')
    {
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        try {
            $userCounts = array();
            $getMessages = DB::table('messages')
                ->where('from', $userId)
                ->select(DB::raw('"message" as type, count(*) as count'));
            $getDocuments = DB::table('shp_codelist')
                ->where('entity', 'documents')
                ->select(DB::raw('"documents" as type, count(*) as count'));

            $getOffers = DB::table('shp_seller_quotes')
                ->where('buyerPostId', $postId)
                ->select(DB::raw('"offers" as type, count(*) as count'))->union($getMessages)->union($getDocuments)
                ->get();
            if (count($getOffers) >= 1) {
                for ($i = 0; $i < sizeof($getOffers); $i++) {
                    $userCounts[$getOffers[$i]->type] = $getOffers[$i]->count;
                }
                return $userCounts;
            }
        } catch (ApplicationException $ae) {
            $err = shipres::nok3($ae);
            LOG::info('Application Exception ', (array)$err);
            return shipres::nok3($ae);
        }


        /*if(SellerQuotes::where('buyerPostId', '=', $postId)->exists()){
            $sellerQuoteStatus = SellerQuotes::where('buyerPostId', '=', $postId)
                ->select('status')->get()->toArray();
        }*/
    }

    public function postMasterIndex(BuyerPostSearchBO $bo)
    {

        $jsonResponse = null;

        LOG::info('PostMaster index for buyer posts');

        try {

            $fq = $this->generatePostMasterQuery($bo);

            LOG::info("filter query generated is " . $fq);

            $service = new SolrSearchService();

            //TODO: Add additional facet field named Delivery date. But what is this field?
            $facets = ["fields" => ["leadType", "isPublic", "visibleToSeller", "commodity", "containerType", "cargoReadyDate", "loadPort", "dischargePort", "status"], "ranges" => []];

            $groups = ["postId"];

            $jsonResponse = $service->search(null, $fq, $facets, $bo->start, $bo->rows, "updated_at%20desc", $groups);

            $jsonResponse = $this->addAditionalData($jsonResponse);

        } catch (Exception $e) {

            LOG::error($e);

            throw new ApplicationException([], ["Error searching buyerposts from search store"]);
        }

        return $jsonResponse;
    }

    public function generatePostMasterQuery(FCLBuyerPostMasterOutboundBO $bo)
    {
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $fq = "entity:buyerpost AND service:" . FCL;
        if (JWTAuth::parseToken()->getPayload()->get('role') == "Seller") {
            //$fq .= " visibleToSeller:".$userId."*";
            $fq .= " AND (isPublic:true";
            $fq .= " OR (isPublic:false AND visibleToSeller:" . $userId . "*))";
        } else {
            $fq .= " buyer:" . $userId . "*";
        }
        if (isset($bo->leadType)) {
            $fq .= " AND leadType:" . $bo->leadType;
        }
        if (isset($bo->isPublic)) {
            if ($bo->isPublic == "public")
                $fq .= " AND isPublic:true";
            else
                $fq .= " AND isPublic:false";
        }
        if (isset($bo->postIds) && sizeof($bo->postIds) > 0) {
            $fq .= " AND postId:(";
            for ($i = 0; $i < sizeof($bo->postIds); $i++) {
                if ($i == 0)
                    $fq .= (int)$bo->postIds[$i];
                else
                    $fq .= " " . (int)$bo->postIds[$i];
            }
            $fq .= ") ";
        }
        if (isset($bo->visibleToSeller) && sizeof($bo->visibleToSeller) > 0) {
            $fq .= " AND visibleToSeller:(";
            for ($i = 0; $i < sizeof($bo->visibleToSeller); $i++) {
                if ($i == 0)
                    $fq .= $bo->visibleToSeller[$i] . "*";
                else
                    $fq .= " OR " . $bo->visibleToSeller[$i] . "*";
            }
            $fq .= ") ";
        }
        if (isset($bo->commodity) && sizeof($bo->commodity) > 0) {
            $fq .= " AND commodity:(";
            for ($i = 0; $i < sizeof($bo->commodity); $i++) {
                if ($i == 0)
                    $fq .= str_replace(" ", "*", $bo->commodity[$i]);//$bo->commodity[$i];
                else
                    $fq .= " OR " . str_replace(" ", "*", $bo->commodity[$i]);
            }
            $fq .= ") ";
        }
        if (isset($bo->containerType) && sizeof($bo->containerType) > 0) {
            $fq .= " AND containerType:(";
            for ($i = 0; $i < sizeof($bo->containerType); $i++) {
                if ($i == 0)
                    $fq .= str_replace(" ", "*", $bo->containerType[$i]);//$bo->containerType[$i];
                else
                    $fq .= " OR " . str_replace(" ", "*", $bo->containerType[$i]);;
            }
            $fq .= ") ";
        }
        if (isset($bo->loadPort) && sizeof($bo->loadPort) > 0) {
            $fq .= " AND loadPort:(";
            for ($i = 0; $i < sizeof($bo->loadPort); $i++) {
                if ($i == 0)
                    $fq .= str_replace(" ", "*", $bo->loadPort[$i]);//$bo->loadPort[0];
                else
                    $fq .= " OR " . str_replace(" ", "*", $bo->loadPort[$i]);//$bo->loadPort[0];
            }
            $fq .= ") ";
        }
        if (isset($bo->dischargePort) && sizeof($bo->dischargePort) > 0) {
            $fq .= " AND dischargePort:(";
            for ($i = 0; $i < sizeof($bo->loadPort); $i++) {
                if ($i == 0)
                    $fq .= str_replace(" ", "*", $bo->dischargePort[$i]);//$bo->dischargePort[$i];
                else
                    $fq .= " OR " . str_replace(" ", "*", $bo->dischargePort[$i]);//$bo->dischargePort[$i];
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

        //TODO: Check if CargoReadyDate should be an exact match or a lesser than match
        /*if(isset($bo->cargoReadyDate)){
            $fq .= "AND cargoReadyDate:" . $bo->cargoReadyDate;
        }*/
        Log::info($fq);
        return $fq;
    }

    public function addAditionalData($jsonResponse)
    {
        $buyerPostData = array();
        $jsonResponse = json_decode($jsonResponse);
        $groups = $jsonResponse->grouped->postId->groups;
        foreach ($groups as $group) {
            $docs = $group->doclist->docs;
            $sellerQuote = $this->getSellerQuoteStautsForPostmaster($docs);
            //if(JWTAuth::parseToken()->getPayload()->get('role') == "Seller"){
            if (sizeof($sellerQuote) > 0) {
                $docs[0]->sellerQuoteId = $sellerQuote['id'];
                $docs[0]->sellerQuoteStatus = $sellerQuote['status'];
                $docs[0]->isSellerAccepted = $this->getContractSellerAccepted($docs);//$sellerQuote['isSellerAccepted'];
                $docs[0]->isContractGenerated = $this->getContractInfromation($docs);//$sellerQuote['isContractGenerated'];
            } else {
                $docs[0]->sellerQuoteId = '';
                $docs[0]->sellerQuoteStatus = '';
                $docs[0]->isContractGenerated = '';
                $docs[0]->isSellerAccepted = '';
            }
            //}
            /*else{
                $docs[0]->notificationCounts = $this->getNotificationCounts($docs[0]->postId);
            }*/
            $docs[0]->notificationCounts = $this->getNotificationCounts($docs[0]->postId);
        }
        return $jsonResponse;
    }

    public function getSellerQuoteStautsForPostmaster($docs)
    {
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $postId = $docs[0]->postId;
        if (SellerQuotes::where('buyerPostId', '=', $postId)->where('sellerId', '=', $userId)->exists()) {
            $sellerQuoteStatus = SellerQuotes::where('buyerPostId', '=', $postId)
                ->where('sellerId', '=', $userId)
                ->select('status', 'id', 'isSellerAccepted')->get()->toArray();
        } else
            $sellerQuoteStatus = array();
        if (sizeof($sellerQuoteStatus) > 0) {
            return $sellerQuoteStatus[0];
        } else
            return [];
    }

    public function getContractSellerAccepted($docs)
    {
        $postId = $docs[0]->postId;
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        //Load model object
        //$buyerContract = BuyerContract::where('buyerPostId', '=', $id, )

        if (BuyerContract::where('buyerPostId', '=', $postId)->where('sellerId', '=', $userId)->exists()) {
            $sellerQuoteStatus = BuyerContract::where('buyerPostId', '=', $postId)
                ->where('isSellerAccepted', '=', 0)
                ->where('sellerId', '=', $userId)
                ->select("*")->get()->toArray();
        } else
            $sellerQuoteStatus = array();
        if (sizeof($sellerQuoteStatus) > 0) {
            return 0;
        } else
            return 1;
    }

    public function getContractInfromation($docs)
    {
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $postId = $docs[0]->postId;
        if (SellerQuotes::where('buyerPostId', '=', $postId)->where('sellerId', '=', $userId)->exists()) {
            $sellerQuoteStatus = SellerQuotes::where('buyerPostId', '=', $postId)
                ->where('isContractGenerated', '=', 1)
                ->where('sellerId', '=', $userId)
                ->select('isContractGenerated', 'isSellerAccepted')->get()->toArray();
        } else
            $sellerQuoteStatus = array();
        if (sizeof($sellerQuoteStatus) > 0) {
            return $sellerQuoteStatus[0]['isContractGenerated'];
        } else
            return [];
    }

}