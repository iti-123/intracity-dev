<?php
/**
 * Created by PhpStorm.
 * User: chetan
 * Date: 14/2/17
 * Time: 1:12 PM
 */

namespace ApiV2\Services;

use ApiV2\Utils\LoggingServices;
use App\LogUserSms;
use App\User;
use DB;
use Log;
use Session;
use ApiV2\Services\EmailService;
use Tymon\JWTAuth\Facades\JWTAuth;

class SendSmsService implements ISendSmsService
{
    public static function shpSendSMS($phone = array(), $smsEventId, $params = array(), $userID)
    {

        if (SMS_GATEWAY_ENABLED == 0) {
            return;
        }
        // $userID = JWTAuth::parseToken()->getPayload()->get('id');
        $error = array();

        if (empty($phone))
            $error[] = "Phone Number Required";

        if (empty($smsEventId))
            $error[] = "SMS Event ID Required";

        if ($error) {
            return $error;
        } else {

            $mobilenos = implode(',', $phone);

            $sender_id = (isset($userID)) ? $userID : 0;
            $created_at = date('Y-m-d H:i:s');
            $createdIp = $_SERVER['REMOTE_ADDR'];

            //*** Getting SMS Template Body ***//
            $msg_template = DB::table('lkp_sms_templates')
                ->where(['lkp_sms_event_id' => $smsEventId])
                ->select('lkp_sms_templates.*')
                ->get();

            $msg_template_id = $msg_template[0]->id;

            $body = $msg_template[0]->body;

            // Replacing params in templage
            if ($params && is_array($params)) {
                foreach ($params as $key => $value) {
                    $body = str_replace("{!! $key !!}", $value, $body);
                }
            }

            $site_url = url('/');

            //replace site url for image paths.
            $body = str_replace("{!! site_url !!}", $site_url, $body);

            $serviceId = Session::get('service_id');
            switch ($serviceId) {
                case FCL :
                    $body = str_replace("FTL", 'FCL', $body);
                    break;
                default :
                    $body = str_replace("FTL", 'FCL', $body);
                    break;
            }

            // Send SMS Params
            $sms_params = array(
                'mobilenos' => $mobilenos,
                'body' => $body,
                'requestType' => 'bulk',
            );

            // Send SMS request
            $curlresponse = SendSmsService::smsApiRequest($sms_params);
           // $job_id = str_replace('OK:', '', $curlresponse);

            // Save in SMS LOG
            foreach ($phone as $key => $value) {
                $saveSmsLog = new LogUserSms;
                $saveSmsLog->lkp_sms_template_id = $msg_template_id;
                $saveSmsLog->sender_user_id = $sender_id;
               // $saveSmsLog->job_id = $job_id;
                $saveSmsLog->mobile_no = $value;
                $saveSmsLog->template_message = '';
                $saveSmsLog->converted_message = $body;
                $saveSmsLog->is_sent = 0;
                $saveSmsLog->created_at = $created_at;
                $saveSmsLog->updated_at = $created_at;
                $saveSmsLog->created_by = $sender_id;
                $saveSmsLog->updated_by = $sender_id;
                $saveSmsLog->created_ip = $createdIp;
                $saveSmsLog->updated_ip = $createdIp;
                $saveSmsLog->message_status = 0;
                $saveSmsLog->save();
                // LoggingServices::auditLog($saveSmsLog->id, SMS_SEND_TO_USER, json_encode((array)$saveSmsLog));
            }
        }
    }

    public static function smsApiRequest($params = array())
    {
        //Please Enter Your Details

        $user = "logistiks"; //your username
        $password = '$Marketplacelogi';  //your password

        if ($params['requestType'] == 'report') {
            $fromdate = $params['from'];
            $todate = $params['to'];
            $url = "http://api.smscountry.com/smscwebservices_bulk_reports.aspx?";
            $postFields = "User=$user&passwd=$password&fromdate=$fromdate&todate=$todate";
        } else {
            $mobilenumbers = $params['mobilenos']; //enter Mobile numbers comma seperated
            $message = $params['body']; //enter Your Message
            $senderid = "Logistiks"; //Your senderid
            $messagetype = "N"; //Type Of Your Message
            $DReports = "Y"; //Delivery Reports
            $message = urlencode($message);

            $url = "http://www.smscountry.com/SMSCwebservice_Bulk.aspx";
            $postFields = "User=$user&passwd=$password&mobilenumber=$mobilenumbers&message=$message&sid=$senderid&mtype=$messagetype&DR=$DReports";
        }
        $ch = curl_init();
        if (!$ch) {
            die("Couldn't initialize a cURL handle");
        }
        $ret = curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        $ret = curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $curlresponse = curl_exec($ch); // execute

        if (curl_errno($ch) || empty($ret)) {
            // some kind of an error happened
            return curl_error($ch);
            curl_close($ch); // close cURL handler
        } else {
            $info = curl_getinfo($ch);
            curl_close($ch); // close cURL handler
            return $curlresponse; //echo "Message Sent Succesfully" ;
        }
    }


    public static function getBuyerMobileNumbers($user_ids = array())
    {
        $mobileNumbers = [];
        $where = null;
        if (sizeof($user_ids) > 0) {
            //A beautiful way to solve SQL injection problems with IN Clasue
            $inClause = trim(str_repeat('?,', count($user_ids)), ',');


            // To fetch buyer mobile number
            $baseDataQuery = "
            select (case when u.is_business = 1 then bb.contact_mobile when u.is_business = 0 then b.mobile end) mobile_number
            from users u
            left join buyer_details b on (u.id = b.user_id)
            left join buyer_business_details bb on (u.id = bb.user_id)";

            $where = "where u.id  in (" . $inClause . ")";

            $dataQuery = $baseDataQuery . $where;

            $rows = DB::select($dataQuery, $user_ids);

            if (isset($rows) && sizeof($rows) > 0) {
                foreach ($rows as $row) {
                    if (isset($row->mobile_number) && (!is_null($row->mobile_number))) {
                        array_push($mobileNumbers, $row->mobile_number);
                    }
                }
            }
        }
        // Log::debug("MobileNumber Array => ");
        // Log::debug($mobileNumbers);
        return $mobileNumbers;
    }


    public static function getSellerMobileNumbers($user_ids = array())
    {
        $mobileNumbers = [];
        $where = null;
        if (sizeof($user_ids) > 0) {
            DB::enableQueryLog();
            //A beautiful way to solve SQL injection problems with IN Clasue
            $inClause = trim(str_repeat('?,', count($user_ids)), ',');

            // To fetch buyer mobile number
            $baseDataQuery = "select u.id, (case when u.is_business = 1 then bb.contact_mobile when u.is_business = 0 then b.mobile end) mobile_number from users u
              left join buyer_details b on (u.id = b.user_id) left join buyer_business_details bb on (u.id = bb.user_id) ";

            $where = " where u.id  in (" . $inClause . ")";
            $dataQuery = $baseDataQuery . $where;

            $rows = DB::select($dataQuery, $user_ids);

            if (isset($rows) && sizeof($rows) > 0) {
                foreach ($rows as $row) {
                    if (isset($row->mobile_number) && (!is_null($row->mobile_number))) {
                        array_push($mobileNumbers, $row->mobile_number);
                    }
                }
            }
            Log::debug("MobileNumber Array => ");
            Log::debug($mobileNumbers);
        }
        return $mobileNumbers;

    }

    public static function getMobleNumber($user_ids = array())
    {
        $data = array();
        try {
            $users = User::find($user_ids);
            foreach ($users as $user) {
                $user_id = $user->id;
                $role_id = $user->lkp_role_id;
                $user_phone = DB::table('users');
                $user_phone_number = $user_phone->where('users.id', $user_id);
                if ($role_id == SELLER) {
                    $user_phone_number->leftJoin('seller_details as c2', function ($join) {
                        $join->on('users.id', '=', 'c2.user_id');
                        $join->on(DB::raw('users.is_business'), '=', DB::raw(0));
                    });
                    $user_phone_number->leftJoin('seller_details as cc2', function ($join) {
                        $join->on('users.id', '=', 'cc2.user_id');
                        $join->on(DB::raw('users.is_business'), '=', DB::raw(1));
                    });
                } else if ($role_id == BUYER) {
                    $user_phone_number->leftJoin('buyer_details as c2', function ($join) {
                        $join->on('users.id', '=', 'c2.user_id');
                        $join->on(DB::raw('users.is_business'), '=', DB::raw(0));
                    });
                    $user_phone_number->leftJoin('buyer_business_details as cc2', function ($join) {
                        $join->on('users.id', '=', 'cc2.user_id');
                        $join->on(DB::raw('users.is_business'), '=', DB::raw(1));
                    });
                }
                if ($role_id == SELLER) {
                    $user_phone_number->select(DB::raw("(case when users.is_business = 1 then cc2.contact_mobile when users.is_business = 0 then c2.contact_mobile end) as 'phone'"));
                } else if ($role_id == BUYER) {
                    $user_phone_number->select(DB::raw("(case when users.is_business = 1 then cc2.contact_mobile when users.is_business = 0 then c2.mobile end) as 'phone'"));
                }
                $data[] = $user_phone_number->first()->phone;
            }
            
            Log::info("Data Phone Number Array => ");
            Log::info($data);

            $smsArray = [];
            foreach ($data as $key => $value) {
                Log::info($value);
                if (!empty($value) || $value != NULL || $value != "") {
                    //Do not consider NULL values
                    Log::info("Inside  ");
                    break;
                } else {
                    array_push($smsArray, $value);
                }
            }

            Log::info("After removing NULL Refining Data Phone Number Array => ");
            Log::info($smsArray);
            return $smsArray;
            if (isset($smsArray) && sizeof($smsArray) > 0)
                return $smsArray;
            else
                return false;

        } catch (Exception $exc) {

        }
    }


    public static function sendSms($model,$event,$params)
    {
        // Send notification to all assigned buyer and seller for private post
        $tempUser = explode(",",$params['users']);
        array_push($tempUser,$model->createdBy);
        
        // $getAllUsers = array_merge(explode(",",$params['users']),$postCreatedByUsers);

        $phoneNumber = static::getMobileNumber(User::whereIn("id", array_unique($tempUser))->get(["phone"]));
        Log::info('$tempUser'. json_encode(array_unique($tempUser)));
        Log::info('phoneNumber'. json_encode($phoneNumber));
        
        $emailInfo = $params;
        // Send Email Notification 
        Log::info('Send email alert');
        // EmailService::sendMailTo($tempUser, $emailInfo, $event);       
        Log::info('Send SMS alert');
        return static::shpSendSMS($phoneNumber,$event,$params,$model->createdBy);        
    
    }

    public static function getMobileNumber($phoneNumber) {
        $phone = array();
        foreach ($phoneNumber as $key => $value) {
            array_push($phone,$value->phone);
        }
        return $phone;
    }

}