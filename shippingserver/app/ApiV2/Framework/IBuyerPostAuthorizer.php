<?php

/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 04-02-2017
 * Time: 13:44
 */

namespace ApiV2\Framework;

interface IBuyerPostAuthorizer
{
    function authorizeGet();

    function authorizeSave();

    function authorizeDelete();

}