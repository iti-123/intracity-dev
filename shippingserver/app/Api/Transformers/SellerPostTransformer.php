<?php

namespace Api\Transformers;

use Api\Utils\GenericMethods;
use App\SellerPost as SellerPost;
use League\Fractal\TransformerAbstract;
use Validator;

class SellerPostTransformer extends TransformerAbstract
{

    public static function get_seller_post($response = '')
    {
        $seller_post = '';
        if (empty($response))
            $sellerPost = SellerPost::all();
        else
            $sellerPost = SellerPost::all()->where('id', (int)$response)->first();
        $getJsonResponse = GenericMethods::convert_db_object_to_json($sellerPost);
        return response()->json($getJsonResponse);
    }

    /*
     * For Index all items
     *
     */

    public static function save_seller_post($request)
    {
        $dbObj = SellerPostTransformer::convert_json_to_db_object($request);
        return $dbObj;
        // validate
        $rules = unserialize(SELLER_RATE_CARD_RULES);
        $validator = Validator::make($request::all, $rules);
        // process the  login
        if ($validator->fails()) {
            return response()->json($validator);
        } else {
            // store
            $dbObj = SellerPostTransformer::convert_json_to_db_object($request);
            return $dbObj;
        }
    }

    /*
     * For Store JSON items
     *
     */

    private static function convert_json_to_db_object($request)
    {

        $requestObj = json_decode($request);
        $commonFlatFields = unserialize(SELLER_RATE_CARD_FLAT_FIELDS);
        $rateCard = new SellerPost;
        foreach ($commonFlatFields as $key) {
            $rateCard->$key = $requestObj->$key;
            unset($requestObj->$key);
        }
        $rateCard->attributes = json_encode($requestObj);
        if ($rateCard->save())
            return response()->json("Successfully created Rate Card!");
        else
            return response()->json("Could not create Rate Card.");

    }

    public static function update_seller_post($request, $id)
    {
        $dbObj = SellerPostTransformer::update_json_to_db_object($request, $id);
        return $dbObj;
        // validate
        $rules = unserialize(SELLER_RATE_CARD_RULES);
        $validator = Validator::make($request::all, $rules);
        if ($validator->fails()) {
            return response()->json($validator);
        } else {
            // store
            $dbObj = SellerPostTransformer::convert_json_to_db_object($request);
            return $dbObj;
        }
    }

    /*
     * For Store JSON items
     *
     */

    private static function update_json_to_db_object($request, $id)
    {

        $requestObj = json_decode($request);
        $commonFlatFields = unserialize(SELLER_RATE_CARD_FLAT_FIELDS);
        $rateCard = SellerPost::find($id);
        foreach ($commonFlatFields as $key) {
            $rateCard->$key = $requestObj->$key;
            unset($requestObj->$key);
        }
        $rateCard->attributes = json_encode($requestObj);
        if ($rateCard->save())
            return response()->json("Successfully updated Rate Card!");
        else
            return response()->json("Could not updated Rate Card.");

    }

    public function transform(SellerPost $SellerPost)
    {
        $fclOutputJson = GenericMethods::convert_db_object_to_json($SellerPost);
        return $fclOutputJson;
    }
}