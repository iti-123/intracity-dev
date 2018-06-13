<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 2/18/2017
 * Time: 10:47 AM
 */

namespace Api\Modules\FCL;

use Api\Framework\ISellerQuoteTransformer;
use Api\Framework\SerializerServiceFactory;
use Api\Model\BuyerContract;
use Api\Model\BuyerPost;
use Api\Model\SellerQuotes;
use Api\Services\UserDetailsService;
use Tymon\JWTAuth\Facades\JWTAuth;

//use Api\Model\BuyerPost;

class FCLSellerQuoteTransformer implements ISellerQuoteTransformer
{
    public function ui2bo_save($payload, $leadType)
    {
        //Convert the request JSON into a BO
        $serializer = SerializerServiceFactory::create();
        if ($leadType == "term") {
            //Delegate request to TermSellerPostService
            $post = $serializer->deserialize($payload, 'array<Api\Modules\FCL\FCLSellerQuoteTermBO>', 'json');
        } else {
            //Delegate request to SpotSellerPostService
            $post = $serializer->deserialize($payload, 'Api\Modules\FCL\FCLSellerQuoteSpotBO', 'json');
        }

        return $post;
    }

    public function model2boGet($model)
    {
        if (!is_array($model)) {
            $model->attributes = json_decode($model->attributes);
            $model->quoteId = $model->id;
            //$model['sellerName'] = UserDetailsService::getUserDetails($model["sellerId"])->username;
            return $model;
        }
        for ($i = 0; $i < sizeof($model); $i++) {
            $model[$i]['quoteId'] = $model[$i]["id"];
            $model[$i]['sellerName'] = UserDetailsService::getUserDetails($model[$i]["sellerId"])->username;
            $model[$i]['attributes'] = json_decode($model[$i]["attributes"]);
            unset($model[$i]["id"]);
        }
        return $model;
    }

    public function model2termBoGet($model)
    {

        if (!is_array($model)) {

            $model->attributes = json_decode($model->attributes);
            $model->quoteId = $model->id;
            //$model['sellerName'] = UserDetailsService::getUserDetails($model["sellerId"])->username;
            return $model;
        }
        $sellerPost = json_decode(BuyerPost::where('id', '=', $model[0]['buyerPostId'])->firstOrFail()->attributes)->serviceType[0]->routes;
        for ($i = 0; $i < sizeof($model); $i++) {
            $model[$i]['quoteId'] = $model[$i]["id"];
            $model[$i]['sellerName'] = UserDetailsService::getUserDetails($model[$i]["sellerId"])->username;
            $model[$i]['totalPortPairs'] = sizeof($sellerPost);
            $model[$i]['attributes'] = json_decode($model[$i]["attributes"]);
            unset($model[$i]["id"]);
        }
        $model = $this->processTermObject($model);
        return $model;
    }

    public function processTermObject($model)
    {
        $portPairs = $sortedArray = [];
        $portPairs = $this->getUniquePortPairs($model);
        $sorted = $this->getSortedPortPairs($portPairs, $model);
        $groupedBySeller = $this->getArrayGroupBy($sorted, "sellerId");
        $sellerQuotes = $this->getTermSellerQuotes($groupedBySeller);
        //$grouped = $this->getArrayGroupBy( $sorted, "loadPort");
        return $sellerQuotes;
    }

    public function getUniquePortPairs($model)
    {
        foreach ($model as $item) {
            $portPairs[] = array('loadPort' => $item["loadPort"], 'dischargePort' => $item["dischargePort"]);
        }
        return array_map("unserialize", array_unique(array_map("serialize", $portPairs)));
    }

    public function getSortedPortPairs($portPairs, $model)
    {
        foreach ($portPairs as $portPair) {

            foreach ($model as $items) {
                if ($portPair["loadPort"] == $items["loadPort"] && $portPair["dischargePort"] == $items["dischargePort"]) {
                    $sortedArray[] = $items;
                }
            }
        }
        $sorted = $this->array_msort($sortedArray, array('totalFreightCharges' => SORT_ASC));
        $sorted = array_values($sorted);
        foreach ($portPairs as $portPair) {
            $rank = 1;
            for ($i = 0; $i < sizeof($sorted); $i++) {
                if ($portPair["loadPort"] == $sorted[$i]["loadPort"] && $portPair["dischargePort"] == $sorted[$i]["dischargePort"]) {
                    $sorted[$i]['rank'] = $rank;
                    $rank++;
                }
            }
        }
        return $sorted;
    }

    public function array_msort($array, $cols)
    {
        $colarr = array();
        foreach ($cols as $col => $order) {
            $colarr[$col] = array();
            foreach ($array as $k => $row) {
                $colarr[$col]['_' . $k] = strtolower($row[$col]);
            }
        }
        $eval = 'array_multisort(';
        foreach ($cols as $col => $order) {
            $eval .= '$colarr[\'' . $col . '\'],' . $order . ',';
        }
        $eval = substr($eval, 0, -1) . ');';
        eval($eval);
        $ret = array();
        foreach ($colarr as $col => $arr) {
            foreach ($arr as $k => $v) {
                $k = substr($k, 1);
                if (!isset($ret[$k])) $ret[$k] = $array[$k];
                $ret[$k][$col] = $array[$k][$col];
            }
        }
        return $ret;

    }

    public function getArrayGroupBy(array $array, $key)
    {
        if (!is_string($key) && !is_int($key) && !is_float($key) && !is_callable($key)) {
            trigger_error('getArrayGroupBy(): The key should be a string, an integer, or a callback', E_USER_ERROR);
            return null;
        }
        $func = (is_callable($key) ? $key : null);
        $_key = $key;
        // Load the new array, splitting by the target key
        $grouped = [];
        foreach ($array as $value) {
            if (is_callable($func)) {
                $key = call_user_func($func, $value);
            } elseif (is_object($value) && isset($value->{$_key})) {
                $key = $value->{$_key};
            } elseif (isset($value[$_key])) {
                $key = $value[$_key];
            } else {
                continue;
            }
            $grouped[$key][] = $value;
        }
        // Recursively build a nested grouping if more parameters are supplied
        // Each grouped array value is grouped according to the next sequential key
        if (func_num_args() > 2) {
            $args = func_get_args();
            foreach ($grouped as $key => $value) {
                $params = array_merge([$value], array_slice($args, 2, func_num_args()));
                $grouped[$key] = call_user_func_array('getArrayGroupBy', $params);
            }
        }
        return $grouped;
    }

    public function getTermSellerQuotes($groupedBySeller)
    {
        $sellerGroup = [];
        foreach ($groupedBySeller as $sellerQuote) {
            $sellerGroup[] = $this->getSellerFinalObject($sellerQuote);
        }
        return $sellerGroup;
    }

    public function getSellerFinalObject($sellerQuote)
    {
        $sellerQuoteArray = [];
        $counter = 1;
        $quotedPortPairs = sizeof($sellerQuote);
        foreach ($sellerQuote as $value) {
            $sellerQuoteArray["buyerPostId"] = $value["buyerPostId"];
            $sellerQuoteArray["serviceId"] = $value["serviceId"];
            $sellerQuoteArray["sellerId"] = $value["sellerId"];
            $sellerQuoteArray["sellerName"] = $value["sellerName"];
            $sellerQuoteArray["createdBy"] = $value["createdBy"];
            $sellerQuoteArray["isSellerAccepted"] = $value["isSellerAccepted"];
            $sellerQuoteArray["isBuyerAccepted"] = $value["isBuyerAccepted"];
            $sellerQuoteArray["contractStatus"] = $this->getContractStatuts($value);//$value["isBuyerAccepted"];
            $sellerQuoteArray["status"] = $value["status"];
            $sellerQuoteArray["awardType"] = $value["awardType"];
            $sellerQuoteArray["quotedPortPairs"] = $quotedPortPairs . "/" . $value["totalPortPairs"];
            $sellerQuoteArray["percentage"] = ($quotedPortPairs / $value["totalPortPairs"]) * 100;
        }
        $rankGrouped = $this->getArrayGroupBy($sellerQuote, "rank");
        foreach ($rankGrouped as $key => $quote) {
            foreach ($quote as $items) {
                $sellerQuoteArray["attributes"]['l' . $key][] = array(
                    'quoteId' => $items['quoteId'],
                    'isContractGenerated' => $items['isContractGenerated'],
                    'loadPort' => $items['loadPort'],
                    'dischargePort' => $items['dischargePort'],
                    'attributes' => $items['attributes'],
                    'status' => $items["status"],
                    'containers' => $this->getContainersList($items, $sellerQuoteArray),//$items['attributes']->containers,//
                    'commodity' => $items['attributes']->commodity,
                    //'quantity' => $rankGrouped[$i]['loadPort'],
                    'totalFreightCharges' => $items['totalFreightCharges'],
                    'rank' => $items['rank']
                    //'contractedFreightCharge' => $rankGrouped[$i]['loadPort'],
                    //'contractedQuantity' => $rankGrouped[$i]['loadPort']
                );
            }
            //$counter++;
        }
        //dd($sellerQuoteArray);

        return $sellerQuoteArray;

    }

    public function getContractStatuts($items)
    {
        $postId = $items['buyerPostId'];
        $buyerId = JWTAuth::parseToken()->getPayload()->get('id');
        $buyerContractedContainers = BuyerContract::where('buyerPostId', $postId)
            ->where('sellerId', $items['sellerId'])
            ->where('buyerId', $buyerId)->select('status')->get()->toArray();
        if (sizeof($buyerContractedContainers) > 0)
            return $buyerContractedContainers[0]["status"];
        else
            return "";
    }

    public function getContainersList($items, $sellerQuoteArray = '')
    {
        $postId = $sellerQuoteArray['buyerPostId'];
        $buyerId = JWTAuth::parseToken()->getPayload()->get('id');
        $containers = $containersArray = [];
        $buyerContractedContainers = BuyerContract::where('buyerPostId', $postId)
            ->where('sellerId', $items['sellerId'])
            ->where('buyerId', $buyerId)->get()->toArray();

        if (sizeof($buyerContractedContainers) > 0) {
            $portPairs = json_decode($buyerContractedContainers[0]["attributes"])->portPairs;
            foreach ($portPairs as $portPair) {
                if ($items["quoteId"] == $portPair->quoteId) {
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
        return $items['attributes']->containers;
    }

    public function model2boGetBuyerPost($bpIds)
    {
        $privateBuyerPosts = array();
        $buyerPosts = BuyerPost::find($bpIds);
        $privateBuyerPosts = $this->PostModel2boGet($buyerPosts);
        return $privateBuyerPosts;
    }

    public function PostModel2boGet($models)
    {
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $buyerPosts = array();
        $sellerQuoteStatus = "";
        for ($i = 0; $i < sizeof($models); $i++) {
            if (SellerQuotes::where('buyerPostId', '=', $models[$i]["id"])->where('sellerId', '=', $userId)->exists()) {
                $sellerQuoteStatus = SellerQuotes::where('buyerPostId', '=', $models[$i]["id"])
                    ->where('sellerId', '=', $userId)
                    ->select('status')->get()->toArray();
            } else
                $sellerQuoteStatus = array();

            $buyerPosts[$i] = $models[$i]["attributes"];
            $buyerPosts[$i]['postId'] = $buyerPosts[$i]["id"];
            $buyerPosts[$i]['attributes'] = json_decode($buyerPosts[$i]["attributes"]);
            if (sizeof($sellerQuoteStatus) > 0) {
                $buyerPosts[$i]['sellerQuoteStatus'] = $sellerQuoteStatus[0]['status'];
            } else
                $buyerPosts[$i]['sellerQuoteStatus'] = '';
            unset($models[$i]["id"]);
        }
        return $buyerPosts;
    }

    public function array_group_by($arr, $group_by_fields = false, $sum_by_fields = false)
    {
        if (empty($group_by_fields)) return; // * nothing to group

        $fld_count = 'grp:count'; // * field for count of grouped records in each record group

        // * format sum by
        if (!empty($sum_by_fields) && !is_array($sum_by_fields)) {
            $sum_by_fields = array($sum_by_fields);
        }

        // * protected  from collecting
        $fields_collected = array();

        // * do
        $out = array();
        foreach ($arr as $value) {
            $newval = array();
            $key = '';
            foreach ($group_by_fields as $field) {
                $key .= $value[$field] . '_';
                $newval[$field] = $value[$field];
                unset($value[$field]);
            }
            // * format key
            $key = substr($key, 0, -1);

            // * count
            if (isset($out[$key])) { // * record already exists
                $out[$key][$fld_count]++;
            } else {
                $out[$key] = $newval;
                $out[$key][$fld_count] = 1;
            }
            $newval = $out[$key];

            // * sum by
            if (!empty($sum_by_fields)) {
                foreach ($sum_by_fields as $sum_field) {
                    if (!isset($newval[$sum_field])) $newval[$sum_field] = 0;
                    $newval[$sum_field] += $value[$sum_field];
                    unset($value[$sum_field]);
                }
            }

            // * collect differencies
            if (!empty($value))
                foreach ($value as $field => $v) if (!is_null($v)) {
                    if (!is_array($v)) {
                        $newval[$field][$v] = $v;
                    } else $newval[$field][join('_', $v)] = $v; // * array values
                }

            $out[$key] = $newval;
        }
        return array_values($out);
    }
}