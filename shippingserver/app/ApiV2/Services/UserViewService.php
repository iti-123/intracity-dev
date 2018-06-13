<?php
/**
 * Created by PhpStorm.
 * User: 10528
 * Date: 2/3/2017
 * Time: 11:50 AM
 */

namespace ApiV2\Services;

use App\EmailNotification;
use App\User;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;
use ApiV2\Model\UserView;
use Log;

use ApiV2\Services\LogistiksCommonServices\EncrptionTokenService;

class UserViewService
{

    public $serviceId;    
    public $userId;
    public $roleId;    
    public $modelId;    
    public $type;    
    public $createdBy;
    public $viewCount;
    public $updatedBy;
    public $updatedIp;    
    public $createdIp; 

    

    public function __construct($request)
    {   
        $gettype=gettype($request->data['routeId']);
        

        if ($gettype == 'integer') {
            $routeId = $request->data['routeId'];
         } else {
            $routeId = EncrptionTokenService::idDecrypt($request->data['routeId']);
         }

        $this->modelId = $routeId;
        $this->type = $request->data['type'];
        $this->serviceId = $request->data['serviceId'];
        $this->roleId = $request->data['roleId'] == 'seller'?2:1;
        
        $this->userId = JWTAuth::parseToken()->getPayload()->get('id');
        $this->createdBy = JWTAuth::parseToken()->getPayload()->get('id');
        $this->updatedBy = JWTAuth::parseToken()->getPayload()->get('id');
        
        $this->createdIp = $_SERVER['REMOTE_ADDR'];
        $this->updatedIp = $_SERVER['REMOTE_ADDR'];

        $this->viewCount = 1;

    }

    // Set visited user action

    public function userVisitAction() {
    
         // Check if Current user visited  
        $userView = UserView::where([
            'model_id'=>$this->modelId,
            'type'=>$this->type,
            'service_id'=>$this->serviceId,
            'role_id'=>$this->roleId
        ])->first();

        if(empty($userView)) {
        // Visited new user  
            $userView = new UserView();
            $userView->created_by = $this->createdBy;
            $userView->view_count = $this->viewCount;
            $userView->created_ip = $this->createdIp;
            $message = 'new user visited';
        } else {
        // update visit_count + 1            
            $userView->updated_by = $this->updatedBy;
            $userView->updated_ip = $this->updatedIp;
            $userView->view_count = $userView->view_count+1;
            $message = 'User view updated';
        }        
        
        $userView->model_id = $this->modelId;
        $userView->type = $this->type;
        $userView->service_id = $this->serviceId;
        $userView->role_id = $this->roleId;
        
        $userView->user_id = $this->userId;       

        $userView->save();
        
        return response()->json([
            'message'=>$message,
            'payload'=>$userView
        ]);
    }



    
}

