<?php
namespace ApiV2\Controllers\Community;

use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use ApiV2\Controllers\Community\CommunityController;
use ApiV2\Services\Community\ProfileService;

class CommunityProfileController extends CommunityController
{    
    public function communityProfile(Request $request) {
        try {
            return response()->json([
                'isSuccessfull' => true,
                'payload' => ProfileService::communityProfile($request->data)
            ]);
        } catch(Exception $e) {

        }
    } 
    
    
    

}