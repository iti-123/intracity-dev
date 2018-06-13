<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 3/25/17
 * Time: 5:54 PM
 */

namespace ApiV2\Utils;

use App\Exceptions\ApplicationException;
use Carbon\Carbon;
use Log;

class DateUtils
{

    const STD_DATEFORMAT = 'Ymd';

    const STD_DATETIMEFORMAT = 'Ymd-His';

    const DAY_END = '235959';

    public static function parseFomattedDateTime($format, $str, $timezone)
    {
        LOG::debug("Parse DateTime [" . $str . "] in format [" . $format . "] in TZ [" . $timezone . "]");

        $dateTime = \DateTime::createFromFormat($format, $str, new \DateTimeZone($timezone));

        if (!$dateTime) {
            throw new ApplicationException(["datestring" => $str, "timezone" => $timezone], [0 => "Invalid Date String Specified"]);
        }

        return $dateTime->getTimestamp();
    }

    /**
     * Returns the current Unix timestamp
     */
    public static function unixNow()
    {
        return (new \DateTime())->getTimestamp();
    }

    /**
     * Returns the current Unix timestamp
     */
    public static function getYesterdayDate()
    {
        return (new \DateTime("1 day ago"))->getTimestamp();
    }

    /**
     * Returns the current Unix timestamp in Milliseconds
     * For more information please see
     * http://stackoverflow.com/questions/3656713/how-to-get-current-time-in-milliseconds-in-php
     */
    public static function unixNowMillis()
    {
        $milliseconds = round(microtime(true) * 1000);
    }

    /**
     * Returns the current Unix timestamp
     */
    public static function diffInDays($date1, $date2)
    {
        $str = strtotime($date1) - (strtotime($date2));
        return (floor($str / 3600 / 24));
    }

    /**
     * Returns the current Unix timestamp
     */

    public static function diffInDays1($subscrDate, $current)
    {

        $end = Carbon::parse($subscrDate);
        return $end->diffInDays($current);


    }

    /**
     * Is this a valid date?
     * @param $str
     * @param $timezone
     * @return bool
     */
    public function isValidDateString($str, $timezone)
    {

        try {

            $val = self::parseDate($str, $timezone);

            if ($val == false) {
                return false;
            }

            return true;

        } catch (\Exception $e) {

            return false;

        }

    }

    /**
     * Returns a Unix timestamp from a standard Date string Ymd. Assumes end time of the day as DAY_END
     * @param $str
     * @param $timezone
     * @return int
     */
    public static function parseDate($str, $timezone)
    {

        LOG::info("Parse Date [" . $str . "] in TZ [" . $timezone . "]");

        $dateTimeStr = $str . '-' . DateUtils::DAY_END;

        $dateTime = \DateTime::createFromFormat(DateUtils::STD_DATETIMEFORMAT, $str . '-' . DateUtils::DAY_END, new \DateTimeZone($timezone));

        if (!$dateTime) {
            throw new ApplicationException(["datestring" => $str, "timezone" => $timezone], [0 => "Invalid Date String Specified"]);
        }

        return $dateTime->getTimestamp();
    }

    /**
     * Is this a valid datetime ?
     * @param $str
     * @param $timezone
     * @return bool
     */
    public function isValidDateTimeString($str, $timezone)
    {

        try {

            $val = self::parseDateTime($str, $timezone);

            if ($val == false) {
                return false;
            }

            return true;

        } catch (\Exception $e) {

            return false;

        }

    }

    /**
     * Returns a Unix timestamp from a standard Date string Ymd-His. Assumes end time of the day as DAY_END
     * @param $str
     * @param $timezone
     * @return int
     */
    public static function parseDateTime($str, $timezone)
    {
        LOG::debug("Parse DateTime [" . $str . "] in TZ [" . $timezone . "]");

        $dateTime = \DateTime::createFromFormat(DateUtils::STD_DATETIMEFORMAT, $str, new \DateTimeZone($timezone));

        if (!$dateTime) {
            throw new ApplicationException(["datestring" => $str, "timezone" => $timezone], [0 => "Invalid Date String Specified"]);
        }

        return $dateTime->getTimestamp();
    }

}