<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 4/5/17
 * Time: 8:34 PM
 */

namespace Api\Services;

use DB;
use Api\Model\Settings;
use Log;

class UserSettingsService implements IUserSettingsService
{
    /**
     * Gets all settings per user.
     * @param $context
     * @param $userId
     * @return mixed
     */
    public static function getUserSettings($serviceId, $context, $userId)
    {
        $config = [];
         //DB::enableQueryLog();
        $settings = Settings::where('user_id', $userId)
            ->where('service_id', $serviceId)
            // ->where('context', $context)
            ->get(['settings']);
          //dd(DB::getQueryLog());
         if (empty($settings) || count($settings) <= 0) {
            $settings = self::getDefault($serviceId, $context);
        }

        foreach ($settings as $setting) {
            $config = unserialize($setting->settings);
        }
           
        return $config;

    }

    /**
     * Get default settings for this context
     * @param $context
     * @return mixed
     */
    public static function getDefault($serviceId, $context)
    {
        return Settings::where('user_id', 0)
            // ->where('context', $context)
            ->where('service_id', $serviceId)
            ->get(['settings']);

    }

    /**
     * Store per user settings
     * @param SettingsBO $bo
     * @return mixed
     */
    public static function storeUserSettings($serviceId, $context, $userId, array $settings = [])
    {

        LOG::info($settings);
        //Delete all currently stored user settings

        Settings::where('user_id', $userId)
            ->where('service_id', $serviceId)
            ->where('context', $context)
            ->delete();

        //Insert all new settinfs

        foreach ($settings as $key => $value) {
            $key = str_replace('_', '.', $key);
            $setting = new Settings();
            $setting->user_id = $userId;
            $setting->service_id = $serviceId;
            $setting->context = $context;
            $setting->settings = $key;
            $setting->value = $value;

            $setting->save();

        }

    }

}