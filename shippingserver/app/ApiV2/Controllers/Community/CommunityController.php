<?php
namespace ApiV2\Controllers\Community;

use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use ApiV2\Controllers\BaseController;
use ApiV2\Services\Community\ProfileService;
class CommunityController extends BaseController
{    
    
    public function getFollowers(Request $request)
    {
        try {
            return response()->json([
                'isSuccessfull' => true,
                'payload' => ProfileService::getFollowers($request->data)
            ]);
        } catch(Exception $e) {

        }
    }


    public function getAllUsers(Request $request)
    {
        try {
            return response()->json([
                'isSuccessfull' => true,
                'payload' => ProfileService::getAllUsers($request->data)
            ]);
        } catch(Exception $e) {

        }
    }

    public function getProfileDetail(Request $request)
    {
        try {
            return response()->json([
                'isSuccessfull' => true,
                'payload' => ProfileService::getProfileDetail($request)
            ]);
        } catch(Exception $e) {

        }
    }


        public function getSellerDetail(Request $request)
    {



        try {
            return response()->json([
                'isSuccessfull' => true,
                'payload' => ProfileService::getSellerDetail($request)
            ]);
        } catch(Exception $e) {

        }
    }

}