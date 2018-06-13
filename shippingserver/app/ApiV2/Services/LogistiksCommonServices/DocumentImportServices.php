<?php

namespace ApiV2\Services\LogistiksCommonServices;

use ApiV2\Services\BlueCollar\BaseServiceProvider;

use Log;
use PHPExcel_IOFactory;
use PHPExcel_Settings;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Exceptions\ApplicationException;
use ApiV2\Modules\Intracity\IntracitySellerPostTransformer;

use ApiV2\Requests\IntracitySellerPostRequest as SellerPostRequest;

use Validator;
use DB;
class DocumentImportServices extends BaseServiceProvider
{
    
    private $fields = array(
        'title'=>'required',
        'isTermAccepted' => 'required',
        'serviceId'=>'required',
        'isPublic'=>'required',
        'validFrom'=>'Valid from date is required for master table',
        'validTo'=>'required',
        'status'=>'required',
        'termsConditions'=>'required',
        'isTermAccepted'=>'required',  
        'attributes'=> array('routes' => array(
            'is_seller_buyer'=>'required',
            'lkp_service_id'=>'required',
            'type_basis'=>'required',
            'city_id'=>'City is required',
            'valid_from'=>'Valid from date is required',
            'valid_to'=>'Valid to date is required',
            'transit_hour'=>'required',
            'tracking'=>'required',
            'time_from'=>'Time from is required',
            'time_to'=>'Time to is required',
            'base_distance'=>'Base Distance is required',
            'base_time'=>'Base time is required',
            'rate_base_distance'=>'Rate Per/Km Base Distance is required',
            'cost_per_extra_hour'=>'required',
            'additional_hour_charge'=>'Additional hour charge is required',
            'odc_base_volume'=>'required',
            'vehicle_type_id'=>'Vehicle type is required'
        ),'discount'=> array(
            'buyerId'=>'required',
            'discountType'=>'required',
            'creditDays'=>'required',
            'discount'=>'required',
        )),      
    );

    public $errors = array();


    public function bulkSaveOrUpdate($request)
    {
        try {


            
            if (!$request->hasFile('uploadFile')) {
                throw new ApplicationException([], ["uploadFile needs to be specified"]);
            }
            
            PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);            
            //get the file
            $file = $request->file('uploadFile');

            $fileType = $file->getClientOriginalExtension();
            
            if($fileType !='xlsx'):
                array_push($this->errors,"Only Excel file is exceptable");
                return array('isSuccessful'=>false,'message'=>'Validation error','payload'=>$this->errors);    
            endif;
            


            //Load Excel
            $objPHPExcel = PHPExcel_IOFactory::load($file);
            
            //Parse Master Sheet
            $masterSheet = $objPHPExcel->getSheet(0);
            $topRow = $masterSheet->getHighestRow();
            $topColumn = $masterSheet->getHighestColumn();
            $master = [];
            
            for ($row = 1; $row <= $topRow; ++$row) {
                $masterRows = $masterSheet->rangeToArray('A' . $row . ':' . $topColumn . $row, NULL, TRUE, FALSE);
                array_push($master, $masterRows[0][1]);
            }
            
           // return $master;
            
            
            //Parse Details sheet
            $detailSheet = $objPHPExcel->getSheet(1);
            $topRow = $detailSheet->getHighestRow();
            
            $details = [];
            
            LOG::debug("Rows found in Details sheet [" . $topRow . "]");
            
            for ($row = 2; $row <= $topRow; ++$row) {
                $rowData = $detailSheet->rangeToArray('A' . $row . ':' . 'Z' . $row, NULL, TRUE, FALSE);
                array_push($details, $rowData[0]);
            }
            
            // return $details;


            //Parse Discounts sheet
            $discountSheet = $objPHPExcel->getSheet(2);
            $topRow = $discountSheet->getHighestRow();
            
            $discounts = [];
            
            LOG::debug("Rows found in Discount sheet [" . $topRow . "]");
            
            for ($row = 2; $row <= $topRow; ++$row) {
                $rowData = $discountSheet->rangeToArray('A' . $row . ':' . 'F' . $row, NULL, TRUE, FALSE);
                array_push($discounts, $rowData[0]);
                //$discounts = $rowData;
            }
            
           //  return $discounts;
            LOG::debug("Converting xls to bo");
            
            //Transform Excel rows into a BO.
            $transform = new IntracitySellerPostTransformer();
           
            $bo = $transform->xsl2boSave($master,$details,$discounts);
            LOG::debug("Converted to bo");
            // return (array)$bo;
            // Data pass to validator 
            // return "reach";

            $isValid = $this->validateXslData( (array) $bo);
            if (!$isValid) {
                return array('isSuccessful'=>false,'message'=>'Validation error','payload'=>$this->errors);
            }
            
            $masterModel = $transform->xsl2MasterModel($bo);
            
            DB::beginTransaction();
                $masterModel->save();            
                // Save Detail data into routes table
                $rateCartId = $masterModel->id;
                foreach ($bo->attributes->routes as $key => $value):
                    $value['fk_buyer_seller_post_id'] = $rateCartId;
                    $discounts = $bo->attributes->discount;
                    $routeDiscount = $value['discount'];

                    Log::info("Disc : json".json_encode($routeDiscount));
                    unset($value['discount']);
                    unset($value['attributes']);
                    
                    $routeId = DB::table('intra_hp_buyer_seller_routes')->insertGetId($value);
                    // Save individual discount
                    $this->xslDiscount2Model($routeDiscount,$rateCartId,$routeId);
                    
                endforeach;
                
                // Global discount                     

                if(count($discounts) > 0):
                    if(!$this->validateXslDiscount($discounts))
                        return array('isSuccessful'=>false,'message'=>'Validation error','payload'=>$this->errors);
                    else
                        $disArray = $this->xslDiscount2Model($discounts,$rateCartId,0);
                endif;

            DB::commit();
            
            return array('isSuccessful'=>true,'message'=>'Data successfully imported','payload'=>$bo);
            
            
        } catch (\Exception $e) {
            DB::rollback();
            LOG::error('Exception while bulkSaveOrUpdateof SellerPost ', (array)$e->getMessage());

            array_push($this->errors,"Your file format is not valid");
            return array('isSuccessful'=>false,'message'=>'Validation error','payload'=>$this->errors);
        }
    }



    public function validateXslData($bo) {  
        // return $bo;
        // Validate master data

        foreach ($this->fields as $key => $value) {
            if(array_key_exists($key,$bo)) {
                if($bo[$key] =='') {
                    $msg = '';
                    if($value == 'required') {
                        $msg = "$key is required";
                    } else {
                        $msg = $value; 
                    }
                    array_push($this->errors,$msg);
                }
            }             
        }   
        
        // Validate details data 
        if(!count($this->errors)) {
            foreach ($bo['attributes']->routes as $rKey => $route) {
                foreach ($this->fields['attributes']['routes'] as $key => $value) {
                    if(array_key_exists($key,$route)) {
                        if($route[$key] =='') {
                            $r = $rKey+1;
                            $msg = '';
                            if($value == 'required') {
                                $msg = "$key is required for Route $r";
                            } else {
                                $msg = $value." for Route $r"; 
                            }
                            array_push($this->errors,$msg);
                        }
                    }             
                }
                // Let's validate discount for route 
                Log::info('Count:'. json_encode($route));
                Log::info('Count:'. count($route['discount']));
                if(count($route['discount']) > 0) {
                    $this->validateXslDiscount($bo['attributes']->discount);
                }
            }                        
        }
        
        return count($this->errors) == 0? true : false;
    }

    public function validateXslDiscount($discounts) {
        try {
            if(count($discounts) > 0):
                Log::info('message'. json_encode($this->fields['attributes']['discount']));
                foreach ($discounts as $key => $value):
                    $value = (array)$value;
                    Log::info("jhj".$value['discountType']);
                    foreach ($this->fields['attributes']['discount'] as $dKey => $d) {
                        if($value[$dKey] == '') {
                            $r = $key+1;
                            $msg = '';
                            if($d == 'required') {
                                $msg = "$dKey is required for Discount";
                            } else {
                                $msg = $d." for Discount"; 
                            }
                            array_push($this->errors,$msg);
                        }
                    }                                
                endforeach;      
            endif;
        } catch(Exception $e) {

        }      
        
        return count($this->errors) == 0? true : false; 
    } 


    private function saveOrUpdateXslData($bo) {
        return $bo;
    }

    private function xslDiscount2Model($discounts,$id = null,$routeId = null)
    {     
        $discountArray = array();

        if(is_array($discounts)) {
            foreach ($discounts as $key => $value) {
                DB::table('intra_hp_discounts')->insert(array(
                    "fk_rate_card_id"=>$id,
                    "lkp_service_id"=>_INTRACITY_,
                    "intra_hp_sellerpost_ratecart_id"=>$routeId,
                    "buyer_id"=>$value->buyerId,
                    "discount_basis"=> $routeId != ''? 1:2,
                    "disc_type"=>$value->discountType == 'Percentage'?1:2,
                    "disc_amt"=>$value->discount,
                    "net_price"=>"",
                    "credit_days"=>$value->creditDays,
                    "is_active" => 1
                ));
            }
        } else {
            Log::info("Invalid Discount");
        }
        return $discountArray;
    }

}
