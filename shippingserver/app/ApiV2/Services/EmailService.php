<?php
/**
 * Created by PhpStorm.
 * User: 10528
 * Date: 2/3/2017
 * Time: 11:50 AM
 */

namespace ApiV2\Services;

use App\EmailNotification;
use App\User;
use Illuminate\Support\Facades\Mail;

class EmailService
{

    public $email;
    public $subject;
    public $body;
    public $site_url;
    public $email_template;
    public $emailInfo;


    /********
     *
     * Send Mail to specific
     */
    public static function sendMailTo($to, $emailInfo, $event_id = null)
    {

        $email = array();
        $users = User::find($to);
        $info = array();
        $emailPacket = [];

        //Get the email template
        $email_template = EmailNotification::get_email_template($event_id);
        $subject = $email_template->subject;
        $templateBody = $email_template->body;


        /**
         * Build the Recepeint's list while getting their emails and data needed to populate the template
         *
         */
        foreach ($users as $user) {
            $email['email'] = $user->email; //SendTo address,can be multiple
            $info['username'] = $user->username;// To  address the recepient in the E-mail
            $emailInfoR = array_merge($emailInfo, $info, $email);

            array_push($emailPacket, $emailInfoR);
        }
        // dd($emailPacket);
        $bo = [];

        foreach ($emailPacket as $key => $value) {

            $body = $templateBody;
            foreach ($value as $k => $val) {
                $replace = "{!! " . $k . " !!}";
                $body = str_replace($replace, $val, $body);
            }

            // dd($value['email']);
            EmailService::prepareEmail($value['email'], $subject, $body);
        }

        return true;
    }


    /*
	* Send Email
	* Sends emails as per the configuration set based on the parameters
	*/


    public static function prepareEmail($email, $subject = null, $body = null, $info = null, $remdays = null, $pathToFile = null, $attachmentFlag = false)
    {

        $site_url = url();

        //replace site url for image paths.

        //$body = str_replace("{!! site_url !!}", $site_url, $body);

        $body = str_replace("{!! remainderdays !!}", $remdays, $body);
        //send mail to invitee to login into the system

        $mailResponse = 0;
        // dd($email);
        if (isset($email) && !empty($email)) {

            $mailResponse = Mail::raw($body, function ($message) use ($email, $subject, $body, $pathToFile, $attachmentFlag) {
                $message->from(FROM_EMAIL, APPL_TITLE);

                $message->to($email)->subject($subject);
                if ($attachmentFlag && !empty($pathToFile)) {
                    $message->attach($pathToFile);
                }

            });

            return $mailResponse;
        }
    }

    /*public static function sendMail($params) {
        $emailTemplate = self::get_email_template($params['emailAlertTemplateId']);
        $body = $emailTemplate->body;
        $subject = $emailTemplate->subject;
        foreach ($params as $key=>$value) {
            $replace = "{!! " . $key . " !!}";
            $subject = str_replace($replace, $value, $subject);
            $body = str_replace($replace, $value, $body);
        }
        LOG::info('In sendMail');
        return self::prepareEmail($params['email'], $subject, $body);
    }*/
}

