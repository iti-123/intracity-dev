<?php

namespace Api\Utils;

use App\ActivityLog;
use Auth;
use DB;
use Log;

class CommonComponents
{

    /** Save data into activity tables ****/
    public static function activityLog($event, $event_description, $ref_id, $ref_url, $action_url)
    {
        $createdAt = date('Y-m-d H:i:s');
        if ($ref_id != 0) {
            $createdBy = Auth::User()->id;
        } else {
            $createdBy = null;
        }
        $createdIp = $_SERVER['REMOTE_ADDR'];

        $activityLog = new ActivityLog;
        $activityLog->activity_event = $event;
        $activityLog->event_description = $event_description;
        $activityLog->ref_id = $createdBy;
        $activityLog->referrer_url = $ref_url;
        $activityLog->action_page_url = $action_url;
        $activityLog->created_at = $createdAt;
        $activityLog->created_by = $createdBy;
        $activityLog->created_ip = $createdIp;
        $activityLog->updated_at = $createdAt;
        $activityLog->updated_by = $createdBy;
        $activityLog->updated_ip = $createdIp;
        $activityLog->save();
    }

    /** Save data into audit log tables **/
    public static function auditLog($id, $table)
    {
        $qry = "insert into log_" . $table . " (select * from " . $table . " where id ='$id')";
        DB::insert($qry);
    }

    /*
 * Get all service Ids
 */
    public static function getMessageID()
    {
        try {
            $post = DB::table('user_messages')->select('id')->orderBy('id', 'desc')->first();
            if (!empty($post))
                return $post->id + 1;
            else
                return 1;
        } catch (\Exception $e) {
            //return $e->message;
        }
    }

    public static function getServiceIds($userid)
    {
        $sIds = array();
        $result = DB::table('user_subscription_services as uss')
            ->where('uss.user_id', $userid)
            ->where('uss.service_payment_status', 1)
            //         ->where('uss.subscription_enddate', '<', NOW())
            ->select('uss.lkp_service_id')
            ->get();
        Log::info("getServiceIds for the logged in user =>" . $userid);
        if (!empty($result)) {

            foreach ($result as $service_id) {
                $sIds[] = $service_id->lkp_service_id;
            }
            Log::info("user_subscription Service Ids :" . implode(",", $sIds));
            return implode(",", $sIds);
        }
        return '';
    }


    //code added by govind

    /**
     * Get SellerSelectedServices for dropdown
     */
    public static function getSellerSelectedServices()
    {
        try {

            $allservices = [];
            $allservices[0] = 'Services (ALL)';
            $roleId = Auth::User()->lkp_role_id;
            $allservice = DB::table('seller_services')->join('lkp_services', 'seller_services.lkp_service_id', '=', 'lkp_services.id');
            $allservice->where(['user_id' => Auth::User()->id, 'is_service_offered' => 1]);
            $allservice->orderBy('service_name', 'asc');
            $allservice->where('is_active', '1');
            $allservice = $allservice->select('lkp_services.service_name', 'lkp_services.id')->get();
            foreach ($allservice as $service) {
                $allservices[$service->id] = $service->service_name;
            }
            return $allservices;
        } catch (Exception $ex) {

        }
    }


    /**
     * Get Buyer requested  services for dropdown
     */
    public static function getBuyerReqServices()
    {
        try {
            $allservicess = [];

            // $roleId = Auth::User()->lkp_role_id;
            $userId = Auth::User()->id;
            $allservices = DB::table('seller_services as ss')
                ->join('lkp_services as ls', 'ls.id', '=', 'ss.lkp_service_id')
                ->where('ss.user_id', $userId)
                ->where('ss.is_service_required', '1')
                ->where('ls.is_active', '1')
                ->lists('ls.service_name', 'ss.lkp_service_id');

            return $allservices;
        } catch (Exception $ex) {

        }
    }


    /**
     * Get all services for dropdown
     */
    public static function getAllServices()
    {
        try {
            //         CommonComponent::activityLog("GET_STATE", GET_STATE, 0, HTTP_REFERRER, CURRENT_URL);
            $allservices = [];
            $allservices[0] = 'Services (ALL)';
            $roleId = Auth::User()->lkp_role_id;
            $allservice = DB::table('lkp_services');
            $allservice->orderBy('service_name', 'asc');
            $allservice->where('is_active', '1');
            $allservice = $allservice->lists('service_name', 'id');
            foreach ($allservice as $id => $servicename) {
                $allservices[$id] = $servicename;
            }
            return $allservices;
        } catch (Exception $ex) {

        }
    }
}