<?php
/**
 * Created by PhpStorm.
 * User: chetan
 * Date: 23/2/17
 * Time: 11:53 PM
 */

namespace ApiV2\Modules\RoRo;

use ApiV2\Framework\IBuyerPostTransformer;
use ApiV2\Framework\SerializerServiceFactory;
use ApiV2\Model\SelectedSellers;
use Log;

class RoRoBuyerPostTransformer implements IBuyerPostTransformer
{

    public function ui2bo_save($payload)
    {

        $leadType = json_decode($payload)->leadType;

        //Convert the request JSON into a BO
        $serializer = SerializerServiceFactory::create();
        if ($leadType == "spot") {
            $post = $serializer->deserialize($payload, 'Api\Modules\RoRo\RoRoSpotBuyerPostBO', 'json');
        } else {
            $post = $serializer->deserialize($payload, 'Api\Modules\RoRo\RoRoTermBuyerPostBO', 'json');
        }
        return $post;

    }

    public function ui2bo_filter($payload)
    {
        //Convert the request JSON into a BO
        $serializer = SerializerServiceFactory::create();
        $bo = $serializer->deserialize($payload, 'Api\Modules\RoRo\RoRoBuyerPostSearchBO', 'json');
        return $bo;
    }

    public function model2boGet($model)
    {
        //TODO: Implement model2boGet() method.
        $visibleToSellers = array();
        $model = $model["attributes"];
        $model['postId'] = $model["id"];
        $model['attributes'] = json_decode($model["attributes"]);
        $visibleToSellers = $this->getVisibleToSellersSellerId($model);
        $model['visibleToSellers'] = $visibleToSellers;
        unset($model["id"]);
        return $model;
    }

    public function getVisibleToSellersSellerId($model)
    {
        $sellectedSeller = SelectedSellers::where('post_id', $model["id"])->select('seller_id')->get();
        foreach ($sellectedSeller as $val) {
            $visibleToSellers[] = $val->seller_id;
        }
        return $visibleToSellers;
    }

    public function model2boGetAll($models)
    {
        //TODO: Implement model2boGet() method.
        $visibleToSellers = $model = array();
        for ($i = 0; $i < sizeof($models); $i++) {
            $model[$i] = $models[$i]["attributes"];
            $model[$i]['postId'] = $model[$i]["id"];
            $model[$i]['attributes'] = json_decode($model[$i]["attributes"]);
            $visibleToSellers = $this->getVisibleToSellersSellerId($model[$i]);
            $model[$i]['visibleToSellers'] = $visibleToSellers;
            unset($model[$i]["id"]);
        }
        return $model;
    }

    public function model2boSave($model)
    {
        $response = 'RoRoBuyerPostTransformer.model2boSave() called';
        LOG::info($response);
        return ((array)$response);
    }

    public function bo2modelDelete($bo)
    {
        $response = 'RoRoBuyerPostTransformer.bo2modelDelete() called';
        LOG::info($response);
        return ((array)$response);
    }

    public function model2boDelete($model)
    {
        $response = 'RoRoSellerPostTransformer.model2boDelete() called';
        LOG::info($response);
        return ((array)$response);
    }


    public function bo2modelGet($bo)
    {

        //$mapper = new JsonMapper();
        //$contactObject = $mapper->map($bo, new SellerPostBO());

        $response = 'RoRoBuyerPostTransformer.bo2modelGet() called';
        LOG::info($response);
        return ((array)$response);
    }

}