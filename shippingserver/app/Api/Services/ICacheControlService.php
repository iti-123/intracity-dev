<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 4/3/17
 * Time: 4:08 PM
 */

namespace Api\Services;

/**
 * Interface ICacheControlService Provides database oriented cache control.
 * It does not cache the data, it only controls the cache lifetimes.
 * @package Api\Services
 */
interface ICacheControlService
{

    /**
     * @param $user_id
     * @param $cache_key
     * @return mixed
     */
    public static function markCached($user_id, $cache_key);

    public static function markExpired($user_id, $cache_key);

}