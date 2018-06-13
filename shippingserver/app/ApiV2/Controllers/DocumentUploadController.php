<?php

namespace ApiV2\Controllers;

use ApiV2\Requests\BaseShippingResponse as ShipRes;
use ApiV2\Services\DocumentService;
use Illuminate\Http\Request;
use Log;

class DocumentUploadController extends BaseController
{

    public function uploadFiles(Request $request)
    {
        try {

            $sp = DocumentService::upload($request);

            return ShipRes::ok($sp);

        } catch (\Exception $e) {

            LOG::error($e->getMessage());
            return $this->errorResponse($e);

        }
    }

    public function uploadMessageFiles(Request $request)
    {
        dd("ikugjggy");
    }


    public function getFile($docid)
    {
        try {

            $response = DocumentService::getUrl($docid);

            return response()->download($response);

        } catch (\Exception $e) {

            LOG::error($e->getMessage());
            return $this->errorResponse($e);

        }
    }
}