<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 2/9/2017
 * Time: 9:09 PM
 */

namespace Api\Modules\FCL;

use Api\BusinessObjects\SellerPostBO;
use Api\BusinessObjects\SellerPostSearchBO;
use Api\Framework\ISellerPostIndexer;
use Api\Model\FCLSearchSellerPost;
use Api\Model\FCLSellerPostIndex;
use Api\Services\SolrSearchService;
use Api\Services\UserDetailsService;
use App\Exceptions\ApplicationException;
use Log;
use Tymon\JWTAuth\Facades\JWTAuth;


class FCLSellerPostIndexer implements ISellerPostIndexer
{


    public function rebuildIndex(SellerPostBO $bo)
    {

        try {

            LOG::info("Rebuilding search index for sellerpost " . $bo->postId);

            $sellerPostIndexes = [];

            $index = [];
            $now = date('Y-m-d H:i:s');
            $index['entity'] = "sellerpost";
            $index['serviceSubType'] = $bo->serviceSubType;
            $index['postId'] = $bo->postId;
            $index['serviceId'] = $bo->serviceId;
            $index['serviceName'] = "FCL";
            $index['sellerId'] = $bo->sellerId;
            $index['sellerName'] = JWTAuth::parseToken()->getPayload()->get('firstname');
            $index['title'] = $bo->title;
            $index['validFrom'] = $bo->validFrom;
            $index['validTo'] = $bo->validTo;
            $index['isPublic'] = $bo->isPublic;
            $index['status'] = $bo->status;
            $index['isDeleted'] = 0;
            $index['created_at'] = $now;
            $index['updated_at'] = $now;

            $attrs = $bo->attributes;

            $globalDiscounts = $attrs->discount;

            //Flatten each Port Pair and collect index records
            if (count($attrs->portPair) > 0) {

                foreach ($attrs->portPair as $portPair) {

                    $this->flattenPortPair($index, $portPair, $sellerPostIndexes, $globalDiscounts, $bo->isPublic);

                }
            }

            LOG::info("Pushing " . count($sellerPostIndexes) . " entries to index table for SellerPostId = " . $bo->postId);

            //Push collected index records to database
            FCLSearchSellerPost::insert($sellerPostIndexes);

            LOG::info("Finished rebuilding search index for sellerpost " . $bo->postId);

            $service = new SolrSearchService();
            $service->deltaImport("sellerposts");

            return true;

        } catch (\Exception $e) {

            LOG::error($e);

            throw new ApplicationException([], ["Failed posting sellerpost to Search Store"]);

        }

    }


    private function flattenPortPair(array $index, FCLSellerPortPair $portPair, array &$sellerPostIndexes, array $globalDiscounts = [], $ispublic = false)
    {

        LOG::debug("Flattening port pair [" . $portPair->loadPort . " - " . $portPair->dischargePort . "]");

        $allDiscounts = [];

        $localDiscounts = $portPair->discount;

        //Consider global discounts
        if (count($globalDiscounts) > 0) {
            foreach ($globalDiscounts as $elem) {
                array_push($allDiscounts, $elem);
            }
        }

        //Consider local discounts
        if (count($localDiscounts) > 0) {
            foreach ($localDiscounts as $elem) {
                array_push($allDiscounts, $elem);
            }
        }

        //Consider public access (no privatebuyer)
        if ($ispublic == true) {
            $public = new Discount();
            $public->buyerId = 0;
            array_push($allDiscounts, $public);
        }

        LOG::debug("Flattening " . count($allDiscounts) . " discounts(s), global=" . count($globalDiscounts) . ", local=" . count($localDiscounts) . ", public=" . $ispublic);

        //Flatten for each discounted rate card level or port level buyer or public
        foreach ($allDiscounts as $discount) {

            //dd($discount);

            LOG::debug("Flattening discount for buyer [" . $discount->buyerId . "]");

            $indexExtended = $index;

            $indexExtended['loadPort'] = $portPair->loadPort;
            $indexExtended['dischargePort'] = $portPair->dischargePort;

            $indexExtended['discountBuyerId'] = $discount->buyerId;
            $indexExtended['discountType'] = $discount->discountType;
            $indexExtended['discount'] = $discount->discount;
            $indexExtended['creditDays'] = $discount->creditDays;

            if ($discount->buyerId != 0) {
                //This is a private buyer
                $indexExtended['discountBuyerName'] = UserDetailsService::getUserDetails($discount->buyerId)->username;
            } else {
                $indexExtended['discountBuyerName'] = "";
            }


            if (count($portPair->carriers) > 0) {
                foreach ($portPair->carriers as $carrier) {

                    $this->flattenCarrier($indexExtended, $carrier, $sellerPostIndexes);
                }
            }

        }

    }

    private function flattenCarrier(array $index, SellerCarriers $carrier, array &$sellerPostIndexes)
    {

        LOG::debug("Flattening carrier [" . $carrier->carrierName . "]");

        $indexExtended = $index;

        $indexExtended['carrierName'] = $carrier->carrierName;
        $indexExtended['tracking'] = $carrier->tracking;
        $indexExtended['transitDays'] = $carrier->transitDays;


        if (count($carrier->containers) > 0) {

            foreach ($carrier->containers as $container) {

                $this->flattenContainer($indexExtended, $container, $sellerPostIndexes);

            }

        }

    }


    private function flattenContainer(array $index, SellerContainer $container, array &$sellerPostIndexes)
    {

        LOG::debug("Flattening container [" . $container->containerType . "]");

        $indexExtended = $index;

        $indexExtended['containerType'] = $container->containerType;

        //TODO: Where do I get the containerQuantity from ?
        //$indexExtended['containerType'] = ???

        $fctotal = 0;
        if (count($container->freightCharges) > 0) {
            LOG::debug('Found Freight Charges ' . count($container->freightCharges));
            foreach ($container->freightCharges as $freightCharge) {
                LOG::debug((array)$freightCharge);
                $fctotal += $freightCharge->amount;
            }
        }

        $lctotal = 0;
        if (count($container->localCharges) > 0) {
            foreach ($container->localCharges as $localCharge) {
                $lctotal += $localCharge->amount;
            }
        }
        $indexExtended['freightCharges'] = $fctotal;
        $indexExtended['freightChargesCurrency'] = "USD";
        $indexExtended['localCharges'] = $lctotal;
        $indexExtended['localChargesCurrency'] = "INR";

        //var_dump($indexExtended);

        array_push($sellerPostIndexes, $indexExtended);

    }


    public function rebuildIndex2(SellerPostBO $bo)
    {

        LOG:
        info("Rebuilding search index for sellerpost " . $bo->postId);

        try {
            $discountedBuyer = $indexer = $sellerRateCard = array();

            $discountedBuyer['entity'] = "sellerpost";
            $discountedBuyer['postId'] = $bo->postId;
            $discountedBuyer['serviceId'] = $bo->serviceId;
            $discountedBuyer['serviceName'] = "FCL";
            $discountedBuyer['sellerId'] = $bo->sellerId;
            $discountedBuyer['sellerName'] = JWTAuth::parseToken()->getPayload()->get('firstname');
            $discountedBuyer['title'] = $bo->title;
            $discountedBuyer['validFrom'] = $bo->validFrom;
            $discountedBuyer['validTo'] = $bo->validTo;
            $discountedBuyer['isPublic'] = $bo->isPublic;
            $discountedBuyer['status'] = $bo->status;
            $discountedBuyer['isDeleted'] = 0;


            $fclSellerPostAttributes = $bo->attributes;
            if (!empty($fclSellerPostAttributes)) {
                $indexer = array_merge($indexer, $this->getDiscountToBuyers($fclSellerPostAttributes, $discountedBuyer));
                foreach ($indexer as $index) {
                    foreach ($index as $obj) {
                        $sellerRateCard[] = $obj;
                    }
                }
                //Log::info(json_encode($sellerRateCard));
                $sellerRateCard = $sellerRateCard->unique();//array_unique($sellerRateCard);
                FCLSearchSellerPost::insert($sellerRateCard);
            }

            return true;

        } catch (\Exception $e) {

            LOG::error($e);

            throw new ApplicationException([], ["Failed posting sellerpost to Search Store"]);
        }
    }

    public function getDiscountToBuyers(FCLSellerPostAttributes $attributes, $discountedBuyer)
    {
        $discountedArray = array();
        foreach ($attributes->discount as $discounte) {
            $discountedBuyer['discountBuyerId'] = $discounte->buyerId;
            $discountedBuyer['discountBuyerName'] = UserDetailsService::getUserDetails($discounte->buyerId)->username;
            $discountedBuyer['discountType'] = $discounte->discountType;
            $discountedBuyer['discount'] = $discounte->discount;
            $discountedBuyer['creditDays'] = $discounte->creditDays;
            $discountedArray = array_merge($discountedArray, $this->getBuyersPortPairs($attributes, $discountedBuyer));
        }
        return $discountedArray;
    }

    public function getSellersPortPairs(FCLSellerPostAttributes $attributes, $discountedBuyer)
    {
        $discountedBuyers = $portLevelDiscount = $continerLevel = $portContainerLevel = array();
        foreach ($attributes->portPair as $portPair) {
            $discountedBuyer['loadPort'] = $portPair->loadPort;
            $discountedBuyer['dischargePort'] = $portPair->dischargePort;
            $continerLevel = array_merge($continerLevel, $this->fillContainers($portPair, $discountedBuyer));
            if (sizeof($portPair->discount) > 0) {
                foreach ($discountedBuyer as $key => $value) {
                    $portLevelDiscount[$key] = $value;
                }
                foreach ($portPair->discount as $discounte) {
                    $portLevelDiscount['discountBuyerId'] = $discounte->buyerId;
                    $portLevelDiscount['discountBuyerName'] = UserDetailsService::getUserDetails($discounte->buyerId)->username;
                    $portLevelDiscount['discountType'] = $discounte->discountType;
                    $portLevelDiscount['discount'] = $discounte->discount;
                    $portLevelDiscount['creditDays'] = $discounte->creditDays;
                    $portLevelDiscount['loadPort'] = $portPair->loadPort;
                    $portLevelDiscount['dischargePort'] = $portPair->dischargePort;
                    $portContainerLevel = array_merge($portContainerLevel, $this->fillContainers($portPair, $portLevelDiscount));
                }
                array_push($discountedBuyers, $portContainerLevel);
            }
            $discountedBuyers[] = $continerLevel;
        }
        return $discountedBuyers;
    }

    function fillContainers(FCLSellerPortPair $portPair, $discountedBuyer)
    {
        $containerLevel = $carriersLevel = array();
        $fclSearchRec = array();
        if (!empty($portPair)) {
            $fclCarriers = $portPair->carriers;
            foreach ($fclCarriers as $carriers) {
                $fclSearchRec["tracking"] = $carriers->tracking;
                $fclSearchRec['transitDays'] = $carriers->transitDays;
                foreach ($carriers->containers as $container) {
                    foreach ($discountedBuyer as $key => $value) {
                        $fclSearchRec[$key] = $value;
                    }
                    $fclSearchRec['containerType'] = $container->containerType;
                    $fclSearchRec['freightCharges'] = $this->sumOfCharges($container, "freightCharges");
                    $fclSearchRec['freightChargesCurrency'] = "USD";
                    $fclSearchRec['localCharges'] = $this->sumOfCharges($container, "localCharges");
                    $fclSearchRec['localChargesCurrency'] = "INR";
                    //$containerLevel = array_push($containerLevel, $fclSearchRec);
                    $containerLevel[] = $fclSearchRec;
                }
            }
        }

        return $containerLevel;
    }

    public function sumOfCharges(SellerContainer $container, $chargeType)
    {
        $charges = $container->$chargeType;
        $amount = 0;

        foreach ($charges as $charge) {
            $amount += (int)$charge->amount;
        }
        return $amount;
    }


    /**
     * @param $postId
     * @throws ApplicationException
     */
    public function dropIndex($postId)
    {
        LOG::info('Dropping existing search index for sellerpost = ' . $postId);
        try {

            $delQuery = "{'delete': {'query': 'entity:sellerpost and sellerPostId:$postId'}}";

            $service = new SolrSearchService();
            $service->remove($delQuery);

        } catch (Exception $e) {

            LOG::error($e);

            throw new ApplicationException(["postid" => $postId], ["Error deleting existing sellerposts from search store"]);
        }
    }

    public function searchIndex(SellerPostSearchBO $bo)
    {

        $jsonResponse = null;

        LOG::info('Searching index for sellerposts');

        try {

            $fq = $this->generateSearchQuery($bo);
            LOG::info("filter query generated is " . $fq);

            $service = new SolrSearchService();

            //TODO: Add additional facet field named Delivery date. But what is this field?
            $facets = ["fields" => ["seller", "containerType", "tracking"], "ranges" => []];

            //$groups = ["postId"];

            $jsonResponse = $service->search(null, $fq, $facets, $bo->start, $bo->rows, "updated_at%20desc"); //, $groups
            $response = json_decode($jsonResponse, true);
        } catch (Exception $e) {

            LOG::error($e);

            throw new ApplicationException([], ["Error searching sellerposts from search store"]);
        }

        return $response;

    }


    private function generateSearchQuery(FCLSellerPostSearchBO $bo)
    {
        Log::info((array)$bo);
        //Generated query should look like this.
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $fq = "entity:sellerpost AND service:" . FCL;
        $fq .= " AND (isPublic:true";
        $fq .= " OR (isPublic:false AND discountBuyer:" . $userId . "*))";


        if (isset($bo->seller) && sizeof($bo->seller) > 0) {
            $fq .= " AND seller:(";
            for ($i = 0; $i < sizeof($bo->seller); $i++) {
                if ($i == 0)
                    $fq .= $bo->seller[$i] . "*";
                else
                    $fq .= " OR " . $bo->seller[$i] . "*";
            }
            $fq .= ") ";
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

        /*if(isset($bo->commodity) && sizeof($bo->commodity)>0){
            $fq .= " AND commodity:(";
            for($i=0;$i<sizeof($bo->commodity);$i++){
                if($i == 0)
                    $fq .= str_replace(" ", "*",$bo->commodity[$i]);
                else
                    $fq .= " OR ".str_replace(" ", "*",$bo->commodity[$i]);
            }
            $fq .= ") ";
        }*/

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

        /*if(isset($bo->cargoReadyDate)){
            $fq .= " AND cargoReadyDate:" . $bo->cargoReadyDate;
        }*/

        //TODO: Check if CargoReadyDate should be an exact match or a lesser than match
        /*if(isset($bo->cargoReadyDate)){
            $fq .= "AND cargoReadyDate:" . $bo->cargoReadyDate;
        }*/


        $fqContainers = $fqContainer = '';

        foreach ($bo->containers as $container) {

            if (isset($container->containerType)) {
                $fqContainer .= " containerType:" . str_replace(" ", "*", $container->containerType);
            }

            /*if(isset($container->containerQuantity)) {
                $fqContainer .= " AND containerQuantity:" . str_replace(" ", "*",$container->containerQuantity);
            }

            if(isset($container->grossWeight)) {
                $fqContainer .= " AND grossWeight:" . $container->grossWeight;
            }

            if(isset($container->weightUnit)) {
                $fqContainer .= " AND weightUnit:" . $container->weightUnit;
            }*/

            //$fqContainers = ltrim($fqContainers, ' AND');
            if (empty($fqContainers)) {
                $fqContainers .= "(" . $fqContainer . " )";
            } else
                $fqContainers .= " OR (" . $fqContainer . " )";
            $fqContainer = '';
        }


        //$fqContainers = rtrim($fqContainers, ' OR');

        if (count($bo->containers) > 0) {
            $fq = $fq . "AND (" . $fqContainers . ")";
        }

        return $fq;

    }

    public function postMasterIndex(SellerPostSearchBO $bo)
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

            $jsonResponse = $service->search(null, $fq, $facets, $bo->start, $bo->rows, "updated_at%20desc", $groups);
            $jsonResponse = $this->addAditionalData($jsonResponse);

        } catch (Exception $e) {

            LOG::error($e);

            throw new ApplicationException([], ["Error searching Seller posts from search store"]);
        }

        return $jsonResponse;
    }

    public function generatePostMasterQuery(FCLSellerPostMasterOutboundBO $bo)
    {

        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $fq = "entity:sellerpost AND service:" . FCL . " AND ";

        if (JWTAuth::parseToken()->getPayload()->get('role') == "Seller") {
            $fq .= "seller:" . $userId . "*";
        } else {
            $fq .= "(isPublic:false AND visibleToBuyer:" . $userId . "*)";
        }

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
                    $fq .= str_replace(" ", "*", $bo->containerType[$i]);//$bo->containerType[$i];
                else
                    $fq .= " OR " . str_replace(" ", "*", $bo->containerType[$i]);//$bo->containerType[$i];
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