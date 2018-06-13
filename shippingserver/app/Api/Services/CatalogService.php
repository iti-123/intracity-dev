<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 2/6/17
 * Time: 4:25 PM
 */

namespace Api\Services;


use Api\Requests\Service;
use Api\Requests\ServiceCrumb;
use Api\Requests\ServiceGroup;
use Api\Requests\UserMenuBO;
use App\Exceptions\UnauthorizedException;
use DB;
use Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class CatalogService extends BaseService implements ICatalogService
{

    public function getServiceIds($userid)
    {
        $sIds = array();
        $result = DB::table('user_subscription_services as uss')
            ->where('uss.user_id', $userid)
            ->where('uss.service_payment_status', 1)
            //         ->where('uss.subscription_enddate', '<', NOW())
            ->select('uss.lkp_service_id')
            ->get();
        Log::info("retreiving Service Ids for USer=>" . $userid);
        if (!empty($result)) {

            foreach ($result as $service_id) {
                $sIds[] = $service_id->lkp_service_id;
            }
            Log::info("USER subscribed to servcies Ids :" . implode(",", $sIds));
            return implode(",", $sIds);
        }
        return '';
    }


    public function getServiceCatalog($userId)
    {
        //$userRole= JWTAuth::parseToken()->getPayload()->get('role');

        //Get the principal
        //$principal = $this->getPrincipal();

        //Get the current user
        //$userId =  $principal->getUserId();

        //Load the service catalog

        //TODO: This query has to be improved considering current subscriptions etc..
        //TODO: Use Bind parameters instead of raw $userId


        $currentUserId = JWTAuth::parseToken()->getPayload()->get('id');

        if ($currentUserId != $userId) {
            throw new UnauthorizedException("User " . $currentUserId . " not allowed to see menu for user " . $userId);
        }


        $results = DB::select(DB::raw("SELECT  s.id as service_id , s.service_name as service_name, s.lkp_invoice_service_group_id as  invoiceServiceGroupId,s.group_name as group_name,s.service_image_path as service_image_path, s.service_crumb_name,u.service_payment_status as opted
     FROM lkp_services s
     LEFT JOIN user_subscription_services as u ON u.lkp_service_id = s.id
     LEFT JOIN users as users ON users.id = u.user_id

     WHERE s.is_active = 1 AND  s.service_crumb_name IN  ('Transportation')

     ORDER BY s.id"));


        $prevCrumb = "";
        $prevGroup = "";

        $counter = 0;

        $menu = new UserMenuBO();

        //$menu->userRole = $userRole;

        //current values
        $crumb = array();
        $group = array();

        //Iterate all services and generate a catalog model

        foreach ($results as $result) {

            if ($counter == 0) {

                $prevCrumb = $result->service_crumb_name;
                $prevGroup = $result->group_name;

                $crumb = new ServiceCrumb();
                $crumb->name = $result->service_crumb_name;


                $group = new ServiceGroup();
                $group->name = $result->group_name;

                array_push($crumb->groups, $group);

            }


            if ($prevCrumb != $result->service_crumb_name) {

                array_push($menu->crumbs, $crumb);

                $crumb = new ServiceCrumb();
                $crumb->name = $result->service_crumb_name;

                $group = new ServiceGroup();
                $group->name = $result->group_name;
            }

            if ($prevGroup != $result->group_name) {

                $group = new ServiceGroup();
                $group->name = $result->group_name;
                array_push($crumb->groups, $group);
            }

            $crumb->invoiceServiceGroupId = $result->invoiceServiceGroupId;
            $svc = new Service();
            $svc->serviceId = $result->service_id;
            $svc->serviceName = $result->service_name;
            $svc->fullName = $result->service_name;
            $svc->imagePath = $result->service_image_path;
            // $svc->opted = ( $result->opted == 1 ? true : false);

            LOG::debug("Processing " . $result->service_crumb_name . "/" . $result->group_name . "/" . $result->service_name);

            array_push($group->services, $svc);

            $counter = $counter + 1;

            $prevCrumb = $result->service_crumb_name;
            $prevGroup = $result->group_name;

            LOG::debug("Previous counter/crumb/group => " . $counter . " = " . $prevCrumb . " / " . $prevGroup);
        }

        // array_push($crumb->groups, $group);
        array_push($menu->crumbs, $crumb);

        return $menu;


    }

}