<?php
/**
 * Created by PhpStorm.
 * User: chetan
 * Date: 28/2/17
 * Time: 4:17 PM
 */

namespace ApiV2\Controllers;

use ApiV2\Requests\BaseShippingResponse as shipres;
use DB;
use Exception;
use Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class AbstractUserServices extends BaseController
{
    public function getAllSeller()
    {

        try {
            $users = $this->getAllUserDetails(SELLER);
            return $users;
        } catch (\Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    public function getAllUserDetails($userType)
    {
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $seller_data = DB::table('users')
            ->leftjoin('seller_details', 'users.id', '=', 'seller_details.user_id')
            ->where(['users.is_active' => 1])
            // ->whereRaw("(users.id != " . $userId . ")")
            ->whereRaw("(users.id != 1)")//excluding adminstration user
            ->whereRaw("(users.primary_role_id = " . $userType . " or users.secondary_role_id = " . $userType . ")")
            ->orderBy('users.username', 'asc')
            ->select('users.id', 'users.username')
            ->get();
        return $seller_data;
    }

    public function getAllBuyer()
    {
        //Delegate request to UserServices
        try {
            $users = $this->getAllUserDetails(BUYER);
            return shipres::ok($users);
        } catch (\Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    public function getNameList($searchval)
    {
        //Delegate request to UserServices
        try {
            $users = $this->getAllUsers($searchval);
            return shipres::ok($users);
        } catch (\Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    public function getAllUsers($searchval)
    {
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $userList = DB::table('users')
            ->where(['users.is_active' => 1])
            ->where('username', 'LIKE', $searchval . '%')
            ->where('users.id', '!=', $userId)
            ->orderby('users.id', 'asc')
            ->select('users.id', 'users.username')
            ->get();
        return $userList;

    }

    public function getUserById($userId)
    {
        try {
            $users = $this->getUserDetailsById($userId);
            return shipres::ok($users);
        } catch (\Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    public function getUserDetailsById($userId)
    {
        try {
            $getUserrole = DB::table('users')
                ->where('users.id', $userId)
                ->select('users.primary_role_id', 'users.is_business')
                ->first();
            if ($getUserrole->primary_role_id == 1)
                $userTable = 'buyer_details';
            else
                $userTable = 'seller_details';

            $getUserDetails = DB::table('users')
                ->leftJoin($userTable, 'users.id', '=', $userTable . '.user_id')
                ->leftjoin('lkp_cities as c1', $userTable . '.lkp_city_id', '=', 'c1.id')
                ->leftjoin('lkp_states as s1', $userTable . '.lkp_state_id', '=', 's1.id')
                ->leftjoin('lkp_districts as d1', $userTable . '.lkp_district_id', '=', 'd1.id')
                ->where('users.id', $userId)
                ->select('users.*', $userTable . '.*', 'c1.city_name as city', 's1.state_name as state', 'd1.district_name as district')
                ->first();
            if (count($getUserDetails) >= 1)
                return $getUserDetails;

        } catch (\Exception $e) {

            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    public function getCurrentUserDetails()
    {
        try {
            $userId = JWTAuth::parseToken()->getPayload()->get('id');
            $users = $this->getUserDetailsById($userId);
            return shipres::ok($users);
        } catch (\Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    public function getuseremail($searchval)
    {
        try {
            $users = $this->getUserEmailDetails($searchval);
            return shipres::ok($users);
        } catch (\Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    public function getUserEmailDetails($searchval)
    {
        try {
            $getUserEmailName = DB::table('users')
                ->where('users.username', 'like', $searchval . '%')
                ->orWhere('users.email', 'like', $searchval . '%')
                ->select('users.username', 'users.email')
                ->get();
            if (count($getUserEmailName) >= 1)
                return $getUserEmailName;
        } catch (\Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    public function getBuyerPostMasterCounts()
    {
        try {
            $userId = JWTAuth::parseToken()->getPayload()->get('id');
            $counts = $this->getUserPostMasterCountsData($userId);
            return shipres::ok($counts);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    public function getUserPostMasterCountsData($userId)
    {
        try {
            $userCounts = array();
            $getUserPrivacyPosts = DB::table('shp_buyer_posts')
                ->where('buyerId', $userId)
                ->groupBy('isPublic')
                ->select(DB::raw('case isPublic  when "0" then "private" when "1" then "public" end as Type, count(*) as count'));
            $getUserTotalPosts = DB::table('shp_buyer_posts')
                ->where('buyerId', $userId)
                ->groupBy('buyerId')
                ->select(DB::raw('"totalPosts" as Type, count(*) as count'));
            $getUserDetails = DB::table('shp_buyer_posts')
                ->where('buyerId', $userId)
                ->groupBy('leadType')
                ->select(DB::raw('leadType as Type, count(*) as count'))->union($getUserPrivacyPosts)->union($getUserTotalPosts)
                ->get();
            if (count($getUserDetails) >= 1) {
                for ($i = 0; $i < sizeof($getUserDetails); $i++) {
                    $userCounts[$getUserDetails[$i]->Type] = $getUserDetails[$i]->count;
                }
                return $userCounts;
            }

        } catch (\Exception $e) {

            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    public function getSellerPostMasterCounts()
    {
        try {
            $userId = JWTAuth::parseToken()->getPayload()->get('id');
            $counts = $this->getSellerPostMasterCountsData($userId);
            return shipres::ok($counts);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    public function getSellerPostMasterCountsData($userId)
    {
        try {
            $userCounts = array();
            $getUserPrivacyPosts = DB::table('shp_seller_posts')
                ->where('seller_id', $userId)
                ->groupBy('isPublic')
                ->select(DB::raw('case isPublic  when "0" then "private" when "1" then "public" end as Type, count(*) as count'));
            $getUserTotalPosts = DB::table('shp_seller_posts')
                ->where('seller_id', $userId)
                ->groupBy('seller_id')
                ->select(DB::raw('"totalPosts" as Type, count(*) as count'))->union($getUserPrivacyPosts)
                ->get();
            if (count($getUserTotalPosts) >= 1) {
                for ($i = 0; $i < sizeof($getUserTotalPosts); $i++) {
                    $userCounts[$getUserTotalPosts[$i]->Type] = $getUserTotalPosts[$i]->count;
                }
                return $userCounts;
            }

        } catch (\Exception $e) {

            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }


}