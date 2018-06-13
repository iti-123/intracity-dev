<?php

namespace ApiV2\Services\Community;
use ApiV2\Services\Community\BaseServiceProvider;
use ApiV2\Model\LogistiksUser;
use Tymon\JWTAuth\Facades\JWTAuth;
use ApiV2\Model\Community\Follower;
use ApiV2\Model\Community\CommunityConnection as Invitation;
use ApiV2\Services\LogistiksCommonServices\EncrptionTokenService as Encryptable;
use Illuminate\Support\Collection;
use ApiV2\Model\Community\Group;
use ApiV2\Model\Community\Member;
use ApiV2\Services\LogistiksCommonServices\DocumentServices;

class MemberService extends BaseServiceProvider
{
    
    public static function getAllBusiness($request) {
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        // Already followed user
        $filteredId = array($userId);
        
        $alreadySendInvitation = static::getAlreadySendInvitation($userId,$request['type']);

        foreach($alreadySendInvitation as $key=>$value) {
            array_push($filteredId,$value->user_id);
        }
        
        $business = LogistiksUser::where('users.is_active','=',1)
        ->with(['partners'=> function($query) use ($request) {
            $query->where('type','=',2)
                ->where('status','=',2);
        }])
        ->with(['connections'=> function($query) use ($request) {
            $query->where('type','=',1)
                ->where('status','=',2);
        }])
        ->with(['follower'=> function($query) use ($request,$userId) {
            $query->where('status','=',1)
            ->where('follower_id','=',$userId);
        }]);
        if(!empty($request['data']['name'])){
            $business->where('users.username', 'like', '%'.$request['data']['name'].'%');
        }


        $business->leftJoin('seller_details','seller_details.user_id','=','users.id');
        $business->leftJoin('lkp_localities','lkp_localities.id','=','seller_details.lkp_location_id');


        if(!empty($request['data']['company'])){
            $business->where('seller_details.name', 'like', '%'.$request['data']['company'].'%');
        }
                 
        if(!empty($request['data']['location'])){
            $business->where('lkp_localities.locality_name', 'like', '%'.$request['data']['location'].'%');
        }

        $business->whereNotIn('users.id',$filteredId);
        $business = static::getBusiness($business);
        return array(
            'business'=> $business->take(10)->get(['users.id',
                'username',
                'user_pic',
                'designation',
                'seller_details.user_id',
                'seller_details.lkp_location_id',
                'lkp_localities.locality_name']),
        );
    }

    // Get followers details a/c to user
    public static function getAlreadySendInvitation($activeUserId,$type) {
        return $checkAlreadyFollowed = Invitation::where([
            ['connector_id','=',$activeUserId],
            ['status','=',1],
            ['type','=',static::getConnectionType($type)]                       
        ])->orWhere('status','=',2)
        ->get(['user_id']);
    }
    

    private static function getBusiness($query) {
        
        $query->with(['seller','seller.city','seller.industry','seller.empStrength','seller.business']);
       
        return $query;
    } 
    
    
    public static function sendInvitation($request) {
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        
        $invitation = Invitation::firstOrCreate(
            [   
                'user_id'=>$request['userId'],
                'connector_id'=>$userId,
                'type'=> static::getConnectionType($request['type']),
                'status' => 1,
                'group_id'=> isset($request['groupId']) && !empty($request['groupId']) ? $request['groupId']:'',
            ]
        );

        if($invitation) {
            return array('message'=>"Invitation sent for ".$request['name']);
        } else {
            return array('message'=>"Fail to send invitation for ".$request['name']);
        }
    }

    public static function sendBulkInvitation($request) {
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        foreach ($request['userId'] as $key => $value) {
            $invitation = Invitation::firstOrCreate(
                [   
                    'user_id'=>$value,
                    'connector_id'=>$userId,
                    'type'=> static::getConnectionType($request['type']),
                    'status' => 1,
                    'message'=>$request['message']
                ]
            );
        }        
        return array('message'=>"Invitation sent to all selected user");        
    }
    


    private static function getConnectionType($type) {
        if ($type == 'individual') {
            return 1;
        } else if($type == 'partner') {
            return 2;
        } else if($type == 'group') {
            return 3;
        }
    }

    public static function getInvitation($request) {
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $invitation = Invitation::where('status','=',1)
                        ->where('user_id', '=',$userId)
                        ->where('type','=',static::getConnectionType($request->type))
                        ->with('user','group');
        $members = Invitation::where('status','=',2)
                        ->where('user_id', '=',$userId)
                        ->where('type','=',static::getConnectionType($request->type));                        
                        
        return array(
            'data'=>$invitation->take(3)->get(),
            'totalInvitation' => $invitation->count(),
            'totalMembers'=>$members->count()
        );
        
    } 



    public static function actionOnInvitation($request) {
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $status = 1;// default
        $message = '';
        if(isset($request['action']) && !empty($request['action'])) {
            if($request['action'] == 'accept') {
                $status = 2;
                $message = 'Invitation accepted';
            } else if($request['action'] == 'cancel') {
                $status = 3;
                $message = 'Invitation canceled';
            }
            $invt = Invitation::find($request['id']);
            $invt->status = $status;
            if($invt->save()) {
                return  array("message"=>$message);
            } 
        } else {
            return false;
        }
    } 


    public static function createNewGroup($request) {  
        $memberIds = isset($request['members']) && !empty($request['members']); 
        $checkGroupName = Group::where('name','=',$request['name'])
                            ->where('is_public','=',$memberIds?1:0)
                            ->count();
        
        if($checkGroupName) {
            return array("message"=>"Group \"{$request['name']}\" already exist");
        }

        $group = new Group();
        
        $group->name = $request['name'];
        $group->description = $request['description'];
        $group->is_public = $memberIds?1:0;
        $group->member_ids = $memberIds?json_encode($request['members']):'';
        $group->created_by = JWTAuth::parseToken()->getPayload()->get('id');
        $group->image = DocumentServices::uploadImage($request,'community/group/');
        $group->status = 1;
        \DB::transaction(function () use ($group,$request,$memberIds) {
            $group->save();
            if($memberIds) {
                $groupId = $group->id;
                foreach ($request['members'] as $key => $value) {
                    $member = new Member();
                    $member->group_id = $groupId;
                    $member->member_id = $value;
                    $member->status = 1;
                    $member->save();
                }
            } 
        });
       
        return array("message"=>"Group \"{$request['name']}\" created successfully","data"=>$group);
    }


    public static function getAllGroup($request) {
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $groups = static::getGroupAction($request);
        $groups->with(['isJoining'=>function($q) use ($userId) {
            $q->where('connector_id','=',$userId);
        }]);
        $groups->with(['members']);
        return  $groups->select('id','name','description','image','created_at','created_by')->paginate(100);       

    }

    public static function getGroupAction($request) {
       
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        
        $groups = Group::whereNotIn('created_by',[$userId])
                ->where('status','=',1)
                ->where('is_public','=',static::isPublic($request));
        return $groups;
    }

    public static function isPublic($request)
    {
        if($request['t'] == 'public') {
            return 0;
        } else if($request['t'] == 'private') {
            return 1;
        }
    }


       
}
