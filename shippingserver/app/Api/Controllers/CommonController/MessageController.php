<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 07-02-2017
 * Time: 19:37
 */

namespace Api\Controllers\CommonController;


use Api\Controllers\BaseController;
use Api\Services\LogistiksCommonServices\MessageServices;
use Exception;
use Illuminate\Http\Request;
use Pusher\Pusher;

class MessageController extends BaseController
{
    public function getMessage(Request $request)
    {
        try {
            return $message = MessageServices::getMessage($request);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function reply(Request $request, $messageId)
    {
        try {
            return $message = MessageServices::reply($request, $messageId);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function getMessageBySenderId($senderId)
    {
        try {
            return $message = MessageServices::getMessageBySenderId($senderId);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function broadcastMessage($channel, $event)
    {

        $options = array(
            'cluster' => 'ap2',
            'encrypted' => true
        );
        $pusher = new Pusher(
            '276a19811bb8506bfcf0',
            '80bfbea76dfe6a4d817d',
            '365743',
            $options
        );
        $data['message'] = 'hello world';
        try {
            return response()->json(["statuswsss" => $pusher->trigger(array($channel), $event, $data)]);
        } catch (Exception $e) {

        }

        // $message = new BroadcastMessage($pusher);
        // return response()->json(["status"=>$message->broadcast(array($channel), $event,$data)]); 

    }
}
