<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 2/15/2017
 * Time: 11:59 AM
 */

namespace ApiV2\Services;


interface ISendSmsService
{
    public static function shpSendSMS($phone, $smsEventId, $params, $userID);

    public static function smsApiRequest($params);

    public static function getMobleNumber($user_id);

    /**
     * @param $user_ids
     * @return mixed
     */
    public static function getBuyerMobileNumbers($user_ids);

    /**
     *
     * @param $user_ids
     * @return mixed
     */
    public static function getSellerMobileNumbers($user_ids);

}