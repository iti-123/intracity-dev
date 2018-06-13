<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 04-02-2017
 * Time: 13:47
 */

namespace ApiV2\Modules\FCL;

use ApiV2\Framework\IBuyerPostAuthorizer;
use Log;


class FCLBuyerPostAuthorizer implements IBuyerPostAuthorizer
{
    function authorizeGet()
    {
        // TODO: Validate if the post either belongs to this user
        // or if it is privately posted to this buyer or if this is a public post

    }

    function authorizeSave()
    {
        $authorized = false;

        try {

            //TODO: Add required authorizations.

            //Check that the current role is buyer.


            //Check whether has access to the service.


            $authorized = true;

        } catch (\Exception $e) {

            LOG::info("Exception occured while authorizing -> " . $e->getTraceAsString());

            $authorized = false;
        }

        return $authorized;
    }

    function authorizeDelete()
    {
        // TODO: Implement authorizeDelete() method.
    }

}