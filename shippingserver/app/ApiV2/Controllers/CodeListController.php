<?php

namespace ApiV2\Controllers;

use ApiV2\Requests\BaseShippingResponse as shipres;
use ApiV2\Services\CodelistService;
use Log;

//use ApiV2\Transformers\CodeListTransformer;


class CodeListController extends BaseController
{

    public function index()
    {

        try {

            $svc = new CodelistService();
            $respCodelists = $svc->getCodelists();
            $resp = shipres::ok($respCodelists);
            return $resp;

        } catch (\Exception $e) {

            LOG::error($e->getMessage());
            return $this->errorResponse($e);

        }

    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($entity)
    {

        try {

            $svc = new CodelistService();
            $respCodelist = $svc->getCodelistByName($entity);
            $res = shipres::ok($respCodelist);
            return $res;

        } catch (\Exception $e) {

            LOG::error($e->getMessage());
            return $this->errorResponse($e);

        }

    }

    /**
     * @param $id
     * @return mixed
     */
    public function showMany($entityCsv)
    {

        try {

            $entities = str_getcsv($entityCsv);

            $svc = new CodelistService();
            $respCodelist = $svc->getCodelistByNames($entities);
            $res = shipres::ok($respCodelist);
            return $res;

        } catch (\Exception $e) {

            LOG::error($e->getMessage());
            return $this->errorResponse($e);

        }

    }
}
