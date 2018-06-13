<?php
/**
 * Created by PhpStorm.
 * User: chetan
 * Date: 16/2/17
 * Time: 5:08 PM
 */

namespace ApiV2\Services;

use ApiV2\Model\ServiceTax;
use DB;

class ServiceTaxCharges1
{
    public static function getServiceTaxCharges($orderTotal, $lkpServiceId, $serviceTaxParams)
    {
        try {
            $serviceCharges = ServiceTax::where(array(
                'lkp_service_id' => $lkpServiceId,
                'is_active' => '1'
            ))->first();
            $serviceCharges->order_service_tax_amount = 0.00;
            $seller_liable_to_pay = false;
            $serviceTax = false;
            $notaxStates = unserialize(SERVICE_TAX_NOT_APPLICABLE_STATES);
            $serviceCharges->service_tax = 0;
            $serviceCharges->swachh_bharat_cess = 0;
            $serviceCharges->krishi_kalyan_cess = 0;

            if (SHOW_SERVICE_TAX && $orderTotal > SERVICE_TAX_FRIEGHT_MIN) { // Check Service Tax enable and Min Frieght true
                $trasport_export_goods = true;
                if (isset($serviceTaxParams['trasport_export_goods'])) {
                    $trasport_export_goods = $serviceTaxParams['trasport_export_goods'];
                } // Defualt Value
                $seller_abatment = false; // Default Value
                $seller_liable_to_pay = false; // Default Value
                $buyer_id = $serviceTaxParams['buyer_id'];
                $seller_id = $serviceTaxParams['seller_id'];
                $stateServiceTax = $serviceTaxParams['stateServiceTax'];
                if ($trasport_export_goods == true) { //Start: Check trasport export goods is No
                    if ($stateServiceTax) {    //Start :  Check state wise service tax avilable or not
                        if ($buyer_id && $seller_id) { // Check Buyer and Seller Id's available or not
                            $BuyerDetails = UserDetailsService::getUserDetails($buyer_id); // Buyer Details
                            $SellerDetails = UserDetailsService::getUserDetails($seller_id); // Selelr Details
                            $buyer_business_type = false; // Default Business Type

                            if (isset($SellerDetails->service_tax_number) && $SellerDetails->service_tax_number != '') { // Checking Seller have service tax number
                                if ($SellerDetails->gta) {       // Checking Seller GTA Yes and notaxStates or outside of india
                                    $seller_abatment = true; // seller abatment true
                                } else {
                                    $seller_abatment = false; // Selelr abatment false
                                }
                                if (self::getServiceGroupID($lkpServiceId) == TRANSPORT) {
                                    //$service_tax_formula =  ((PERCENT40*($orderTotal))/10000);
                                    $service_tax_formula = ($orderTotal / 100);
                                } elseif (self::getServiceGroupID($lkpServiceId) == OTHERS) {
                                    $service_tax_formula = ($orderTotal / 100);
                                }
                                if ($seller_abatment == true) {
                                    //$service_tax = PERCENT14; // OLD Constant
                                    $service_tax = ABATEMENT_SERVICE_TAX * $service_tax_formula;
                                    $swachh_bharat_cess = ABATEMENT_SWACHH_BHARAT_CESS * $service_tax_formula;
                                    $krishi_kalyan_cess = ABATEMENT_KRISHI_KALYAN_CESS * $service_tax_formula;
                                } else if ($seller_abatment == false) {
                                    //$service_tax = PERCENT14; // OLD Constant
                                    $service_tax = SERVICE_TAX * $service_tax_formula;
                                    $swachh_bharat_cess = SWACHH_BHARAT_CESS * $service_tax_formula;
                                    $krishi_kalyan_cess = KRISHI_KALYAN_CESS * $service_tax_formula;
                                }
                                //if($seller_abatment!=''){ // checking seller_abatment is true or false only allow tax calculation
                                $serviceCharges->service_tax = $service_tax;
                                $serviceCharges->swachh_bharat_cess = $swachh_bharat_cess;
                                $serviceCharges->krishi_kalyan_cess = $krishi_kalyan_cess;
                                $serviceCharges->order_service_tax_amount = $service_tax + $swachh_bharat_cess + $krishi_kalyan_cess;
                                //}
                            } // Service tax applicable for sellet tax number exit only changedone on @19102016
                        } // End : Check Buyer and Seller Id false
                    }   //End :  Check state wise service tax avilable or not
                } // End : Check trasport export goods is Yes
            } // End : Check Service Tax enable and Min Frieght false
            $insurance = InsuranceService::getOrderInsuranceTotal();
            $serviceCharges->order_total_amount = $orderTotal + $serviceCharges->order_service_tax_amount + $insurance;

            return $serviceCharges;
        } catch (Exception $ex) {

        }
    }

    public static function getServiceGroupID($service_id)
    {
        try {
            $services = DB::table('lkp_services')->where('id', $service_id)
                ->select('lkp_services.lkp_invoice_service_group_id')->get();
            return $services[0]->lkp_invoice_service_group_id;
        } catch (\Exception $e) {
            //return $e->message;
        }
    }

    public static function checkStateServiceTax($from_location, $to_location)
    {
        $notaxStates = unserialize(SERVICE_TAX_NOT_APPLICABLE_STATES);

        $from_state = DB::table('lkp_states');
        $from_state->leftjoin('lkp_cities', 'lkp_cities.lkp_state_id', '=', 'lkp_states.id');
        $from_state->where('lkp_cities.city_name', $from_location);
        $from_state->select('lkp_states.id');
        $from_state_data = $from_state->first();

        $to_state = DB::table('lkp_states');
        $to_state->leftjoin('lkp_cities', 'lkp_cities.lkp_state_id', '=', 'lkp_states.id');
        $to_state->where('lkp_cities.city_name', $from_location);
        $to_state->select('lkp_states.id');
        $to_state_data = $to_state->first();

        if (!$from_state_data && !$to_state_data) {
            $from_state = DB::table('lkp_states');
            $from_state->leftjoin('lkp_ptl_pincodes', 'lkp_ptl_pincodes.state_id', '=', 'lkp_states.id');
            $from_state->whereRaw('CONCAT_WS("-", pincode, postoffice_name)="' . $from_location . '"');
            $from_state->select('lkp_states.id');
            $from_state_data = $from_state->first();

            $to_state = DB::table('lkp_states');
            $to_state->leftjoin('lkp_ptl_pincodes', 'lkp_ptl_pincodes.state_id', '=', 'lkp_states.id');
            $to_state->whereRaw('CONCAT_WS("-", pincode, postoffice_name)="' . $from_location . '"');
            $to_state->select('lkp_states.id');
            $to_state_data = $to_state->first();

        }

        if (!in_array($from_state_data->id, $notaxStates) && !in_array($to_state_data->id, $notaxStates)) {
            return true;
        } else {
            return false;
        }

    }

    public static function getLoadTypeTax($id, $serviceId, $buyer_post_id, $is_contract)
    {
        try {
            $serviceId = $serviceId;
            $load_type_tax = true;
            switch ($serviceId) {
                case ROAD_FTL :
                    if ($is_contract) {
                        $loadtype = DB::table('term_buyer_quote_items')
                            ->where('term_buyer_quote_items.id', $buyer_post_id)
                            ->leftjoin('lkp_load_types as lt', 'term_buyer_quote_items.lkp_load_type_id', '=', 'lt.id')
                            ->get();
                    } else {
                        $loadtype = DB::table('seller_post_items')
                            ->where('seller_post_items.id', '=', $id)
                            ->leftjoin('lkp_load_types as lt', 'seller_post_items.lkp_load_type_id', '=', 'lt.id')
                            ->get();

                        $buyerLoadType = DB::table('buyer_quote_items')
                            ->where('buyer_quote_items.id', $buyer_post_id)
                            ->leftjoin('lkp_load_types as lt', 'buyer_quote_items.lkp_load_type_id', '=', 'lt.id')
                            ->get();
                    }
                    if (($loadtype && $loadtype[0]->is_servicetax == 1)) {
                        if (isset($buyerLoadType)) {
                            if (($buyerLoadType && $buyerLoadType[0]->is_servicetax == 1))
                                $load_type_tax = true;
                            else
                                $load_type_tax = false;
                        } else {
                            $load_type_tax = true;
                        }
                    } else {
                        $load_type_tax = false;
                    }
                    break;

                case ROAD_TRUCK_HAUL :

                    $loadtype = DB::table('truckhaul_seller_post_items')
                        ->where('truckhaul_seller_post_items.id', '=', $id)
                        ->leftjoin('lkp_load_types as lt', 'truckhaul_seller_post_items.lkp_load_type_id', '=', 'lt.id')
                        ->get();

                    $buyerLoadType = DB::table('truckhaul_buyer_quote_items')
                        ->where('truckhaul_buyer_quote_items.id', $buyer_post_id)
                        ->leftjoin('lkp_load_types as lt', 'truckhaul_buyer_quote_items.lkp_load_type_id', '=', 'lt.id')
                        ->get();

                    if (($loadtype && $loadtype[0]->is_servicetax == 1)) {
                        if (isset($buyerLoadType)) {
                            if (($buyerLoadType && $buyerLoadType[0]->is_servicetax == 1))
                                $load_type_tax = true;
                            else
                                $load_type_tax = false;
                        } else {
                            $load_type_tax = true;
                        }
                    } else {
                        $load_type_tax = false;
                    }
                    break;

                default :
                    $load_type_tax = true;
                    break;


            }
            return $load_type_tax;


        } catch (Exception $ex) {

        }

    }

}