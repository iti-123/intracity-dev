<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 4/5/17
 * Time: 8:24 PM
 */

namespace ApiV2\Services;


use ApiV2\BusinessObjects\SettingsBO;

interface IUserSettingsService
{

    /**
     * Get default settings for this context
     * @param $context
     * @return mixed
     */
    public static function getDefault($serviceId, $context);

    /**
     * Gets all settings per user.
     * @param $context
     * @param $userId
     * @return mixed
     */
    public static function getUserSettings($serviceId, $context, $userId);


    /**
     * Store per user settings
     * @param $settings associateive array
     * @return mixed
     */
    public static function storeUserSettings($serviceId, $context, $userId, array $settings = []);

}