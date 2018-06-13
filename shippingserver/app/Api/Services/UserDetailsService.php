<?php
/**
 * Created by PhpStorm.
 * User: chetan
 * Date: 21/2/17
 * Time: 6:26 PM
 */

namespace Api\Services;

use App\User;
use DB;
use Log;
use Session;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserDetailsService
{
    public static function getUserDetails($id)
    {
        try {
            $getUserrole = DB::table('users')
                ->where('users.id', $id)
                ->select('users.primary_role_id', 'users.is_business')
                ->first();
            if ($getUserrole->primary_role_id == 1) {
                if ($getUserrole->is_business == 1) {
                    $buyerTable = 'seller_details';
                    $contact = 'contact_mobile';
                } else {
                    $buyerTable = 'buyer_details';
                    $contact = 'mobile';
                }
            } else {
                $buyerTable = 'seller_details';
                $contact = 'contact_mobile';
            }

            $getUserDetails = DB::table('users')
                ->leftJoin($buyerTable, 'users.id', '=', $buyerTable . '.user_id')
                ->where('users.id', $id)
                ->select('users.*', $buyerTable . '.*', $buyerTable . '.' . $contact . ' as phone')
                ->first();

            if (count($getUserDetails) == 0)
                return $getUserDetails = array();
            else
                return $getUserDetails;
        } catch (Exception $e) {

        }
    }

    public static function getUserNameDetails()
    {

        $userID = JWTAuth::parseToken()->getPayload()->get('id');

        $roleId = JWTAuth::parseToken()->getPayload()->get('roleId');
        try {
            if (($roleId == BUYER && (Session::get('last_login_role_id') == 0)) || (Session::get('last_login_role_id') == BUYER)) {
                $buyerTable = 'buyer_details';
                $buyerDetails = DB::table('users')
                    ->leftJoin($buyerTable, 'users.id', '=', $buyerTable . '.user_id')
                    ->where('users.id', $userID)
                    ->select($buyerTable . '.address1', $buyerTable . '.address2', $buyerTable . '.address3', $buyerTable . '.contact_email', $buyerTable . '.firstname as contact_firstname', $buyerTable . '.mobile as contact_mobile', $buyerTable . '.principal_place')
                    ->first();

                if (!$buyerDetails->principal_place) {
                    $sellerTable = 'seller_details';
                    $sellerDetails = DB::table('users')
                        ->leftJoin($sellerTable, 'users.id', '=', $sellerTable . '.user_id')
                        ->where('users.id', $userID)
                        ->select($sellerTable . '.principal_place')
                        ->first();
                    $buyerDetails->principal_place = $sellerDetails->principal_place;
                }
            }

            return $buyerDetails;
        } catch (Exception $e) {
            // return $e->message;
        }
    }

    public static function getUserByName($name)
    {
        return User::where('username', $name)->id;
    }

    public static function getUserByEmail($email)
    {
        $user = User::where('email', $email)->first();
        LOG::debug($user);
        if (isset($user)) {
            return $user->id;
        } else {
            return null;
        }
    }
}