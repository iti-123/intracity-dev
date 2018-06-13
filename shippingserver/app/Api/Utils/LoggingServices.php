<?php

namespace Api\Utils;

use App\ActivityLog;
use App\ShippingLogs;
use Auth;
use DB;
use Log;

class LoggingServices
{

    /** Save data into activity_log tables ****/
    public static function activityLog($event, $event_description, $ref_id, $ref_url, $action_url, $service_id = '')
    {
        $createdAt = date('Y-m-d H:i:s');
        $createdBy = '';
        if ($ref_id != 0) {
            $createdBy = Auth::User()->id;
        }
        $createdIp = $_SERVER['REMOTE_ADDR'];

        $activityLog = new ActivityLog;

        $activityLog->lkp_service_id = $service_id;
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

    /** Save data into shp_audit_log tables **/
    public static function auditLog($entityId, $entity, $postData)
    {
        Log::info($postData);
        $now = date('Y-m-d H:i:s');

        $shippingLogs = new ShippingLogs;

        $shippingLogs->entity_id = $entityId;
        $shippingLogs->entity = $entity;
        $shippingLogs->post_data = $postData;
        $shippingLogs->created_by = 1;
        $shippingLogs->created_ip = $_SERVER['REMOTE_ADDR'];
        $shippingLogs->created_at = $now;
        $shippingLogs->updated_at = $now;
        $shippingLogs->save();
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