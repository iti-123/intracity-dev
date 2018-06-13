<?php
namespace ApiV2\Controllers\Community;

use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use ApiV2\Controllers\BaseController;
use ApiV2\Services\Community\FollowService;
class FollowController extends BaseController
{    
    
    public function getFollowers(Request $request)
    {
        try {
            return response()->json([
                'isSuccessfull' => true,
                'payload' => FollowService::getFollowers($request->data)
            ]);
        } catch(Exception $e) {

        }
    }

    public function follow(Request $request)
    {
        try {
            return response()->json([
                'isSuccessfull' => true,
                'payload' => FollowService::follow($request->data)
            ]);
        } catch(Exception $e) {

        }
    }
    public function getMyFollowers(Request $request)
    {
        try {
            return response()->json([
                'isSuccessfull' => true,
                'payload' => FollowService::getMyFollowers($request->data)
            ]);
        } catch(Exception $e) {

        }
    }

    public function getMyFollowing(Request $request)
    {
        try {
            return response()->json([
                'isSuccessfull' => true,
                'payload' => FollowService::getMyFollowing($request->data)
            ]);
        } catch(Exception $e) {

        }
    }
    
    public function unFollow(Request $request)
    {
        try {
            return response()->json([
                'isSuccessfull' => true,
                'payload' => FollowService::unFollow($request->data)
            ]);
        } catch(Exception $e) {

        }
    }
    
    
    

}