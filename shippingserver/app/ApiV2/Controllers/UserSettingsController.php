<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 4/5/17
 * Time: 8:33 PM
 */

namespace ApiV2\Controllers;


use ApiV2\Requests\BaseShippingResponse as ShipRes;
use ApiV2\Services\UserSettingsService;
use Illuminate\Http\Request;
use Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use ApiV2\Model\Settings;
use Input;



class UserSettingsController extends BaseController
{

    public function getUserSettings($serviceId, $context)
    {

        try {

            LOG::info("Getting user settings");

            $userId = JWTAuth::parseToken()->getPayload()->get('id');

            if ($serviceId == 'fcl') {
                $serviceId = FCL;
            }
            if ($serviceId == 'lcl') {
                $serviceId = LCL;
            }

            $results = UserSettingsService::getUserSettings($serviceId, $context, $userId);

            return ShipRes::ok($results);



        } catch (\Exception $e) {

            LOG::error($e->getMessage());
            return $this->errorResponse($e);

        }

    }

    public function storeUserSettings($serviceId, $context, Request $request)
    {


        try {

            if ($serviceId == 'fcl') {
                $serviceId = FCL;
            }
            if ($serviceId == 'lcl') {
                $serviceId = LCL;
            }


            LOG::info("Storing user settings");

            $userId = JWTAuth::parseToken()->getPayload()->get('id');

            $payload = $request->getContent();

            $settings = json_decode($payload);

            LOG:
            info((array)$settings);

            $results = UserSettingsService::storeUserSettings($serviceId, $context, $userId, (array)$settings);

            return ShipRes::ok($results);

        } catch (\Exception $e) {

            LOG::error($e->getMessage());
            return $this->errorResponse($e);

        }

    }

     /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * this function for creating and updateing the seetings for all services and users
     */

    //  public function userSettingAdd(Request $request)
    //  {

    //     $userId = JWTAuth::parseToken()->getPayload()->get('id');
    //     $userRole = JWTAuth::parseToken()->getPayload()->get('role');        
    //     $serviceId = _INTRACITY_;
    //     //$pageType = (isset($_REQUEST['page_type']) ? $_REQUEST['page_type'] : POST_MASTER) ;

    //     $UserSetting = new \ApiV2\Model\Settings;
    //     $UserCnt = $UserSetting::where(['user_id' => $userId,
    //         // 'role_id' => $userRole,'service_id' => $serviceId,'page_type' => $pageType
    //         'role_id' => $userRole,'service_id' => $serviceId
    //     ])->first();
        
    //     $settings = serialize();
    //     if($UserCnt){
    //         $UserSettingUpdate = $UserSetting::where([
    //             'user_id' => $userId,
    //             'role_id' => $userRole,
    //             'service_id' => $serviceId,
    //             // 'page_type' => $pageType
    //             ])->update([
    //                 'settings'      => $settings,
    //                 'updated_by'    => $userId,
    //                 'updated_at'    => date ( 'Y-m-d H:i:s' )]
    //                 );

    //             return "Settings Updated Successfully.";
    //     }else{
    //         $UserSettingInsert = $UserSetting::create([
    //             'user_id'   => $userId,
    //             'role_id'   => $userRole,
    //             'service_id' => $serviceId,
    //             // 'page_type' => $pageType,
    //             'settings'      => $settings,
    //             'created_by'    => $userId,
    //             'created_at'    => date ( 'Y-m-d H:i:s' ),
    //             'updated_by'    => $userId,
    //             'updated_at'    => date ( 'Y-m-d H:i:s' )
    //         ]);
    //         return "Settings Updated Successfully";
    //     }
    //  }


     public static function settingsUpdate(Request $request) 
     {
        $userid =  $request->data['userId'];
        $serviceId = $request->data['serviceId'];
        $settings = $request->data['settings'];
        $userRole = $request->data['role'];

        
        $newSettings = array_flip($settings);
        foreach($newSettings as $key=>$value)
        {
            $newSettings[$key] = 'on';
        }
        // return $newSettings;



        $UserSetting = new \ApiV2\Model\Settings;
        $UserCnt = $UserSetting::where(['user_id' => $userid,
            // 'role_id' => $userRole,'service_id' => $serviceId,'page_type' => $pageType
            'role_id' => $userRole,
            'service_id' => $serviceId
        ])->first();
        
        $settings = serialize($newSettings);
        if($UserCnt){
            $UserSettingUpdate = $UserSetting::where([
                'user_id' => $userid,
                'role_id' => $userRole,
                'service_id' => $serviceId,
                // 'page_type' => $pageType
                ])->update([
                    'settings'      => $settings,
                    'updated_by'    => $userid,
                    'updated_at'    => date ( 'Y-m-d H:i:s' )]
                    );

                return "Settings Updated Successfully.";
        }else{
            $UserSettingInsert = $UserSetting::create([
                'user_id'   => $userid,
                'role_id'   => $userRole,
                'service_id' => $serviceId,
                // 'page_type' => $pageType,
                'settings'      => $settings,
                'created_by'    => $userid,
                'created_at'    => date ( 'Y-m-d H:i:s' ),
                'updated_by'    => $userid,
                'updated_at'    => date ( 'Y-m-d H:i:s' )
            ]);
            return "Settings Updated Successfully";
        }

                
     }

    //   public static function userSettings($type, $service_id = 0, $user_id = 0, $role = 0)
    // {
    //     if ($role == 0) $role = self::getUserRole();
    //         if($type==NOTIFICATIONS){
    //             $service_id =0;
    //         }else{ 
    //             $service_id = Session::get('service_id');
    //         }


    //     //if ($service_id == 0) $service_id = Session::get('service_id');
       
    //     if ($user_id == 0) $user_id = Auth::id();

    //     $UserSetting = new \App\Models\UserSetting;

    //     $UserCnt = $UserSetting::where(['user_id' => $user_id, 'role_id' => $role, 'service_id' => $service_id, 'page_type' => $type])->first();
    //     //dd($UserCnt);
    //     if ($UserCnt) {
    //         $usersettings = unserialize($UserCnt->settings);
            
    //         return $usersettings;
    //     }else{

    //       $usersettings = array();
    //       if($role==BUYER){
    //        $usersettings["se_all_rate_card"] = "on";
    //        $usersettings["se_all_private_posts"] = "on";
    //        $usersettings["partly_related_leads"] = "on";
    //        $usersettings["unrelated_leads"] = "on";
    //        $usersettings["notifyratecard"] = "on";
    //        $usersettings["notifysystem"] = "on";
    //       }
    //       if($role==SELLER){
                
    //           $usersettings["se_par_rel_enqs"] = "on";
    //           $usersettings["se_unrelated_enqs"] = "on";
    //           $usersettings["sl_partly_post"] = "on";
    //           $usersettings["sl_unrel_post"] = "on";
    //           $usersettings["te_partly_rel_enqs"] = "on";
    //           $usersettings["se_os_enq"] = "on";
    //           $usersettings["sl_os_posts"] = "on";
    //           $usersettings["te_os_enqs"] = "on";
    //           $usersettings["te_unrel_enqs"] = "on";
    //           $usersettings["sl_partly_rel_enqs"] = "on";
    //           $usersettings["sl_os_enqs"] = "on";
    //           $usersettings["sl_unrel_enqs"] = "on";
    //           $usersettings["sellerenqueries"] = "on";
    //           $usersettings["systemnotify"] = "on";
    //       } 
    //         return $usersettings; 
    //     }
    //     return array();
    // }



}