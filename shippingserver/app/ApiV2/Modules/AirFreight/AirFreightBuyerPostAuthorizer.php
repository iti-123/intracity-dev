<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 2/23/2017
 * Time: 4:07 PM
 */

namespace ApiV2\Modules\AirFreight;

use ApiV2\Framework\IBuyerPostAuthorizer;
use Log;

class AirFreightBuyerPostAuthorizer implements IBuyerPostAuthorizer
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