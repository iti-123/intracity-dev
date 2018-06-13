<?php
/**
 * Created by PhpStorm.
 * User: chetan
 * Date: 16/2/17
 * Time: 5:08 PM
 */

namespace ApiV2\Services;

use App\LogCholaQuote;
use Auth;
use DB;
use SoapClient;
use Tymon\JWTAuth\Facades\JWTAuth;

class InsuranceService
{

    public static $webServiceUrl = "http://115.111.181.94/PortalService/MarineSpecificVoyage.asmx";

    public static function getMarinePremium($cargo, $sumAssured, $bov)
    {
        $userID = JWTAuth::parseToken()->getPayload()->get('id');

        $request_url = self::$webServiceUrl;
        $param = array('objMarineInServiceResult' => array(
            'CargoDesc' => $cargo,
            'SumInsured' => $sumAssured,
            'BOV' => $bov,
        ));

        $logPremium = new LogCholaQuote();
        $logPremium->cargo_desc = $cargo;
        $logPremium->sum_insured = $sumAssured;
        $logPremium->bov = $bov;
        $created_at = date('Y-m-d H:i:s');
        $createdIp = $_SERVER ['REMOTE_ADDR'];
        $logPremium->created_at = $created_at;
        $logPremium->updated_at = $created_at;
        $logPremium->created_by = (isset($userID)) ? $userID : 0;
        $logPremium->updated_by = (isset($userID)) ? $userID : 0;
        $logPremium->created_ip = $createdIp;
        $logPremium->updated_ip = $createdIp;
        $logPremium->save();
        $logpremiumId = $logPremium->id;

        $soapClient = new SoapClient($request_url . '?wsdl');

        try {
            $soapClientResult = $soapClient->PremiumCalculation($param);
            $result = (array)$soapClientResult->PremiumCalculationResult;
            $logPremium::where("id", $logpremiumId)
                ->update(array(
                    'net_prem' => $result['NetPrem'],
                    'st' => $result['ST'],
                    'tot_prem' => $result['TotPrem'],
                ));
            return $soapClientResult->PremiumCalculationResult;
            // dd($soapClientResult->PremiumCalculationResult);
        } catch (SoapFault $fault) {
            echo "Fault code: {$fault->faultcode} \n ";
            echo "Fault string: {$fault->faultstring} \n ";
            if ($soapClient != null) {
                $soapClient = null;
            }
            exit();
        }
    }

    public static function getOrderInsuranceTotal($buyerId = 0)
    {
        if ($buyerId == 0) {
            $buyerId = Auth::id();
        }

        $total = DB::table('view_cart_items')
            ->select(DB::raw('sum(buyer_consignment_insurance_amount) as insurancetotal'))
            ->where('view_cart_items.buyer_id', '=', $buyerId)
            ->first();
        return $total->insurancetotal;
    }
}