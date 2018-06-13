<?php
namespace ApiV2\Controllers\Community;

use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use ApiV2\Controllers\BaseController;
use ApiV2\Services\Community\MemberService;
class MemberController extends BaseController
{    
    
    public function getAllBusiness(Request $request)
    {
        try {
            return response()->json([
                'isSuccessfull' => true,
                'payload' => MemberService::getAllBusiness($request)
            ]);
        } catch(Exception $e) {

        }
    }

    public function sendInvitation(Request $request)
    {
        try {
            return response()->json([
                'isSuccessfull' => true,
                'payload' => MemberService::sendInvitation($request->data)
            ]);
        } catch(Exception $e) {
            \Log::info($e);
        }
    }

    public function getInvitation(Request $request)
    {
        try {
            return response()->json([
                'isSuccessfull' => true,
                'payload' => MemberService::getInvitation($request)
            ]);
        } catch(Exception $e) {
            \Log::info($e);
        }
    }

    public function actionOnInvitation(Request $request)
    {
        try {
            return response()->json([
                'isSuccessfull' => true,
                'payload' => MemberService::actionOnInvitation($request->data)
            ]);
        } catch(Exception $e) {
            \Log::info($e);
        }
    }


    public function sendBulkInvitation(Request $request)
    {
        try {
            return response()->json([
                'isSuccessfull' => true,
                'payload' => MemberService::sendBulkInvitation($request->data)
            ]);
        } catch(Exception $e) {
            \Log::info($e);
        }
    }

    public function createNewGroup(Request $request)
    {
        try {
            return response()->json([
                'isSuccessfull' => true,
                'payload' => MemberService::createNewGroup($request->data)
            ]);
        } catch(Exception $e) {
            \Log::info($e);
        }
    } 
    
    public function getGroupAction(Request $request)
    {
        try {
            return response()->json([
                'isSuccessfull' => true,
                'payload' => MemberService::getAllGroup($request)
            ]);
        } catch(Exception $e) {
            \Log::info($e);
        }
    }

}