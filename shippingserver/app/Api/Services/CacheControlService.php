<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 4/3/17
 * Time: 4:10 PM
 */

namespace Api\Services;


use Api\Utils\DateUtils;
use DB;
use Log;

class CacheControlService implements ICacheControlService
{
    const SELLER_INBOUND_LEADS_ENQUIRIES = "seller_inbound_leads_enquiries";

    const BUYER_INBOUND_LEADS_OFFERS = "buyer_inbound_leads_offers";

    /**
     * @param $user_id
     * @param $cache_key
     * @return mixed
     */
    public static function markCached($user_id, $cache_key)
    {
        LOG::info("Marking Cache create for user [" . $user_id . "] and cache key [" . $cache_key . "]");

        $unixNow = DateUtils::unixNow();
        $oneDayLater = $unixNow + (24 * 60 * 60);

        $attributes = ['user_id' => $user_id, 'cache_key' => $cache_key];
        $values = ['cached_at' => $unixNow, 'expiry_at' => $oneDayLater];
        DB::table('shp_cache_control')->updateOrInsert($attributes, $values);

    }

    public static function markExpired($user_id, $cache_key)
    {

        LOG::info("Expiring Cache for user [" . $user_id . "] and cache key [" . $cache_key . "]");

        $unixNow = DateUtils::unixNow();

        DB::table('shp_cache_control')->where('cache_key', $cache_key)->update(["expiry_at" => $unixNow]);

    }


    public static function isExpired($user_id, $cache_key)
    {
        $unixNow = DateUtils::unixNow();

        $row = DB::table('shp_cache_control')->where('user_id', $user_id)->where('cache_key', $cache_key)->first();

        LOG::info((array)$row);

        if (!empty($row)) {

            if ($row->expiry_at < $unixNow) {

                //Ths is expired. Refresh the cache.
                return true;
            }

        } else {

            //There is no cache for this key. Let us create a one day old-cache and also return as expired, since the cache can now buld up.

            $oneDayBefore = $unixNow - (24 * 60 * 60);

            $attributes = ['user_id' => $user_id, 'cache_key' => $cache_key];
            $values = ['cached_at' => $unixNow, 'expiry_at' => $oneDayBefore];

            DB::table('shp_cache_control')->updateOrInsert($attributes, $values);

            return true;

        }

        //Cache is built and is not expired.
        return false;

    }

}