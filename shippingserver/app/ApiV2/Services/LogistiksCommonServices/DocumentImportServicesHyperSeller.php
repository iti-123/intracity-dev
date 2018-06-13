<?php

namespace ApiV2\Services\LogistiksCommonServices;

use ApiV2\Services\BlueCollar\BaseServiceProvider;

use ApiV2\Model\HyperLocal\SellerRateCard;
use ApiV2\Model\IntraHyperRoute;
use ApiV2\Model\IntraHyperDiscount;
use DB;
use ApiV2\Services\LogistiksCommonServices\CsvImportExportService;
use Log;
use PHPExcel_IOFactory;
use PHPExcel_Settings;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Exceptions\ApplicationException;
use ApiV2\Modules\Intracity\IntracitySellerPostTransformer;
use ApiV2\Requests\IntracitySellerPostRequest as SellerPostRequest;
use Validator;

class DocumentImportServicesHyperSeller extends BaseServiceProvider
{
    
    public function bulkSaveHyperlocalSeller($request)
    {   
        
        $master_array_key = [
            'seller_id' => null,
            'rate_card_title' => 'required',
            'valid_from' => 'required',
            'valid_to' => 'required',
            'terms_condition' => null,
            'post_type' => null,
            'overwrite' => null,
            'user_time_zone' => null
        ];
        
        $custom_array_key = [

                'lkp_service_id' => null,        // rate card id
                'city_id' => 'required',               // city
                'title'=> 'required',                 // Title
                'product_category' => 'required',      // SERVICE TYPE (express - 1 /fast - 2/sameday - 3)
                'service_type' => 'required',          // SELECT PRODUCT (Ecommerce - 1/Food - 2/Documents - 3/Grocery - 4)
            
                'line_items' => 'required',            // LINE ITEM (int)
                'base_price' => 'required',            // BASE PRICE 
                'additional_charges'=> 'required',           //FRAGILE ADDITIONAL CHARGE (int)
                'dist_included_per_base' => 'required',       // DIS INCLUDED (int)
                'rate_per_extra_kms' => 'required',           // RATE PER EXTRA KM (int)
            
                'weight_included' => 'required',       // WEIGHT INCLUDED (int)
                'rate_per_extra_kgs' => 'required',    // WEIGHT PER EXTRA KG (int)
                'num_parcels_included' => 'required',  // NUMBER OF PARCELS (int)
                'additional_cost_per_ext_parcel' => 'required',      //ADDITIONAL COST EXTRA PARCEL (int)
                'time_for_selected_service' => 'required',           // TIME IN HOURS (int)
            
                'extra_time_per_km' => 'required',     // EXTRA TIME PER KM (int)
                'from_date' => null,
                'to_date' => null,
                'posted_by' => null,
                'extra_time_per_kms' => null,
            
                'pricing_service_types' => null,
                'rate_cart_type' => null, 
                'is_private_public' => null, 
                'terms_cond' => null, 
                'post_transaction_id' => null,
            
                'post_status' => null,
                'created_at' => null            
                ];
        
        //$temp_key_value = [1,1,1,1,1,0,11,1,null,1,''];
        $temp_key_value = [''];
        
        try {

            if (!$request->hasFile('uploadFile')) { 

                throw new ApplicationException([], ["uploadFile needs to be specified"]);
            }
            
            PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);            
            //get the file
            $file = $request->file('uploadFile');

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
            
            $master_array_key_value = array_combine( array_keys($master_array_key), $master);
            array_shift($master_array_key_value);
            //print_r($master_array_key_value);

            
            //Parse Details sheet
            $detailSheet = $objPHPExcel->getSheet(1);
            $topRow = $detailSheet->getHighestRow();

/*
 * *  first sheet data insertion start here **
 */            
            
            $details = $all_excel_record = $custom_prepared_array = $rate_card_id_from_excel = $getError = [];
			$getError1 = $getError2 = '';
			
            LOG::debug("Rows found in Details sheet [" . $topRow . "]");
            
            for ($row = 1; $row <= $topRow; ++$row) {
                $rowData = $detailSheet->rangeToArray('A' . $row . ':' . 'Z' . $row, NULL, TRUE, FALSE);
                array_push($details, $rowData[0]);
            }
            

            array_shift($details);
           $last_id = DB::table('intra_hp_sellerpost_ratecart')->select('id')->orderBy('id', 'DESC')->first();
            
            foreach($details as $key => $record){
                
				$custom_post_transaction_id = (string) 'HYPER/' . date('Y') . '/0000' . ( $last_id->id + 1+$key ) ; 
                $custom_record = array_merge($record, $temp_key_value);
                $custom_prepared_array = array_combine( array_keys($custom_array_key) , $custom_record); 
                $rate_card_id_from_excel[] = $custom_prepared_array['lkp_service_id'];   // get rate_card_id_from_excel
				
                $custom_prepared_array['lkp_service_id'] = _HYPERLOCAL_;
                $custom_prepared_array['posted_by'] = JWTAuth::parseToken()->getPayload()->get('id');
                $custom_prepared_array['rate_cart_type'] = 1 ? 1 : 0;
                $custom_prepared_array['is_private_public'] =  ($master_array_key_value['post_type'] == 'Yes' || $master_array_key_value['post_type'] == 'yes') ? 1 : 0;
                $custom_prepared_array['terms_cond'] =  1 ? 1 : 0;                       
               	$custom_prepared_array['post_transaction_id'] =  1 ? $custom_post_transaction_id : 0 ;
                $custom_prepared_array['post_status'] =  1 ? 1 : 0;
                $custom_prepared_array['from_date'] =  ($master_array_key_value['valid_from'] != '') ? $master_array_key_value['valid_from'] : date('Y/M/d');
                $custom_prepared_array['to_date'] =  ($master_array_key_value['valid_to'] != '') ? $master_array_key_value['valid_to'] : date('Y/M/d');
                $custom_prepared_array['city_id'] = (CsvImportExportService::getCityIdByCityName( $custom_prepared_array['city_id'] )['status']) ? 
                        CsvImportExportService::getCityIdByCityName( $custom_prepared_array['city_id'] )['id'] : 0;
                $all_excel_record[] = $custom_prepared_array;
            } 
            
/*
 * *  first sheet data insertion end here **
 */            


            $discountSheet = $objPHPExcel->getSheet(2);
            $topRow = $discountSheet->getHighestRow();
			
//var_dump( $masterSheet, $detailSheet, $discountSheet ); 
/*
 * *  Second sheet data insertion start here **
 */            
            
            $discounts = $all_excel_record_discount = $custom_array_prepared = [];
			
			$details_check_response = $discounts_check_response = '';
            
            LOG::debug("Rows found in Discount sheet [" . $topRow . "]");
            
            for ($row = 2; $row <= $topRow; ++$row) {
                
                $rowData = $discountSheet->rangeToArray('A' . $row . ':' . 'F' . $row, NULL, TRUE, FALSE);
                array_push($discounts, $rowData[0]);
                //$discounts = $rowData;
            }
            
// no record check start here 
			
			$error_info = []; 
			
				if( count( array_filter($master) ) == 0 ){
					
					$error_info[] = ['page' => 1, 'error_msg' => 'no record found' ];
					
				}
			
			$details_check_response = $this->checkIfNoRecord( $details, 2 );
			
			is_array( $details_check_response ) ? $error_info[] = $details_check_response : '' ;
			
			$discounts_check_response = $this->checkIfNoRecord( $discounts, 3 );
			
			 is_array( $discounts_check_response ) ? $error_info[] = $discounts_check_response : '' ; 
			
			if( count( $error_info ) ){
			
			 	return array( 'no_record_flag' => true, 'message' => 'Data imported failed', 'no_record_data' => json_encode( $error_info ) );
			}			
			
// no record check end here 			
			
			
            // Database table column as key
            $discount_array_key = [
                'discount_level' => 'required',      // discount service type
                'lkp_service_id' => 'required',     // service type / e-mail
                'disc_type' => 'required',          // discount type
                'disc_amt' => 'required',           // discount amount
                'intra_hp_sellerpost_ratecart_id' => 'required',  //rate card id
                'credit_days' => 'required',                      //credit days
                'discount_basis' => null,
                'net_price' => null,
                'fk_rate_card_id' => null,            
                'buyer_id' => null     
            ];
  
            // adding extra value to key - require for array_combine (key value count must be same)
            $temp_key_value_discount = [0,0,0,0];



            foreach($discounts as $key => $record){

                $custom_record = array_merge($record, $temp_key_value_discount);
				
                $custom_array_prepared = array_combine( array_keys($discount_array_key), $custom_record);
                
                switch ( $custom_array_prepared['discount_level'] ) {
                    
                    case 1:
                        
                        break;
                    
                    case 2:
                        
                           $get_buyer_id_data = CsvImportExportService::getBuyerIdByEmail( trim($custom_array_prepared['lkp_service_id']) ); 
                           $custom_array_prepared['buyer_id'] = $get_buyer_id_data['status'] ? $get_buyer_id_data['id'] : 0;
                           
                        break;                    
                    
                    case 3:

                        break;                    
                }
                
                switch (trim($custom_array_prepared['disc_type'])){

                    case 'Percentage':
                        $custom_array_prepared['disc_type'] = 1;
                        break;
                    
                    case 'Fixed':
                        $custom_array_prepared['disc_type'] = 2;                        
                        break;
                } 

                $custom_array_prepared['lkp_service_id'] = _HYPERLOCAL_;
                
                $all_excel_record_discount[] = $custom_array_prepared;
            } 

   			 $getError1 = $this->validateXslData($master_array_key_value, $master_array_key, 1);
			
             $getError2 = $this->validateXslData($all_excel_record, $custom_array_key, 2);
			
             $getError3 = $this->validateXslData($all_excel_record_discount, $discount_array_key, 3);  // raju
			
			 $getError = array_merge($getError1, $getError2, $getError3);	

			if( count($getError) ){
			
			 	return array('isSuccessful'=>false,'message'=>'Data imported failed','payload'=> json_encode($getError));
			}else{
			
			 //var_dump($getError1, $getError2, $getError3);

/*      ---------------------------    */            
            // to get last id 
            
DB::beginTransaction();
				
            SellerRateCard::insert( $all_excel_record );
            
            // now get last inserted id greater then last id 
            $get_last_inserted_ids_ratecard = DB::table('intra_hp_sellerpost_ratecart')->select('id')->where('id', '>', $last_id->id)->get();
            
            $temp_arr =  [];
                    
            foreach($get_last_inserted_ids_ratecard as $val){
                
                $temp_arr[] = $val->id;
            }
/*      ---------------------------    */    
            
            
            // to map rate card and its respective discounts we need rate card key in discount record
            if( count($temp_arr) ) {
				
                 $get_rate_card_key_value = array_combine($temp_arr, $rate_card_id_from_excel);            
            }else{
				
                $get_rate_card_key_value = [];
            }

            foreach($all_excel_record_discount as $key => $record){
				
	               $record['intra_hp_sellerpost_ratecart_id'] = array_search( $record['intra_hp_sellerpost_ratecart_id'], $get_rate_card_key_value) ?  array_search( $record['intra_hp_sellerpost_ratecart_id'], $get_rate_card_key_value) : 0;
            }			
            
           IntraHyperDiscount::insert( $all_excel_record_discount );   // raju  $details  $discounts
           
DB::commit();
			 LOG::debug( "successfully commited" );
				
			 return array( 'isSuccessful' => true, 'message' => 'Data successfully imported', 'payload' => '' );
	         
/*
 * *  second sheet data insertion end here **
 */            
		}
			
        } catch (\Exception $e) {
DB::rollback();            
            LOG::error('Exception while bulkSaveOrUpdateof SellerPost ', (array)$e->getMessage());
        }
    }
    

    public function validateXslData($param, $rules, $sheet_name) {  
		
        $custom_error = [];
		
		if( $sheet_name == 2 || $sheet_name == 3){
			
				( count($param) == 0 ) ? $custom_error[ $sheet_name ][ 0 ][ $sheet_name - 1 ] = 'no record found' : '';
            	        
				if( count($param) ) {
					
                    foreach( $param as $k => $val){
                      foreach ($val as $key => $value) {
                          if(array_key_exists($key,$rules)) {
                              if($value == '') {
                                  $msg = '';
                                  if($rules[$key] == 'required') {
                                      $msg = "$key is required";
                                      $custom_error[ $sheet_name ][ $k ][ $k ] = $msg;
                                  }

                              }
                          }             
                      }
                    }
				}
		}
		
		if( $sheet_name == 1){
			
			( count($param) == 0 ) ? $custom_error[ $sheet_name ][ 0 ][ $sheet_name - 1 ] = 'no record found' : '';
			
			$i = 0;
			if( count($param) ) {
				
                    foreach($param as $key => $value) {

                        if(array_key_exists($key,$rules)) {
                            if($value == '') {
                                $msg = '';
                                if($rules[$key] == 'required') {
                                    $msg = "$key is required";
                                    $custom_error[ $sheet_name ][ $i ][ $key ] = $msg;
                                                            $i++;    
                                }

                            }
                        }        
                    }			
			}
		}
			
		return $custom_error;

    }
	
	
	function checkIfNoRecord( $page_data, $page_name ){
		
		$get_array_dept_count = [];
		
			foreach( $page_data as $k => $v ){
				 $get_array_dept_count[] = count( array_filter( $v ) );
			}
			
			
			if( array_sum( $get_array_dept_count ) == 0 ){
					 
					return ['page' => $page_name, 'error_msg' => 'no record found' ];
				
			}else{
				
					return false;
			}	
	}

}
