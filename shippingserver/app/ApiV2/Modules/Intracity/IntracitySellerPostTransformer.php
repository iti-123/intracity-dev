<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 2/18/2017
 * Time: 10:47 AM
 */

namespace ApiV2\Modules\Intracity;

use ApiV2\Model\BuyerPost;
use ApiV2\Model\SellerQuotes;
use ApiV2\Services\UserDetailsService;
use Tymon\JWTAuth\Facades\JWTAuth;
use Log;
use App\Exceptions\ApplicationException;
use ApiV2\Modules\Intracity\IntracitySellerPostBO;
use ApiV2\Services\LogistiksCommonServices\NumberGeneratorServices;
use ApiV2\Model\IntraHyperSellerPost;
use ApiV2\Modules\Intracity\IntracityCommanService;

class IntracitySellerPostTransformer extends IntracityCommanService
{

    public $errors = array();

    public function xsl2boSave(array $master = [], array $details = [], array $discounts = []) {
        $bo = new IntracitySellerPostBO();
        // return $details;
        ///----- Process Master Sheet --------
        // return $discounts;
        $bo->sellerId = JWTAuth::parseToken()->getPayload()->get('id');
        $bo->serviceId = FCL;
        $bo->serviceSubType = "P2P";

        $bo->isPublic = $master[5] == 'Yes' ? true : false;
        $bo->title = $master[1];
        $bo->validFrom = $master[2];
        $bo->validTo = $master[3];
        $bo->status = "draft";
        $bo->viewCount = 0;

        $bo->termsConditions = $master[4];
        $bo->isTermAccepted = "true";
        
        $bo->visibleToBuyers = null; 
        $bo->attributes = new IntracitySellerPostAttributes();
        
        // Process Route Detail Sheet
        
        $isRoute = count($details);
        
        $route = [];
        
        
        if($isRoute > 0) {
            
            foreach ($details as $key => $value) {    
                $timeFrom = $value[5].":00:00";
                $timeTo = $value[6].":00:00";
                
                if(!empty($value[5])):
                    $formatedtimeFrom = date_format(date_create($timeFrom), "h:i:s");
                else:
                    $formatedtimeFrom = '';
                endif;

                if(!empty($value[6])):
                    $formatedtimeTo = date_format(date_create($timeTo), "h:i:s");
                else:
                    $formatedtimeTo = '';
                endif;

                $vehicle_type =  $this->getVehicleIdByName($value[10]);;         
                $route[$key]['is_seller_buyer']=SELLER;
                $route[$key]['lkp_service_id']=_INTRACITY_;
                $route[$key]['type_basis']= 1;
                $route[$key]['city_id'] = $this->getCityIdByName($value[0]);
                $route[$key]['valid_from']=$value[1];
                $route[$key]['valid_to']=$value[2];
                $route[$key]['transit_hour']=$value[3];
                $route[$key]['tracking']= $this->getMileStone($value[4]);

                $route[$key]['time_from'] = $formatedtimeFrom; 
                $route[$key]['time_to'] = $formatedtimeTo;
                $route[$key]['base_distance']=$value[7];
                $route[$key]['rate_base_distance']=$value[8];
                $route[$key]['cost_per_extra_hour']=$value[9];
                $route[$key]['vehicle_type_id']= $vehicle_type;
                $route[$key]['additional_hour_charge']=$value[11];
                $route[$key]['odc_base_volume']=$value[12];
                // $route[$key]['over_dimension_charge'] = $value[13];
                $route[$key]['lc_no_helpers']=$value[14];
                $route[$key]['lc_base_charge_per_labour']=$value[15];

                $route[$key]['additional_km_charge']=$value[16];
                $route[$key]['lc_addtnl_chrg']=$value[16];
                $route[$key]['lc_toll_charge']=$value[17];
                $route[$key]['lc_others']=$value[18];
                $route[$key]['base_time']= $this->getBaseTime($value[19]);
                $route[$key]['is_active']=1;

                $route[$key]['cost_base_time']=$value[20] != ''?$value[20]:0;
                $route[$key]['discount']=[];
                $route[$key]['wc_vehicle_type']=$vehicle_type;
                // $route[$key]['wc_cost_per_extra_hr']=self::has($value->route,'extracost_wait');
                $route[$key]['odc_vehicle_type']=$vehicle_type;
                // $route[$key]['odc_cost_extra_volume']=self::has($value->route,'volume_unit_extra');
                
                // $route[$key]['multiple_rate']= json_encode(self::has($value,'multipleRate'));
                
                array_push($bo->attributes->routes, $route[$key]);
                
            }
        }
        
        
        
        
        ///---- Process Discounts Sheet ---------
        
        $size = count($discounts);
        $counter = 0;
        
        LOG::debug("Discounts setup => [" . $size . "]");
        
        
        
        if ($size > 0) {
            
            foreach ($discounts as $row) {
                
                LOG::debug($row);
                
                LOG::debug("Processing Discounts for Buyer [" . $row[0] . "]");
                
                $discount = new Discount();
                $buyerId = UserDetailsService::getUserByEmail($row[0]);
                if (!isset($buyerId)) {
                    // throw new ApplicationException(["sheet" => "Discounts", "row" => $counter], "No buyer found with email " . $row[0]);
                }
                
                $discount->buyerId = $buyerId;
                $discount->discountType = $row[2];
                $discount->discount = $row[3];
                $discount->creditDays = $row[4];
                
                
                if (!isset($row[1])) {
                    //This is a global discount. Add it.
                    array_push($bo->attributes->discount, $discount);                    
                } else {
                    // Discount for particular block  
                           
                    $routeLevel = ((int) explode("-",$row[1])[1]) -1;
                    
                    array_push($bo->attributes->routes[$routeLevel]['discount'],$discount);
                    
                }
            }           
            
        }        
        
        return  $bo;
        
    }


    public function xsl2MasterModel($bo)
    {
        $model = new IntraHyperSellerPost();
        $userID = JWTAuth::parseToken()->getPayload()->get('id');
        $txnId = NumberGeneratorServices::generateTranscationId(new IntraHyperSellerPost,_INTRACITY_);
        $model->post_transaction_id = $txnId;
        $model->rate_cart_type =2;//2= distance and 1 hours
        $model->notes = $bo->termsConditions;
        $model->is_private_public =$bo->isPublic;
        $model->terms_cond =1;
        $model->posted_by =$userID;
        $model->is_active =1;
        $model->lkp_service_id=_INTRACITY_;
        $model->title=$bo->title;
        $model->post_status =$bo->status == 'draft'?0:1;
        $model->routedata ='';
        $model->discount='';

        $model->from_date = $bo->validFrom;
        $model->to_date = $bo->validTo;       

        return $model;

    }
}