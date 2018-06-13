<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 03-02-2017
 * Time: 19:21
 */

namespace Api\Services;


interface AuthenticationAware
{
    public function setSecurityPrincipal($principal);

    public function getPrincipal();

}