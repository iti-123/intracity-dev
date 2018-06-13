<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 2/21/17
 * Time: 12:43 PM
 */

namespace ApiV2\Services;
use Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use ApiV2\Services\UserSettingsService;
use ApiV2\Controllers\UserServices;
use ApiV2\Model\IntraHyperSellerPost;
use ApiV2\Model\IntraHyperBuyerPost;


class SettingsSearchService
{


    public static function filterQuery($query,$input,$activeUserDetails,$key,$settingValue) {
        if('se_all_rate_card' == $key) {
            
            $query->with(['routes.city','routes.fromLocalities','routes.toLocalities','routes.vehicleType', 'routes'=> function($query) use ($input) {
                
                if($input->isInbound == 'inbound') {
                    self::applyRouteFilter($query, $input);
                }
                
            }]);

        }else if('partly_related_enquiry' == $key) {
            
            $query->with(['routes.city','routes.fromLocalities','routes.toLocalities','routes.vehicleType', 'routes'=> function($query) use ($input) {
                
                if($input->isInbound == 'inbound') {
                    self::applyRouteFilter($query, $input);
                }
                
            }]);

        }else if('partly_related_leads' == $key) {
            
            $query->with(['routes.city','routes.fromLocalities','routes.toLocalities','routes.vehicleType', 'routes'=> function($query) use ($input) {
                
                if($input->isInbound == 'inbound') {
                    self::applyRouteFilter($query, $input);
                }
                
            }]);

        }else if('se_all_private_posts_enquiry' == $key) {
            
            $query->with(['routes.city','routes.fromLocalities','routes.toLocalities','routes.vehicleType', 'routes'=> function($query) use ($input) {
                
                if($input->isInbound == 'inbound') {
                    self::applyRouteFilter($query, $input);
                }
                
            }]);

        }else if('se_all_private_posts' == $key) {
            
            $query->with('routes.city','routes.fromLocalities','routes.toLocalities','routes.vehicleType')
                    ->where("is_private_public","=",1);
        
        }else if('from_location_enquiry_term' == $key) {
            
            $query->with('routes.fromLocalities')
                    ->where("is_active",1);
        }else if('partly_related_leads' == $key) {
         
            $query->with(['routes'=> function($query) use ($activeUserDetails) {
                    $query->where([
                        ['city_id','=', $activeUserDetails->lkp_city_id]
                    ]);
                }])
                ->where("is_private_public","=",1);
        }else if('unrelated_leads' == $key) {
        
            $query->with('routes')
                ->where("is_private_public","=",0);
        
        }else if('se_unrelated_enquiry' == $key) {

           $query->with('routes.city','routes.fromLocalities','routes.toLocalities','routes.vehicleType')
                    ->where("is_private_public","=",1);
        }else if('from_location_leads' == $key) {

           $query->with('routes.fromLocalities')
                    ->where("is_active",1);

        }else if('from_location_enquiry' == $key) {
            $query->with('routes.fromLocalities')
                    ->where('is_active',1);
        }else if('partly_related_term_enquiry' == $key) {

            $query->with(['routes'=> function($query) use ($activeUserDetails) {
                    $query->where([
                        ['city_id','=', $activeUserDetails->lkp_city_id]
                    ]);
                }])
                ->where("is_private_public","=",1);
        }else if('unrelated_enquiry_term' == $key) {

             $query->with(['routes'=> function($query) use ($activeUserDetails) {
                    $query->where([
                        ['city_id','!=', $activeUserDetails->lkp_city_id]
                    ]);
                }])
                ->where("is_private_public","=",1);

        }

       return $query->latest()->get();
    }

    private static $userSettings = array(
        "se_all_rate_card"=>"Spot partly related enquiry",
        "se_all_private_posts"=>"Spot unrelated enquiry ",
        "partly_related_leads"=>"Spot partly related leads",
        "unrelated_leads"=>"Spot unrelated leads",
        "partly_related_enquiry"=>"Partly related enquiry",
        "se_all_private_posts_enquiry"=>"Unrelated enquiry",
        "partly_related_leads"=>"Partly related leads",
        "from_location_enquiry_term"=>"From location enquiry term",
        "se_unrelated_enquiry"=>"Unrelated enquiry",
        "from_location_enquiry"=>"From location enquiry",
        "from_location_leads"=>"From location leads",
        "partly_related_term_enquiry"=>"Partly related term enquiry",
        "unrelated_enquiry_term"=>"Unrelated enquiry term"

        //"unrelated_leads"=>"unrelated leads"
    );

    public static function filterBySettings($query, $input ,$settings, $activeUserDetails) {
        $tempArray = array();
        foreach($settings as $key => $setting):
            if($key != '_token') {
                if($key && array_key_exists($key, self::$userSettings) && $settings[$key] == 'on') {
                    array_push($tempArray,
                        array(
                         "title" => self::$userSettings[$key],
                         "value" => self::filterQuery($query,$input,$activeUserDetails,$key, $settings[$key])
                        )
                    );
                } else {
                    return "not exist";
                }
            }
        endforeach;
        return $tempArray;
    }


    public static function filterInbound($request) {
       Log::info("filter inbound details");
        $postData = '';
        
        $userID = JWTAuth::parseToken()->getPayload()->get('id');  
        $serviceId = $request->serviceId;
        $settings = UserSettingsService::getUserSettings($request->serviceId,'', $userID);
        
        $activeUserDetails = (new UserServices)->getUserDetailsById($userID);
        /** for default settings*/
        $settings['partly_related_enquiry'] ='on';
        $settings['se_all_private_posts'] = 'on';
        if(sizeof($settings) && !empty($activeUserDetails)) {
        // Start filter coding
            



            if( isset($request->role) && !empty($request->role) && $request->role =='Seller') {
                $query  = IntraHyperBuyerPost::with('buyer')
                    ->where(function($query) use($request) {  
                        static::defaultFilter($query);            
                        static::applyInboundFilter($query, $request);
                        static::applyPostFilter($query, $request);                      
                    });
            } else {                
                $query  = IntraHyperSellerPost::with('seller')
                    ->where(function($query) use($request) {  
                        static::defaultFilter($query);            
                        static::applyInboundFilter($query, $request);
                        static::applyPostFilter($query, $request);                      
                    });
            } 
            
            $query->with('routes.quote');
            
            if(array_search(ucfirst($request->title),static::$userSettings,true)) {
                
                $settingKey = array_search(ucfirst($request->title),static::$userSettings,true);
                                                 
                $postData = self::filterQuery($query,$request, $activeUserDetails,$settingKey, 'on');
                
                return response()->json([
                    "payload" => $postData,               
                ]);

            } else {

                $postData = self::filterBySettings($query,$request, $settings, $activeUserDetails);
                
                return response()->json([
                    "payload" => $postData,               
                ]);
            
            }
        } else {
            return "Invalid settings and user details";
        }              
    } 

    public static function applyPostFilter($query, $input) {
        return $query->where(function($query) use ($input) {           

            if(isset($input->postStatus) && !empty($input->postStatus)) {
                $query->whereIn('post_status',$input->postStatus);
            }
    
            if(isset($input->postType) && !empty($input->postType)) {
                $query->whereIn('rate_cart_type',$input->postType);
            }

        });
    }

    public static function defaultFilter($query) {
        return $query->where(function($query) {
            $query->where([
                ['lkp_service_id',_INTRACITY_],
                ['is_active','=',1]
            ]);          

        });
    }


    public static function applyRouteFilter($query, $input) {
        
        if(isset($input->vehicleType) && !empty($input->vehicleType)) {
            $query->whereIn('vehicle_type_id',$input->vehicleType);
        }

        // if(isset($input->fromDate) && !empty($input->fromDate)) {
        //     $query->whereIn('valid_from',$input->fromDate);
        // }
        // if(isset($input->toDate) && !empty($input->toDate)) {
        //     $query->whereIn('valid_to',$input->toDate);
        // }
        return $query;
    }


    public static function applyInboundFilter($query, $input) {
        return $query->where(function($query) use ($input) {
            if ($input->type == 'spot') {
                $query->where('lead_type', 1);
            } else if ($input->type == 'term') {
                $query->where('lead_type', 2);
            } else if ($input->type == 'public') {
                $query->where('is_private_public', 0);
            } else if ($input->type == 'private') {
                $query->where('is_private_public', 1);
            }
        });
    }





    

}