<?php
namespace ApiV2\Services;
use Log;
use ApiV2\Model\IntraHyperBuyerPost;
use DB;

use Tymon\JWTAuth\Facades\JWTAuth;
abstract class AbstractNotificationService {

    public static function notificationCounterService($class,$bo) {
        Log::info(__FILE__. $bo);
        
        $query = static::getParams($class,$bo);

        $requiredParams = array(
            'cityId'=>array(),
            'routeId'=>array(),
            'postId'=>array(),
            'postedBy' => array(JWTAuth::parseToken()->getPayload()->get('id'))
        );

        $params = $query->get();


        $table = static::getTableFromClass($class);
        $countFromTable = $table['countFromTable'];


        Log::info('BO'. json_encode($params));
    // build params for count  
        foreach($params as $key => $value) {
            array_push($requiredParams['cityId'], $value->cityId);
            array_push($requiredParams['routeId'], isset($value->routeId) ? $value->routeId :'');
            array_push($requiredParams['postedBy'], $value->postedBy);
            array_push($requiredParams['postId'], $value->postId);
        }

    // Lets start count
        $count = array(
            'leads' => 0,
            'message' => 0
        );

     
        // Count leads  start
        
        // check if post posted by buyer then count from seller
        $requiredParams['role'] = $bo->role == 1 ? 2 : 1;
        $requiredParams['serviceId'] = $bo->lkp_service_id;
        
        $buildCountQuery = DB::table("$countFromTable as rc")
                        ->where('rc.lkp_service_id','=',$requiredParams['serviceId'])
                        ->where('rc.is_active','=',1);;

        if (isset($requiredParams['routeId']) && !empty($requiredParams['routeId']) && $requiredParams['role'] == BUYER) {
            
            $buildCountQuery->rightjoin('intra_hp_buyer_seller_routes as route',function($join) use ($requiredParams){
                $join->on('rc.id','=','route.fk_buyer_seller_post_id');                
            });        
    
            if(isset($requiredParams['cityId']) && !empty($requiredParams['cityId'])) {
                $buildCountQuery->whereIn('route.city_id',$requiredParams['cityId']);    
                $buildCountQuery->where('route.is_seller_buyer','=',$requiredParams['role']);
                $buildCountQuery->where('route.lkp_service_id','=',$requiredParams['serviceId']);        
                
            }
        } else {
            // Count leads for Hyperlocal buyer from seller rate cart  
            $buildCountQuery->whereIn('rc.city_id',$requiredParams['cityId']);                        
        }
        

        

        $count['leads'] = $buildCountQuery->count();
    
        // Count leads end 
        Log::info('QUERY::'.$buildCountQuery->toSql());

        return $count;

        Log::info('message'. json_encode($count));
                 
    }


// Count message
    public static function countMessage($requiredParams) {
        $countMessage = DB::table('user_messages as msg')
                        ->whereIn('msg.post_item_id',$requiredParams['routeId']);
                        
        return $countMessage->count();
    }

// Get params required to count leads enquiry 
    private static function getParams($class,$bo) {
        $table = static::getTableFromClass($class);
            $tableFrom = $table['postFromTable'];
            $query = DB::table("$tableFrom as pt")
                    ->where('pt.is_active', '=',1)
                    ->where('pt.id','=',$bo->id);
            
            

            
            if($class == 'ApiV2\Model\IntraHyperSellerPost' && $bo->lkp_service_id == _HYPERLOCAL_) {
                $query->select(
                    'pt.city_id as cityId'                    
                );
            } else {
                $query->join('intra_hp_buyer_seller_routes as route', function($join) use ($bo) {
                    $join->on('route.fk_buyer_seller_post_id','=','pt.id');                
                });
                $query->where('route.is_seller_buyer','=',$bo->role)
                    ->where('route.lkp_service_id','=',$bo->lkp_service_id);
    
                $query->select(
                    'route.city_id as cityId',
                    'route.from_location as fromLocation',
                    'route.to_location as toLocation',
                    'route.valid_from as validFrom',
                    'route.valid_to as validTo',
                    'route.id as routeId'                    
                );
            }
            
            $query->addSelect(
                'pt.posted_by as postedBy',
                'pt.id as postId',
                'pt.is_private_public as isPublic'
            );
            
        
        Log::info(__FUNCTION__. $query->toSql());
            
        return $query;
    }

// Get dynamic table from modal 

    private static function getTableFromClass($class) {

        $table = array(
            'postFromTable' => '',
            'countFromTable' => ''
        );

        if ($class == "ApiV2\Model\IntraHyperBuyerPost" || $class=="ApiV2\Model\IntraHyperBuyerPostTerm") {
            return $table = array(
                    'postFromTable' => 'intra_hp_buyer_posts',
                    'countFromTable' => 'intra_hp_sellerpost_ratecart'
                );             
        } else if($class == 'ApiV2\Model\IntraHyperSellerPost') {
            return $table = array(
                'postFromTable' => 'intra_hp_sellerpost_ratecart',
                'countFromTable' => 'intra_hp_buyer_posts'
            );
        }
    }



}