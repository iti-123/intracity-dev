<?php

namespace ApiV2\Controllers;

use ApiV2\Services\InsuranceService;
use ApiV2\Services\PaymentService;
use ApiV2\Services\ServiceTaxCharges;
use Illuminate\Http\Request;
use Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class ShpInsuranceController extends BaseController
{

    public function getInsurance(Request $request)
    {
        $values = json_decode($request->getContent());
        $shpInsurance = new InsuranceService();
        $response = $shpInsurance->getMarinePremium($values->cargoType, $values->sumAssured, $values->bov);
        return Response::json($response);
    }


    public function test()
    {
        $userID = JWTAuth::parseToken()->getPayload()->get('id');
        return "success " . $userID;


    }


    public function getServiceTaxCharges()
    {

        $serviceTaxCharges = new ServiceTaxCharges();
        $userID = JWTAuth::parseToken()->getPayload()->get('id');

        $orderTotal = 10000;
        $lkpServiceId = 1;
        $serviceTaxParams = array(
            'buyer_id' => $userID,
            'seller_id' => 1053,
//          'stateServiceTax' => CheckoutComponent::checkStateServiceTax($getOrderDetails->from_location,$getOrderDetails->to_location),
            'stateServiceTax' => $serviceTaxCharges->checkStateServiceTax('Hyderabad', 'Chennai'),

//          'trasport_export_goods' => CommonComponent::getLoadTypeTax($order->seller_post_item_id,$order->lkp_service_id,$buyerPostItemID,$order->is_contract),
            'trasport_export_goods' => $serviceTaxCharges->getLoadTypeTax(38, 1, 21, 0),
        );
        if (SHOW_SERVICE_TAX) {
            return $response = $serviceTaxCharges->getServiceTaxCharges($orderTotal, $lkpServiceId, $serviceTaxParams);
        }
    }

    public function hdfcpayment()
    {
        $paymentService = new PaymentService();
        $params = array(
            'amount' => 10000,
            'refference_id' => 123456,
            'payment_mode' => 'CC'
        );
        return $PaymentFields = $paymentService->hdfcFields($params);
    }

    public function response()
    {
        $paymentService = new PaymentService();
        return $PaymentFields = $paymentService->hdfcresponse();
    }
}