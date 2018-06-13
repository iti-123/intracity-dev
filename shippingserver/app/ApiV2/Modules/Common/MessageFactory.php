<?php
/**
 * Created by PhpStorm.
 * User: 10528
 * Date: 2/27/2017
 * Time: 5:31 PM
 */

namespace ApiV2\Modules\Common;

use ApiV2\Model\PrivateMessages;
use Log;

class MessageFactory
{
    public function __construct()
    {
        LOG::info('MessageFactory __constructer called');
    }

    public function makeAuthorizer()
    {
        return new MessageAuthorizer();
    }

    public function makeTransformer()
    {
        return new MessageTransformer();
    }

    public function makeValidator()
    {
        return new MessageValidator();
    }

    public function makeService()
    {
        return new MessageService();
    }

}