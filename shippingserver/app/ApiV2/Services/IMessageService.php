<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 4/5/17
 * Time: 8:24 PM
 */

namespace ApiV2\Services;


use ApiV2\BusinessObjects\MessageBO;
use ApiV2\BusinessObjects\MessageSearchBO;

interface IMessageService
{


    /**
     * @return mixed
     */
    public static function createMessage(MessageBO $bo, $docId);

    /**
     * @return mixed
     */
    public static function filter(MessageSearchBO $bo);

    /**
     * @return mixed
     */
    public static function getThread($messageId);

    /**
     * @return mixed
     */
    public static function getMessage($messageId);

    /**
     * @return mixed
     */
    public static function postReply(MessageBO $bo);

    /**
     * @return mixed
     */
    public static function markAsRead($messageId);

    /**
     * @return mixed
     */
    public static function notifyMessage();

    /**
     * @return mixed
     */
    public static function getMessageTypes($userId, $roleId);


    public static function getParentMessageid($messageId);

    public static function isValidMessage($messageId);

    public static function NotificationMessages();

    public static function getMessageDetails($messageId);
}