<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 2/8/17
 * Time: 7:23 PM
 */

namespace ApiV2\Framework;


use ApiV2\BusinessObjects\BuyerPostBO;
use ApiV2\BusinessObjects\ContractBO;
use ApiV2\Services\UserDetailsService;
use ApiV2\Utils\DateUtils;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\ValidationBuilder;
use Carbon\Carbon;
use DB;
use Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class AbstractBuyerPostValidator implements IBuyerPostValidator
{

    protected $current;


    function validateGet()
    {
    }

    /** postId: 62,
     * title: AF_Spot4General,
     * buyerId: 100001,
     * serviceId: 24,
     * leadType: spot,
     * transactionId: AirFreight/2017/000062,
     * lastDateTimeOfQuoteSubmission: 1491814800,
     * visibleToSellers: [],
     * viewCount: 0,
     * isPublic: false,
     * createdBy: null,
     * status: draft,
     * updatedBy: null,
     * createdAt: null,
     * updatedAt: null,
     * isTermAccepted: true,
     * version: 1,
     * userTimezone: Asia/Kolkata
     * */

    /**
     * @param BuyerPostBO $bo
     * @return array
     */
    function validateSave(BuyerPostBO $bo)
    {

        $validationBuilder = ValidationBuilder::create();

        $current = Carbon::now();
        LOG::info("Performing basic Buyerpost Validations");
        $errors = [];

        if (!isset($bo->buyerId) || ($bo->buyerId == '')) {
            $validationBuilder->error("buyerId", "Invalid buyer details");
        }

        if (!isset($bo->title) || ($bo->title == '')) {
            $validationBuilder->error("title", "Title Cannot be empty");
        }

        if ($bo->buyerId != JWTAuth::parseToken()->getPayload()->get('id')) {
            throw new UnauthorizedException("buyer is not the owner of this post");
        }

        if (!isset($bo->isTermAccepted) || empty($bo->isTermAccepted)) {
            $validationBuilder->error("isTermAccepted", "Term & Conditions not accepted");
        }

        if ((empty($bo->lastDateTimeOfQuoteSubmission)) || ($bo->lastDateTimeOfQuoteSubmission <= strtotime($current))) {
            $validationBuilder->error("lastDateTimeOfQuoteSubmission", "Quote submission Date/Time cannot be empty or less than current date/time");
        }

        $validationBuilder->raise();

        return;

        if ((!isset($bo->isPublic) || $bo->isPublic == 'false') && $bo->leadType == 'spot') {
            foreach ($bo->visibleToSellers as $sellerId) {
                $errors = array_merge($errors, $this->sellerServiceSubscription($sellerId, $bo));
            }
        }
        if (!empty($bo->visibleToSellers)) {
            if ($bo->isPublic == true) {
                $errors[109] = "Quote cannot be public and private to sellers at once";
            }
            if ($bo->leadType == 'spot') {
                foreach ($bo->visibleToSellers as $sellerId) {
                    $errors = array_merge($errors, $this->sellerServiceSubscription($sellerId, $bo));
                }
            }
        }


    }

    private function sellerServiceSubscription($usrid, $bo)
    {

        $cargoReadyDate = $bo->attributes->route->cargoReadyDate;
        $currentdate = date('Y-m-d');
        $getDate = '';
        $subscriptionDate = '';
        $now = Carbon::now();

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


        $subscription = DB::table('user_subscription_services')
            ->where(array('user_id' => $usrid))
            ->select('subscription_enddate', 'user_id')->get();
        foreach ($subscription as $subscrip) {

            $days = DateUtils::diffInDays($subscrip->subscription_enddate, $currentdate);
            $cargoReadyDate = DateUtils::diffInDays1($subscrip->subscription_enddate, $now);

            if ($days == 0) {
                $errors[] = array(113 => "User(" . UserDetailsService::getUserDetails($usrid)->username . ") subscription expired");
            }
            if ($cargoReadyDate == 0) {
                $errors[] = array(114 => "Cargo Ready Date should not be beyond Sellers subscription expiry of" . UserDetailsService::getUserDetails($usrid)->username);
            }

        }


        return $errors;
    }

    function validateContractSave(ContractBO $bo)
    {
        $errors = [];
        $unixNow = DateUtils::unixNow();
        if (empty($bo->title)) {
            array_push($errors, array(101 => "Title Required"));
        }

        if (empty($bo->serviceId)) {
            array_push($errors, array(102 => "serviceId Required"));
        }

        if (empty($bo->buyerPostId)) {
            array_push($errors, array(103 => "buyerPostId Required"));
        }

        if (empty($bo->sellerId)) {
            array_push($errors, array(104 => "sellerId Required"));
        }

        if (empty($bo->validFrom)) {
            array_push($errors, array(105 => "validFrom Required"));
        }/*else if((int)$bo->validFrom >= $unixNow){
            array_push($errors, array(106=>"Invalid validFrom, must be greater than Today's date"));
        }*/

        if (empty($bo->validTo)) {
            array_push($errors, array(107 => "validTo Required"));
        }/*else if($bo->validFrom > $bo->validTo){
            array_push($errors, array(108=>"Invalid validTo, must be greater than validFrom date"));
        }*/

        if (empty($bo->status)) {
            array_push($errors, array(109 => "status Required"));
        }

        if (empty($bo->awardType)) {
            array_push($errors, array(110 => "awardType Required"));
        }


        LOG::info("Finished Performing basic BuyerContract Validations. Found " . sizeof($errors) . " error(s)");
        return $errors;
    }

    function validateDelete()
    {
    }

    /***
     * Calculate Subscription validation
     */

    public function calcDateDiff($getDate, $subscriptionDate)
    {
        $diff = abs(strtotime($getDate) - strtotime($subscriptionDate));
        $years = floor($diff / (365 * 60 * 60 * 24));
        $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
        $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
        return $days;
    }

    public function calculateChargeableWeight($totalCBM, $dimUnit, $grossWeight, $weightUnit)
    {

        // echo  "volumetricWeightCalculator";
    }

}