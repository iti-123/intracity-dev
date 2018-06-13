<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 2/18/2017
 * Time: 12:40 PM
 */

namespace Api\Framework;

use Api\BusinessObjects\SellerQuoteBO;
use Api\BusinessObjects\ValidationError;
use Api\Services\UserDetailsService;
use Api\Utils\DateUtils;
use DB;
use Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class AbstractSellerQuoteValidator implements ISellerQuoteValidator
{
    function validateGet()
    {
    }

    function validateSave(SellerQuoteBO $bo)
    {

        LOG::info("Performing basic SellerQuote Validations");

        $errors = [];
        $unixNow = DateUtils::unixNow();

        if (JWTAuth::parseToken()->getPayload()->get('role') == "Seller") {
            $userId = JWTAuth::parseToken()->getPayload()->get('id');
            if ($bo->sellerId != $userId) {
                array_push($errors, array(101 => "Seller not match"));
            }
        }

        if ($bo->sellerId) {
            //array_push($errors, array(103=>"validTill not valid"));
            $errors = array_merge($errors, $this->sellerServiceSubscription($bo->sellerId));
        }

        if (empty($bo->validTill)) {
            array_push($errors, array(103 => "validTill Date Required"));
        } else if ((int)$bo->validTill < $unixNow) {
            array_push($errors, array(103 => "Invalid ValidTill Date"));
        }

        if (!isset($bo->buyerId)) {
            array_push($errors, new ValidationError(100, "No buyer id"));
        }

        if (!isset($bo->sellerId)) {
            array_push($errors, new ValidationError(100, "No seller id"));
        }

        if (!isset($bo->buyerPostId)) {
            array_push($errors, new ValidationError(101, "Buyer post id missing"));
        }

        /*if($bo->sellerId != JWTAuth::parseToken()->getPayload()->get('id')) {
            array_push( $errors, new ValidationError(102, "Not authorized buyer") );
        }*/
        //dd(JWTAuth::parseToken()->getPayload()->get('role'));
        if (JWTAuth::parseToken()->getPayload()->get('role') == "SELLER") {
            $sIds = explode(",", JWTAuth::parseToken()->getPayload()->get('sIds'));
            if (!in_array($bo->serviceId, $sIds)) {
                array_push($errors, new ValidationError(103, "Not authorized to selected service"));
            }
        }


        LOG::info("Finished Performing basic Buyerpost Validations. Found " . sizeof($errors) . " error(s)");

        LOG::info($errors);

        return $errors;
    }

    public function sellerServiceSubscription($usrid)
    {
        $errors = [];
        $result = DB::table("seller_services as ss")
            ->leftjoin('lkp_services as services', 'services.id', '=', 'ss.lkp_service_id')
            ->where(array('ss.user_id' => $usrid))
            ->where(array('ss.lkp_service_id' => 2))
            ->select('services.service_name as services')
            ->get();
        if (sizeof($result) <= 0) {
            $errors[] = array(112 => "User(" . UserDetailsService::getUserDetails($usrid)->username . ") does not have required services subscription");
        }
        $currentdate = date('Y-m-d');

        $subscription = DB::table('user_subscription_services')
            ->where(array('user_id' => $usrid))
            ->select('subscription_enddate', 'user_id')->get();
        foreach ($subscription as $subscrip) {
            $diff = abs(strtotime($currentdate) - strtotime($subscrip->subscription_enddate));
            $years = floor($diff / (365 * 60 * 60 * 24));
            $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
            $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
            if ($days == 0) {
                $errors[] = array(113 => "User(" . UserDetailsService::getUserDetails($usrid)->username . ") subscription expired");
            }
        }


        return $errors;
    }

    function validateDelete()
    {
    }

}