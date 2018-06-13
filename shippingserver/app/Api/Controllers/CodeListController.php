<?php

namespace Api\Controllers;

use Api\Requests\BaseShippingResponse as shipres;
use Api\Services\CodelistService;
use Log;

//use Api\Transformers\CodeListTransformer;


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
