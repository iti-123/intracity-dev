<?php
/**
 * Created by PhpStorm.
 * User: sainath
 * Date: 2/16/17
 * Time: 3:10 PM
 */

namespace ApiV2\Modules\FCL;

use ApiV2\BusinessObjects\Charges;
use ApiV2\Model\CartItem;
use ApiV2\Services\CartItemService;
use ApiV2\Services\CurrencyConvertion;
use ApiV2\Services\ServiceTaxCharges;
use App\Exceptions\ApplicationException;
use Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class FCLCartItemService extends CartItemService
{
    protected $serviceId = FCL;

    /**
     * @param CartItemsBO $bo
     * @return CartItemsBO
     * @throws ApplicationException
     */
    public function saveInit(FCLCartItemsBO $bo)
    {

        $model = $this->bo2model($bo);
        $model->save();
        $bo->cartId = $model->id;
        return $bo;

    }

    /**
     * @param $buyerId
     * @param $serviceId
     * @return resp
     */
    public function getCartdetailsById($cartId, $reqType)
    {

        $model = new CartItem();
        $bo = new FCLCartItemsBO();
        $resultSet = $model->getCartdetailsById($cartId, $reqType);
        if (!$resultSet) {
            throw new ApplicationException([
                "cartId" => $cartId
            ], ["Invalid Request"]);
        }
        return $this->model2bo($resultSet, $bo);

    }

    /**
     * @param $cartId
     * @param $buyerId
     * @return resp
     */
    public function deleteCartById($cartId, $buyerId)
    {
        $serviceId = $this->serviceId;
        parent::deleteCartByIdWithServiceId($cartId, $buyerId, $serviceId);
        return $this->getCartItems($buyerId);
    }

    /**
     * @param $buyerId
     * @param $serviceId
     * @return resp
     */
    public function getCartItems($buyerId)
    {
        $serviceId = $this->serviceId;
        $model = new CartItem();
        $resultSet = $model->getByBuyerIdAndServiceId($buyerId, $serviceId);

        $totalCharges = new \stdClass();
        $totalCharges->freightCharges = 0;
        $totalCharges->localCharges = 0;
        $totalCharges->insuranceCharges = 0;
        $totalCharges->serviceTax = 0;
        $totalCharges->amountToPay = 0;
        $finalResultSet = [];
        $finalResultSet['cartItems'] = [];
        foreach ($resultSet as $cartItem) {
            $bo = new FCLCartItemsBO();
            $cartItemOfeachBo = $this->model2bo($cartItem, $bo);
            $totalCharges->freightCharges += $cartItemOfeachBo->charges->freightCharges;
            $totalCharges->localCharges += $cartItemOfeachBo->charges->localCharges;
            if (isset($cartItemOfeachBo->charges->insuranceCharges->TotPrem)) {
                $totalCharges->insuranceCharges += $cartItemOfeachBo->charges->insuranceCharges->TotPrem;
            } else {
                $totalCharges->insuranceCharges += $cartItemOfeachBo->charges->insuranceCharges;
            }
            $totalCharges->serviceTax += $cartItemOfeachBo->charges->serviceTax;

            $finalResultSet['cartItems'][] = $cartItemOfeachBo;

        }

        $totalCharges->amountToPay = CurrencyConvertion::get('USD', 'INR', $totalCharges->freightCharges)
            + $totalCharges->localCharges
            + $totalCharges->insuranceCharges
            + $totalCharges->serviceTax;

        $finalResultSet['totalCharges'] = $totalCharges;
        return $finalResultSet;

    }

    public function emptyCart($buyerId)
    {
        $serviceId = $this->serviceId;
        $status = $this->emptyCartByServiceId($buyerId, $serviceId);
        return $status;
    }

    public function updateBoForInitData($bo)
    {

        $bo = parent::updateBoForInitData($bo);
        $bo->status = "DRAFT";
        if ($bo->postType == "SQ") {

            $bo->title = $bo->postDetails['title'];
            $bo->commodityType = $bo->postDetails['attributes']->route->commodity;
            $bo->cargoReadyDate = $bo->postDetails['attributes']->route->cargoReadyDate;
            $bo->loadPort = $bo->postDetails['attributes']->route->loadPort;
            $bo->dischargePort = $bo->postDetails['attributes']->route->dischargePort;
            $bo->searchData = $bo->initialDetails;

            $sellerCarrierDetails = $bo->quoteDetails['attributes']->carriers[$bo->initialDetails->carrierIndex];
            if (!$sellerCarrierDetails) {
                throw new ApplicationException([
                    "buyerId" => $bo->buyerPostId,
                    "sellerId" => $bo->buyerPostId
                ], ["Invalid Data"]);
            }

            $carrierDetails = new \stdClass();
            $carrierDetails->carrierName = $sellerCarrierDetails->carrierName;
            $carrierDetails->etd = $sellerCarrierDetails->etd;
            $carrierDetails->cyCutOffDate = $sellerCarrierDetails->cyCutOffDate;
            $carrierDetails->transitDays = $sellerCarrierDetails->transitDays;
            $carrierDetails->validTill = $sellerCarrierDetails->validTill;
            $carrierDetails->tracking = $sellerCarrierDetails->tracking;
            $carrierDetails->routingType = $sellerCarrierDetails->routingType;
            $carrierDetails->routingVia = $sellerCarrierDetails->routingVia;

            $containers = [];
            foreach ($bo->postDetails['attributes']->route->containers as $eachContainer) {
                $container = $eachContainer;
                foreach ($sellerCarrierDetails->containers as $eachQuoteContainer) {
                    if ($eachContainer->containerType == $eachQuoteContainer->containerType) {
                        $container->offer = $eachQuoteContainer->finalOffer;
                        $containers[] = $container;
                        continue;
                    }
                }
            }
            if (!count($containers)) {
                throw new ApplicationException([
                    "sellerQuoteId" => $bo->sellerQuoteId
                ], ["Invalid Containers"]);
            }
            $attributes = new \stdClass();
            $attributes->containers = $containers;
            $attributes->carrierDetails = $carrierDetails;
            $attributes->carrierIndex = $bo->initialDetails->carrierIndex;
            $bo->attributes = $attributes;

        } else if ($bo->postType == 'SP') {

            $bo->buyerPostId = 0;
            $bo->title = $bo->rateCardDetails['title'];
            $bo->commodityType = $bo->searchData->commodityType;
            $bo->cargoReadyDate = $bo->searchData->cargoReadyDate;
            $bo->loadPort = $bo->searchData->loadPort;
            $bo->dischargePort = $bo->searchData->dischargePort;
            $portPair = [];
            foreach ($bo->rateCardDetails['attributes']->portPair as $eachPortPair) {
                if ($eachPortPair->loadPort == $bo->loadPort &&
                    $eachPortPair->dischargePort == $bo->dischargePort) {
                    $portPair = $eachPortPair;
                }
            }
            if (!count($portPair)) {
                throw new ApplicationException([], ["Invalid Port Pair"]);
            }

            $containers = [];
            if (!isset($portPair->carriers[$bo->initialDetails->carrierIndex])) {
                throw new ApplicationException([], ["Invalid Carrier Index"]);
            }
            $sellerCarrierDetails = $portPair->carriers[$bo->initialDetails->carrierIndex];
            if (!$sellerCarrierDetails) {
                throw new ApplicationException([], ["Invalid Data"]);
            }

            $carrierDetails = new \stdClass();
            $carrierDetails->carrierName = $sellerCarrierDetails->carrierName;
            $carrierDetails->etd = $sellerCarrierDetails->etd;
            $carrierDetails->cyCutOffDate = $sellerCarrierDetails->cyCutOffDate;
            $carrierDetails->transitDays = $sellerCarrierDetails->transitDays;
            $carrierDetails->validTill = $sellerCarrierDetails->validTill;
            $carrierDetails->tracking = $sellerCarrierDetails->tracking;
            $carrierDetails->routingType = $sellerCarrierDetails->routingType;
            $carrierDetails->routingVia = $sellerCarrierDetails->routingVia;

            foreach ($bo->searchData->containers as $eachContainer) {
                $container = $eachContainer;
                foreach ($sellerCarrierDetails->containers as $eachQuoteContainer) {
                    if ($eachContainer->containerType == $eachQuoteContainer->containerType) {
                        $totalFC = 0;
                        foreach ($eachQuoteContainer->freightCharges as $eachFreightCharges) {
                            $totalFC += $eachFreightCharges->amount;
                        }
                        $totalLC = 0;
                        foreach ($eachQuoteContainer->localCharges as $eachLocalCharges) {
                            $totalLC += $eachLocalCharges->amount;
                        }

                        $offer = new \stdClass();
                        $offer->freightCharges = new \stdClass();
                        $offer->freightCharges->chargeType = "Total Freight Charges";
                        $offer->freightCharges->currency = "USD";
                        $offer->freightCharges->amount = $totalFC;
                        $offer->localCharges = new \stdClass();
                        $offer->localCharges->chargeType = "Total Local Charges";
                        $offer->localCharges->currency = "INR";
                        $offer->localCharges->amount = $totalLC;

                        $container->offer = $offer;
                        $containers[] = $container;
                        continue;
                    }
                }

                if (!count($containers)) {
                    throw new ApplicationException([
                        "sellerQuoteId" => $bo->sellerQuoteId
                    ], ["Invalid Containers"]);
                }

            }
            $attributes = new \stdClass();
            $attributes->containers = $containers;
            $attributes->carrierDetails = $carrierDetails;
            $attributes->carrierIndex = $bo->initialDetails->carrierIndex;
            $bo->attributes = $attributes;

            $bo->searchData = $bo->initialDetails;

        } else if ($bo->postType == 'TERM') {

            $bo->title = $bo->buyerContractDetails['title'];
            $bo->commodityType = $bo->buyerContractDetails['attributes']->commodity;
            $bo->cargoReadyDate = "";
            $bo->loadPort = $bo->indentData->loadPort;
            $bo->dischargePort = $bo->indentData->dischargePort;

            $containers = [];
            foreach ($bo->indentData->containers as $eachContainer) {
                $container = $eachContainer;
                foreach ($bo->buyerContractDetails['attributes']->portPairs as $eachPortPair) {
                    if ($eachPortPair->loadPort == $bo->loadPort
                        && $eachPortPair->dischargePort == $bo->dischargePort
                        && $eachPortPair->containerType == $eachContainer->containerType
                    ) {
                        $offer = new \stdClass();
                        $offer->freightCharges = new \stdClass();
                        $offer->freightCharges->chargeType = "Total Freight Charges";
                        $offer->freightCharges->currency = "USD";
                        $offer->freightCharges->amount = $eachPortPair->freightCharges->amount;
                        $offer->localCharges = new \stdClass();
                        $offer->localCharges->chargeType = "Total Local Charges";
                        $offer->localCharges->currency = "INR";
                        $offer->localCharges->amount = $eachPortPair->localCharges->amount;
                        $container->offer = $offer;
                        $containers[] = $container;
                        continue;
                    }
                }
            }
            if (!count($containers)) {
                throw new ApplicationException([
                    "sellerQuoteId" => $bo->sellerQuoteId
                ], ["Invalid Containers"]);
            }
            $attributes = new \stdClass();
            $attributes->containers = $containers;
            $bo->attributes = $attributes;

        }

        $localCharges = 0;
        $freightCharges = 0;
        $insuranceCharges = 0;

        foreach ($bo->attributes->containers as $eachContainer) {
            $freightCharges += $eachContainer->quantity * $eachContainer->offer->freightCharges->amount;
            $localCharges += $eachContainer->quantity * $eachContainer->offer->localCharges->amount;
        }

        $charge = new Charges();
        $charge->freightCharges = $freightCharges;
        $charge->localCharges = $localCharges;
        $charge->insuranceCharges = $insuranceCharges;
        $charge->serviceTax = ServiceTaxCharges::getServiceTaxAmount($charge->localCharges);
        $bo->charges = $charge;

        unset($bo->postDetails);
        unset($bo->quoteDetails);
        unset($bo->rateCardDetails);
        unset($bo->buyerContractDetails);
        return $bo;
    }

    /*
        public function model2bo($model, $bo) {

            $bo = parent::model2bo($model, $bo);

            $seviceTax = 0;
            $localCharges = 0;
            $freightCharges = 0;
            $insuranceCharges = 0;
            foreach ($bo->attributes->containers as $eachContainer) {
                $freightCharges +=  $eachContainer->quantity * $eachContainer->offer->freightCharges->amount;
                $localCharges +=  $eachContainer->quantity * $eachContainer->offer->localCharges->amount;
            }
            if($bo->isConsignmentInsured == 1) {
                $shpInsurance = new InsuranceService();
                $insuranceCharges = $shpInsurance->getMarinePremium(
                    $bo->insuranceDetails->cargoType, $bo->insuranceDetails->sumAssured, $bo->insuranceDetails->bov
                );
            }

            $charge = new Charges();
            $charge->freightCharges = $freightCharges;
            $charge->localCharges = $localCharges;
            $charge->insuranceCharges = $insuranceCharges;
            $charge->serviceTax = $seviceTax;
            $bo->charges = $charge;
            return $bo;

        }
    */

    /**
     * @param CartItemsBO $bo
     * @param $model
     * @return mixed
     */
    public function updateBoWithUiFileds($oldBo, $newBo)
    {

        $oldBo->additionalDetails = $newBo->additionalDetails;
        $oldBo->isConsignmentInsured = $newBo->isConsignmentInsured;
        $oldBo->insuranceDetails = $newBo->insuranceDetails;
        $oldBo->isGsaAccepted = $newBo->isGsaAccepted;

        $oldBo->attributes->fclTypeOfBol = $newBo->attributes->fclTypeOfBol;
        $oldBo->attributes->consignmentType = $newBo->attributes->consignmentType;
        $oldBo->attributes->consignmentValue = $newBo->attributes->consignmentValue;
        $oldBo->attributes->blrequirement = $newBo->attributes->blrequirement;

        $oldBo->consignorName = $newBo->consignorName ? $newBo->consignorName : "";
        $oldBo->consignorEmail = $newBo->consignorEmail ? $newBo->consignorEmail : "";
        $oldBo->consignorMobile = $newBo->consignorMobile ? $newBo->consignorMobile : "";
        $oldBo->consignorAddress1 = $newBo->consignorAddress1 ? $newBo->consignorAddress1 : "";
        $oldBo->consignorAddress2 = $newBo->consignorAddress2 ? $newBo->consignorAddress2 : "";
        $oldBo->consignorAddress3 = $newBo->consignorAddress3 ? $newBo->consignorAddress3 : "";
        $oldBo->consignorPincode = $newBo->consignorPincode ? $newBo->consignorPincode : "";
        $oldBo->consignorCity = $newBo->consignorCity ? $newBo->consignorCity : "";
        $oldBo->consignorState = $newBo->consignorState ? $newBo->consignorState : "";
        $oldBo->consignorCountry = $newBo->consignorCountry ? $newBo->consignorCountry : "";

        $oldBo->consigneeName = $newBo->consigneeName ? $newBo->consigneeName : "";
        $oldBo->consigneeEmail = $newBo->consigneeEmail ? $newBo->consigneeEmail : "";
        $oldBo->consigneeMobile = $newBo->consigneeMobile ? $newBo->consigneeMobile : "";
        $oldBo->consigneeAddress1 = $newBo->consigneeAddress1 ? $newBo->consigneeAddress1 : "";
        $oldBo->consigneeAddress2 = $newBo->consigneeAddress2 ? $newBo->consigneeAddress2 : "";
        $oldBo->consigneeAddress3 = $newBo->consigneeAddress3 ? $newBo->consigneeAddress3 : "";
        $oldBo->consigneePincode = $newBo->consigneePincode ? $newBo->consigneePincode : "";
        $oldBo->consigneeCity = $newBo->consigneeCity ? $newBo->consigneeCity : "";
        $oldBo->consigneeState = $newBo->consigneeState ? $newBo->consigneeState : "";
        $oldBo->consigneeCountry = $newBo->consigneeCountry ? $newBo->consigneeCountry : "";

        return $oldBo;
    }

    /**
     * @param array $bos
     */
    public function saveMultipleCartItems(array $bos = [])
    {

        LOG::info('saveMultipleCartItems');
        //empty the current cart and add these items directlt.
        $buyerId = JWTAuth::parseToken()->getPayload()->get('id');
        $deletedRows = CartItem::where('buyer_id', $buyerId)->delete();

        foreach ($bos as $bo) {
            LOG::info('Saving BO  %%%%%%%%%%%%%%%%%%%%%%%%%');
            //  LOG::info($bo);
            $this->save($bo);

        }

    }

    /**
     * @param CartItemsBO $bo
     * @return CartItemsBO|null
     * @throws ApplicationException
     */
    public function save(FCLCartItemsBO $bo)
    {

        /*
         * Validations to be done....
         */
        $bo = $this->extendObject($bo);
        $bo->status = 'UPDATED';
        $model = $this->bo2model($bo);
        if (!$model->save()) {
            throw new ApplicationException([], ["Failed to save"]);
        }
        return $bo;

    }

}