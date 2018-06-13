<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 04-02-2017
 * Time: 13:47
 */

namespace Api\Modules\AirFreight;

use Api\Framework\ISellerPostAuthorizer;


class AirFreightSellerPostAuthorizer implements ISellerPostAuthorizer
{
    function authorizeGet()
    {
        // TODO: Validate if the post either belongs to this user
        // or if it is privately posted to this buyer or if this is a public post

    }

    function authorizeSave()
    {
        // TODO: Implement authorizeSave() method.
    }

    function authorizeDelete()
    {
        // TODO: Implement authorizeDelete() method.
    }

}