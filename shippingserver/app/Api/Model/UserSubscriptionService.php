<?php
/**
 * Created by PhpStorm.
 * User: Karunya
 * Date: 04/16/17
 * Time: 7:34 PM
 */

namespace Api\Model;

use Illuminate\Database\Eloquent\Model;
use Log;

class UserSubscriptionService extends Model
{

    public $timestamps = false;
    protected $table = "user_subscription_services";

    public function getSubscriptionEndDate($serviceId, $userId)
    {

        //   = DB::table('user_subscription_services')
        $userSubscription = $this->where('lkp_service_id', $serviceId)->where('user_id', $userId)->get()->first();
        LOG::debug('getSubscriptionEndDate service Id=> ' . $serviceId . 'User Id :' . $userId);
        LOG::debug($userSubscription);
        if (count($userSubscription) > 0)
            return $userSubscription->subscription_enddate;
        else
            return null;
    }
}