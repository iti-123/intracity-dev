<?php

namespace Api\Services;

use Api\BusinessObjects\CartItemsBO;
use Api\Model\BuyerContract;
use Api\Model\BuyerPost;
use Api\Model\CartItem;
use Api\Model\SellerPost;
use Api\Model\SellerQuotes;
use Api\Requests\BaseShippingResponse as resp;
use App\Exceptions\ApplicationException;
use App\Exceptions\ServiceException;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\ValidationBuilder;
use Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class CartItemService
{

    const P2P = "Port to Port",
        P2D = "Port to Door",
        D2P = "Door to Port",
        D2D = "Door to Door";

    /**
     * @param $factory
     */
    public function setServiceFactory($factory)
    {
        $this->serviceFactory = $factory;
    }


    /**
     * @param $bo
     * @return $bo
     */
    public function extendObject(CartItemsBO $bo)
    {

        if ($bo->isConsignmentInsured) {
            $shpInsurance = new InsuranceService();
            $insuranceCharges = $shpInsurance->getMarinePremium(
                $bo->insuranceDetails->cargoType,
                $bo->insuranceDetails->sumAssured,
                $bo->insuranceDetails->bov
            );
            $bo->charges->insuranceCharges = $insuranceCharges->TotPrem;
        }
        return $bo;

    }

    public function checkOutCart($buyerId, $serviceId)
    {
        $model = new CartItem();
        $resultSet = $model->getCheckoutDetails($buyerId, $serviceId);
        return $resultSet;

    }

    /**
     * @param CartItemsBO $bo
     * @return $model
     */
    public function bo2model($bo)
    {

        LOG::info('bo2model save to cart items Start');
        dd($bo);
        if ($bo->cartId) {
            $model = CartItem::find($bo->cartId);
            if (!$model) {
                throw new ApplicationException([
                    "cartId" => $bo->cartId
                ], ["Invalid Data"]);
            }
        } else {
            $model = new CartItem();
        }

        $model->title = $bo->title;
        $model->buyer_id = $bo->buyerId;
        $model->seller_id = $bo->sellerId;
        $model->lkp_service_id = $bo->serviceId;
        $model->service_type = $bo->serviceType;
        $model->buyer_post_id = $bo->buyerPostId;
        $model->seller_quote_id = $bo->sellerQuoteId;
        $model->status = $bo->status;
        $model->post_type = $bo->postType;
        $model->buyer_name = $bo->buyerName;
        $model->seller_name = $bo->sellerName;
        $model->lead_type = $bo->leadType;
        $model->load_port = $bo->loadPort;
        $model->discharge_port = $bo->dischargePort;
        $model->cargo_ready_date = $bo->cargoReadyDate;
        $model->commodity_type = $bo->commodityType;
        $model->search_data = json_encode($bo->searchData);

        $model->consignor_name = $bo->consignorName;
        $model->consignor_email = $bo->consignorEmail;
        $model->consignor_mobile = $bo->consignorMobile;
        $model->consignor_address1 = $bo->consignorAddress1;
        $model->consignor_address2 = $bo->consignorAddress2;
        $model->consignor_address3 = $bo->consignorAddress3;
        $model->consignor_pincode = $bo->consignorPincode;
        $model->consignor_city = $bo->consignorCity;
        $model->consignor_state = $bo->consignorState;
        $model->consignor_country = $bo->consignorCountry;

        $model->consignee_name = $bo->consigneeName;
        $model->consignee_email = $bo->consigneeEmail;
        $model->consignee_mobile = $bo->consigneeMobile;
        $model->consignee_address1 = $bo->consigneeAddress1;
        $model->consignee_address2 = $bo->consigneeAddress2;
        $model->consignee_address3 = $bo->consigneeAddress3;
        $model->consignee_pincode = $bo->consigneePincode;
        $model->consignee_city = $bo->consigneeCity;
        $model->consignee_state = $bo->consigneeState;
        $model->consignee_country = $bo->consigneeCountry;

        $model->is_gsa_accepted = $bo->isGsaAccepted ? $bo->isGsaAccepted : 0;
        $model->is_consignment_insured = $bo->isConsignmentInsured ? $bo->isConsignmentInsured : 0;

        $model->additional_details = $bo->additionalDetails;
        $model->attributes = json_encode($bo->attributes);
        $model->insurance_details = json_encode($bo->insuranceDetails);

        $model->freight_charges = 0;
        $model->local_charges = 0;
        $model->insurance_charges = 0;
        $model->service_tax = 0;
        if (isset($bo->charges)) {
            $model->freight_charges = isset($bo->charges->freightCharges) ? $bo->charges->freightCharges : 0;
            $model->local_charges = isset($bo->charges->localCharges) ? $bo->charges->localCharges : 0;
            $model->insurance_charges = isset($bo->charges->insuranceCharges) ? $bo->charges->insuranceCharges : 0;
            $model->service_tax = isset($bo->charges->serviceTax) ? $bo->charges->serviceTax : 0;
        }

        if (isset($bo->validFrom))
            $model->valid_from = $bo->validFrom;
        if (isset($bo->validTo))
            $model->valid_to = $bo->validTo;

//        LOG::info('bo2model End ');
        return $model;
    }

    /**
     * @param $cartId
     * @return resp
     */
    public function deleteCartByIdWithServiceId($cartId, $buyerId, $serviceId)
    {
        Log::info($cartId);
        $model = new CartItem();
        if (!$model->deleteByCartId($cartId, $buyerId, $serviceId)) {
            throw new ApplicationException([
                "cartId" => $cartId,
                "buyerId" => $buyerId
            ], ["Invalid Data"]);
        }
        return True;
    }

    /**
     * @param $buyerId
     * @param $serviceId
     * @return resp
     */
    public function emptyCartByServiceId($buyerId, $serviceId)
    {

        $model = new CartItem();
        $status = $model->emptyCart($buyerId, $serviceId);
        return $status;

    }

    public function isJsonString($string)
    {
        try {
            json_decode($string);
            return True;
        } catch (\Exception $e) {
            return False;
        }
    }

    public function updateBoForInitData($bo)
    {

        $bo->buyerPostId = $bo->initialDetails->buyerPostId;
        $bo->sellerQuoteId = $bo->initialDetails->sellerQuoteId;

        isset($bo->initialDetails->searchData)
            ? $bo->searchData = $bo->initialDetails->searchData
            : true;
        isset($bo->initialDetails->indentData)
            ? $bo->indentData = $bo->initialDetails->indentData
            : true;

        $bo->postType = $bo->initialDetails->postType;
        $bo->buyerId = JWTAuth::parseToken()->getPayload()->get('id');

        if ($bo->postType == "SQ") {
            $bo = $this->updateBoWithSQ($bo);
        } else if ($bo->postType == 'SP') {
            $bo = $this->updateBoWithSP($bo);
        } else if ($bo->postType == 'TERM') {
            $bo = $this->updateBoWithTerm($bo);
        } else {
            ValidationBuilder::create()->error("cartitem", "postType specified is not valid")->raise();
        }

        $bo->buyerName = UserDetailsService::getUserDetails($bo->buyerId)->username;
        $bo->sellerName = UserDetailsService::getUserDetails($bo->sellerId)->username;

        return $bo;
    }

    private function updateBoWithSQ($bo)
    {

        $postDetails = BuyerPost::find($bo->buyerPostId);
        $quoteDetails = SellerQuotes::find($bo->sellerQuoteId);

        if (!count($postDetails) || !count($quoteDetails)) {
            throw new ServiceException("Either post or quote are not found");
        }

        $postDetails = $postDetails->toArray();
        $postDetails['attributes'] = json_decode($postDetails['attributes']);
        $bo->postDetails = $postDetails;
        $quoteDetails = $quoteDetails->toArray();
        $quoteDetails['attributes'] = json_decode($quoteDetails['attributes']);
        $bo->quoteDetails = $quoteDetails;

        $bo->leadType = $bo->postDetails['leadType'];

        if ($bo->buyerId != $bo->quoteDetails['buyerId']) {
            throw new UnauthorizedException("BuyerId does not match");
        }

        $bo->sellerId = $bo->quoteDetails['sellerId'];

        if ($bo->postDetails['serviceId'] != $bo->quoteDetails['serviceId']) {
            throw new UnauthorizedException("ServiceId does not match");
        }

        $bo->serviceId = $bo->postDetails['serviceId'];

        if ($bo->postDetails['attributes']->route->serviceSubType == self::P2P) {
            $bo->serviceType = "P2P";
        } else if ($bo->postDetails['attributes']->route->serviceSubType == self::P2D) {
            $bo->serviceType = "P2D";
        } else if ($bo->postDetails['attributes']->route->serviceSubType == self::D2P) {
            $bo->serviceType = "D2P";
        } else if ($bo->postDetails['attributes']->route->serviceSubType == self::D2D) {
            $bo->serviceType = "D2D";
        } else {
            throw new UnauthorizedException("Service Subtype does not match");
        }

        $timestamp = strtotime($bo->postDetails['created_at']);
        $bo->validFrom = $timestamp;
        $bo->validTo = $bo->quoteDetails['validTill'];
        return $bo;
    }

    private function updateBoWithSP($bo)
    {

        $rateCardDetails = SellerPost::find($bo->sellerQuoteId);

        if (!$rateCardDetails) {
            throw new UnauthorizedException("Seller Rate card not found or is not accessible");
        }

        $rateCardDetails = $rateCardDetails->toArray();
        $rateCardDetails['attributes'] = json_decode($rateCardDetails['attributes']);
        $bo->rateCardDetails = $rateCardDetails;

        $bo->leadType = 'spot';
        $bo->sellerId = $bo->rateCardDetails['seller_id'];
        $bo->serviceId = $bo->rateCardDetails['service_id'];
        if ($bo->rateCardDetails['service_subcategory'] == self::P2P) {
            $bo->serviceType = "P2P";
        } else if ($bo->rateCardDetails['service_subcategory'] == self::P2D) {
            $bo->serviceType = "P2D";
        } else if ($bo->rateCardDetails['service_subcategory'] == self::D2P) {
            $bo->serviceType = "D2P";
        } else if ($bo->rateCardDetails['service_subcategory'] == self::D2D) {
            $bo->serviceType = "D2D";
        } else {

            throw new ServiceException("Incorrect service type used");
        }

        $bo->validFrom = $bo->rateCardDetails['valid_from'];
        $bo->validTo = $bo->rateCardDetails['valid_to'];
        return $bo;

    }

    private function updateBoWithTerm($bo)
    {

        $postDetails = BuyerPost::find($bo->buyerPostId);
        $buyerContractDetails = BuyerContract::find($bo->sellerQuoteId);

        if (!count($postDetails) && !count($buyerContractDetails)) {
            throw new ServiceException("Contract details doesn't exists");
        }

        $buyerContractDetails = $buyerContractDetails->toArray();
        $buyerContractDetails['attributes'] = json_decode($buyerContractDetails['attributes']);
        $bo->buyerContractDetails = $buyerContractDetails;
        $postDetails = $postDetails->toArray();
        $postDetails['attributes'] = json_decode($postDetails['attributes']);

        $bo->postDetails = $postDetails;
        $bo->leadType = $bo->postDetails['leadType'];
        if ($bo->buyerId != $bo->postDetails['buyerId']) {

            throw new UnauthorizedException("buyer does not match");

        }
        if ($bo->postDetails['serviceId'] != $bo->buyerContractDetails['serviceId']) {

            throw new ServiceException("service does not match");

        }

        $bo->sellerId = $bo->buyerContractDetails['sellerId'];
        $bo->serviceId = $bo->buyerContractDetails['serviceId'];
        $bo->serviceType = "P2P";

        /*
         * TODO: Remove this and make service type as dynamic
         */
        /*
        if($bo->postDetails['attributes']->route->serviceSubType == self::P2P) {
            $bo->serviceType = "P2P";
        }
        else if($bo->postDetails['attributes']->route->serviceSubType == self::P2D) {
            $bo->serviceType = "P2D";
        }
        else if($bo->postDetails['attributes']->route->serviceSubType == self::D2P) {
            $bo->serviceType = "D2P";
        }
        else if($bo->postDetails['attributes']->route->serviceSubType == self::D2D) {
            $bo->serviceType = "D2D";
        }
        else {
            throw new ApplicationException([
                "buyerId" => $bo->buyerPostId,
                "sellerId" => $bo->buyerPostId
            ], ["Invalid Match of service type"]);
        }
        */
        $bo->validFrom = $bo->buyerContractDetails['validFrom'];
        $bo->validTo = $bo->buyerContractDetails['validTo'];
        return $bo;
    }

    /**
     * @param CartItemsBO $bo
     * @param $model
     * @return mixed
     */

    protected function model2bo($model, $bo)
    {

        $bo->cartId = $model->id;
        $bo->title = $model->title;
        $bo->buyerId = $model->buyer_id;
        $bo->sellerId = $model->seller_id;
        $bo->serviceId = $model->lkp_service_id;
        $bo->serviceType = $model->service_type;
        $bo->buyerPostId = $model->buyer_post_id;
        $bo->sellerQuoteId = $model->seller_quote_id;
        $bo->postType = $model->post_type;
        $bo->status = $model->status;
        $bo->buyerName = $model->buyer_name;
        $bo->sellerName = $model->seller_name;

        $bo->commodityType = $model->commodity_type;
        $bo->cargoReadyDate = $model->cargo_ready_date;
        $bo->loadPort = $model->load_port;
        $bo->dischargePort = $model->discharge_port;
        $bo->leadType = $model->lead_type;

        $bo->consignorName = $model->consignor_name;
        $bo->consignorEmail = $model->consignor_email;
        $bo->consignorMobile = $model->consignor_mobile;
        $bo->consignorAddress1 = $model->consignor_address1;
        $bo->consignorAddress2 = $model->consignor_address2;
        $bo->consignorAddress3 = $model->consignor_address3;
        $bo->consignorPincode = $model->consignor_pincode;
        $bo->consignorCity = $model->consignor_city;
        $bo->consignorState = $model->consignor_state;
        $bo->consignorCountry = $model->consignor_country;

        $bo->consigneeName = $model->consignee_name;
        $bo->consigneeEmail = $model->consignee_email;
        $bo->consigneeMobile = $model->consignee_mobile;
        $bo->consigneeAddress1 = $model->consignee_address1;
        $bo->consigneeAddress2 = $model->consignee_address2;
        $bo->consigneeAddress3 = $model->consignee_address3;
        $bo->consigneePincode = $model->consignee_pincode;
        $bo->consigneeCity = $model->consignee_city;
        $bo->consigneeState = $model->consignee_state;
        $bo->consigneeCountry = $model->consignee_country;

        $bo->attributes = json_decode($model->attributes);
        /*
        $bo->quoteDetails = $model->quoteDetails;
        $bo->postDetails = $model->postDetails;
        */
        $bo->isGsaAccepted = $model->is_gsa_accepted;
        $bo->isConsignmentInsured = $model->is_consignment_insured;
        $bo->insuranceDetails = json_decode($model->insurance_details);

        if (!isset($bo->charges)) {
            $bo->charges = new \stdClass();
        }
        $bo->charges->freightCharges = $model->freight_charges;
        $bo->charges->localCharges = $model->local_charges;
        $bo->charges->insuranceCharges = $model->insurance_charges;
        $bo->charges->serviceTax = $model->service_tax;
        $bo->charges->amountToPay =
            CurrencyConvertion::get('USD', 'INR', $bo->charges->freightCharges)
            + $bo->charges->localCharges
            + $bo->charges->insuranceCharges
            + $bo->charges->serviceTax;

        return $bo;
    }

}