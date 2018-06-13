<?php
/**
 * Created by Raju Gupta.
 * Seller Post MasterSetting Only
 * Date: 06-08-2017
 * 
 */

namespace ApiV2\Controllers\Intracity;


use Api\Controllers\BaseController;
use Api\Model\Intracity\SellerPostMasterSetting as SellerPostMasterSetting;
use Illuminate\Http\Request;

//use Api\Requests\Hyperlocal\BuyerPostRequest;
//use Api\Services\BlueCollar\SellerRegistrationService;


class SellerSettingPost extends BaseController
{

    public function saveSellerPostMasterSetting(Request $request)
    {
        //print_r($request->input());
    	//echo implode(',', $request->input());
        //Array
        //(
        //    [seller_spot_enquiries_related] => 1
        //    [seller_spot_enquiries_partly_related] => 
        //    [seller_spot_enquiries_un_related] => 
        //    [seller_spot_lead_related] => 1
        //    [seller_spot_lead_partly_related] => 
        //    [seller_spot_lead_un_related] => 
        //    [seller_term_enquiries_related] => 1
        //    [seller_term_enquiries_partly_related] => 
        //    [seller_term_enquiries_un_related] => 
        //    [seller_term_lead_related] => 1
        //    [seller_term_lead_partly_related] => 
        //    [seller_term_lead_un_related] => 
        //    
        //    [user_id] => 99
        //    [user_type] => 0 // flag ( 0 = 'seller', 1 = 'buyer' )
        //    [role_id] => 99
        //    [service_id] => 99
        //    [page_type] => 88
        //    [setting_type] => 0 // flag ( 0 = 'post_master_setting', 1 = 'notification_setting' )
        //    [updated_by] => 99
        //)        
               
        
    	// If there are ['user_id' => $request->input('user_id'), 'role_id' => $request->input('role_id'), 'service_id' => $request->input('service_id'), 'page_type' => $request->input('page_type'), 'user_type' => $request->input('user_type'), 'setting_type' => $request->input('setting_type')], then 
        // set the ['settings' => serialize($request->input()), 'updated_by' => $request->input('updated_by')].
	// If no matching model exists, create one. 
        
//                ['user_id' => $request->input('user_id'),
//                'user_type' => $request->input('user_type'),
//                'role_id' => $request->input('role_id'), 
//                'service_id' => $request->input('service_id'), 
//                'page_type' => $request->input('page_type'),
//                'setting_type' => $request->input('setting_type')
//                ]       
              
              //print_r(array_slice($request->input(), -7, 6));
        
            return SellerPostMasterSetting::updateOrCreate(array_slice($request->input(), -7, 6),
                ['settings' => serialize($request->input()), 
                'updated_by' => $request->input('updated_by')
                ]
            );
    	  
            /*
              $seller_model_obj = SellerPostMasterSetting::find(8);
  	      $seller_model_obj->user_id  = '77';
  	      $seller_model_obj->role_id  = '77';
  	      $seller_model_obj->service_id  = '77';
  	      $seller_model_obj->page_type  = '7';
  	      $seller_model_obj->settings  = serialize($request->input());
  	      $seller_model_obj->updated_by  = '1077';
  	      $seller_model_obj->save(); 
            */
    }
    
    public function saveBuyerPostMasterSetting(Request $request){

           $this->saveSellerPostMasterSetting($request);
    }

}