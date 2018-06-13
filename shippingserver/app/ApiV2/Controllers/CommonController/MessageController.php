<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 07-02-2017
 * Time: 19:37
 */

namespace ApiV2\Controllers\CommonController;


use ApiV2\Controllers\BaseController;
use ApiV2\Services\LogistiksCommonServices\MessageServices;
use Exception;
use Illuminate\Http\Request;
use Pusher\Pusher;
use ApiV2\Services\SendSmsService as Sms;
use DB;
use ApiV2\Services\UserViewService;
use ApiV2\Services\NotificationService;
use Log;
class MessageController extends BaseController
{
    public function getMessage(Request $request)
    {
        try {
            return $message = MessageServices::getMessage($request);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }
    
    public function communityMessage(Request $request)
    {
        try {
            return $message = MessageServices::communityMessage($request);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function reply(Request $request, $messageId)
    {
        try {
            return $message = MessageServices::reply($request, $messageId);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    /** Get Message Details by Sender Id*/
    public function getMessageBySenderId($senderId)
    {
        try {
            return $message = MessageServices::getMessageBySenderId($senderId);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }
    /** Get Message Details by Sender Id*/


    public function getNotification(Request $request) 
    {
        try {
            return $message = MessageServices::getMessageNotification($request);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function broadcastMessage($channel, $event)
    {

        $options = array(
            'cluster' => 'ap2',
            'encrypted' => true
        );
        $pusher = new Pusher(
            '276a19811bb8506bfcf0',
            '80bfbea76dfe6a4d817d',
            '365743',
            $options
        );
        $data['message'] = 'hello world';
        try {
            return response()->json(["statuswsss" => $pusher->trigger(array($channel), $event, $data)]);
        } catch (Exception $e) {

        }

        // $message = new BroadcastMessage($pusher);
        // return response()->json(["status"=>$message->broadcast(array($channel), $event,$data)]); 

    }

    /** Message Updating If Buyer or Seller Read Messages */
    public function updateMessage(Request $request)
    {
        try{
            return $updateMessage = MessageServices::updateMessage($request);
        } catch(Exception $e) {
            return $this->errorResponse($e);
        }
    }
    /** Message Updating If Buyer or Seller Read Messages */

    /** Count for Un-read Messages */
    public function messageCount(Request $request)
    {
        try{
            return $messageCount = MessageServices::messageCount($request);
        } catch(Exception $e) {
            return $this->errorResponse($e);
        }
    }
    /** Count for Un-read Messages */

    // Send sms to to use mobile

    public function sendSms(Request $request)
    {   
        
        ApiV2\Services\SendSmsService::sendSms($model,2);
        return response()->json(Sms::getMobleNumber(array(1054,1037)));
        return Sms::smsApiRequest($sms_params);
    }


    public static function seller_buyer_serviceid($userid,$role_id){
        if($role_id==2){
            ### for seller ####
        $buyerquoteid = DB::table('seller_services')->select(
        array( DB::raw('GROUP_CONCAT(DISTINCT lkp_service_id) as   Serviceid'))
            )->where('user_id','=', $userid)->where('is_service_offered','=','1')->get();
        

        }
        else{
        $count_n = DB::table('seller_services')->select('id')->where('user_id','=',$userid)->where('is_service_required','=','1')->get();
                            if (count($count_n) ==0){
            ##### for buyer #####                    
        $buyerquoteid = DB::table('lkp_services')->select(
           // 'id as Serviceid'
        array(      
                DB::raw('GROUP_CONCAT(DISTINCT id) as   Serviceid' ) )
            )->where('is_active','=','1')->get();
                        }
                        else{ 
             ###### if buyer want specific service ######               
        $buyerquoteid = DB::table('seller_services')->select(
        array(      
                DB::raw('GROUP_CONCAT(DISTINCT lkp_service_id) as   Serviceid'
                )
            )
            )->where('is_service_required','=','1')->where(
         'user_id','=',$userid)->get();
        }
        }

       
return ($buyerquoteid);
    }



    /** Header Notifications counts for all services */
    public static function postMasterCounts($userid, $role_id)
    {
        $services = MessageController::seller_buyer_serviceid($userid,$role_id);
        $serv_is = $services[0]->Serviceid;
        $s_ids = explode(",", $serv_is);


        $services = DB::table('lkp_services')->select('*')->where('is_active','=','1')
                                            ->whereIn('id',$s_ids)->get();
                                            
        $menu_list=[];
        foreach ($services as $key => $value) {
            $bu = DB::table('lkp_service_urls')->select('*')->where('serviceId','=',$value->id)
                                                            ->where('usertype','=',$role_id)
                                                            ->where('type','=','postmaster')
                                                            ->get();                           
            $menu_list[$key]['serviceId']=$value->id;
            $menu_list[$key]['name']=$value->service_name;
            $menu_list[$key]['imagePath']=$value->service_image_path; 
            if( count( $bu )>0 ) {
                $menu_list[$key]['url']=$bu[0]->url;
            }
            else {
               $menu_list[$key]['url']=""; 
            }

            $counts = DB::select( DB::raw("SELECT SUM( post_leads ) AS leads_count, SUM( post_enquiries ) AS enquiries_count,
            SUM( post_offers ) AS offers_count, SUM( post_messages ) 
            AS msg_count, SUM( documents ) AS doc_counts,
            service, type FROM  `user_notifications` WHERE TYPE = '$role_id' and service='$value->id' and created_by='$userid'") );
            

    $menu_list[$key]['msg_count']=$counts[0]->msg_count ==0? '' :$counts[0]->msg_count;
    $menu_list[$key]['doc_count']=$counts[0]->doc_counts==0? '' :$counts[0]->doc_counts;
    $menu_list[$key]['enquiries_quotes']=$counts[0]->enquiries_count ==0 ? '' :$counts[0]->enquiries_count;
    $menu_list[$key]['leads']=$counts[0]->leads_count==0 ? '' : $counts[0]->leads_count ;

              


        }
        
        return $menu_list;    

    }


    /** Header Notifications counts for order in all services */
    public static function orderMasterCounts($userid, $role_id)
    {
        $services = MessageController::seller_buyer_serviceid($userid,$role_id);
        $serv_is = $services[0]->Serviceid;
        $s_ids = explode(",", $serv_is);


        $services = DB::table('lkp_services')->select('*')->where('is_active','=','1')
                                            ->whereIn('id',$s_ids)->get();
                                            
        $menu_list=[];
        foreach ($services as $key => $value) {
            $bu = DB::table('lkp_service_urls')->select('*')->where('serviceId','=',$value->id)
                                                            ->where('usertype','=',$role_id)
                                                            ->where('type','=','order')
                                                            ->get();                           
            $menu_list[$key]['serviceId']=$value->id;
            $menu_list[$key]['name']=$value->service_name;
            $menu_list[$key]['imagePath']=$value->service_image_path; 
            if( count( $bu )>0 ) {
                $menu_list[$key]['url']=$bu[0]->url;
            }
            else {
               $menu_list[$key]['url']=""; 
            }

            $counts = DB::select( DB::raw("SELECT SUM( order_messages ) AS msg_count, SUM( order_docs ) AS docs_count, service, type FROM  `user_notifications` WHERE TYPE = '$role_id' and service='$value->id' and created_by='$userid'") );
            
            $menu_list[$key]['msgc']=$counts[0]->msg_count;
            $menu_list[$key]['docs']=$counts[0]->docs_count;


        }
        
        return $menu_list;    

    }

    public function getOrderMessages(Request $request){

        try {
            return MessageServices::getOrderMessages($request);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
        

    }


    // Test API Visit User

    public function userView(Request $request){
        try {
           
            Log::info('API Test');
            
            $userView = new UserViewService($request);
            $userView = $userView->userVisitAction();

            return $userView;
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
        

    }


    /**For All Counts Post and Order */
    public function totalCounts(Request $request)
    {
        $serviceId= $request->s;
        // return $serviceId;
        $userActiveRole = $request->r;
        $role = $request->r;
      
        if($userActiveRole == 'Seller') {
            $role = 2;
        } else if($userActiveRole == 'Buyer') {
            $role = 1;
        }
        
        $postCount = DB::table('user_notifications')->select('*')
                    ->where('post_status', 1)
                    ->where('post_id', '!=', NULL)
                    ->where('type', 1)
                    ->where('role', $role)
                    ->where('service', $serviceId)
                    ->count();

        $orderCount = DB::table('user_notifications')
                    ->where('post_id','!=',NULL)
                    ->where('type','=',3)
                    ->where('role','=',$role)
                    ->where('order_status','!=',0)
                    ->where('order_id', '!=', 0)
                    ->where(function($query) use($request){
                      if(isset($request->data) && !empty($request->data)){
                        $query->where('service', '=',$request->data);
                      }
                    })
                    ->count();
                    
                    
        return response()->json([
            "payload" => array(
                "count" => array(
                    "postCount"=>$postCount,
                    "orderCount"=>$orderCount
                )
            )
        ]);

    }

}
