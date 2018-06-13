<?php
/**
 * Created by PhpStorm.
 * User: 10528
 * Date: 2/27/2017
 * Time: 8:00 AM
 */

namespace ApiV2\Controllers;

use Illuminate\Http\Request;

interface IPrivateMessagesController
{
    public function getAllMessages();

    public function getMessageById($postId);

    public function saveMessage(Request $request);
}