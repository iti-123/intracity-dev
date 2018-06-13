<?php
namespace ApiV2\Controllers\Community;

use DB;
use ApiV2\Controllers\BaseController;
use ApiV2\Model\Community\ShareModel;
use ApiV2\Services\LogistiksCommonServices\EncrptionTokenService;
use ApiV2\Controllers\UserServices;
use ApiV2\Controllers\AbstractUserServices;
use ApiV2\Services\LogistiksCommonServices\DocumentServices;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class ShareController extends BaseController
{
    

    public function shared(Request $request)
    {
      try {
      		//return $request;
            return ShareModel::shareInsert($request);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

}