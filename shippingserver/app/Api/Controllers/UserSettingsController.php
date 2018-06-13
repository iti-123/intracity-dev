<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 4/5/17
 * Time: 8:33 PM
 */

namespace Api\Controllers;


use Api\Requests\BaseShippingResponse as ShipRes;
use Api\Services\UserSettingsService;
use Illuminate\Http\Request;
use Log;
use Tymon\JWTAuth\Facades\JWTAuth;


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


}