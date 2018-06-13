<?php

/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 04-02-2017
 * Time: 13:44
 */

namespace Api\Framework;

interface ISellerPostAuthorizer
{
    function authorizeGet();

    function authorizeSave();

    function authorizeDelete();

}