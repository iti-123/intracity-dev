<?php

namespace Api\Controllers;

use Api\Utils\MasterLocationData;
use Response;


class IntracityBuyerPostController extends BaseController
{

    public function hourDistanceLabs()
    {

        try {
            return Response::json(MasterLocationData::hourDistanceLabs());
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }

    }

}