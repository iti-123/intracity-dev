<?php

namespace Api\Controllers;

use Api\Requests\UserRequest;
use Api\Transformers\AuthTransformer;
use Api\Utils\CommonComponents;
use App\User as User;
use Auth;
use DB;
use Dingo\Api\Facade\API;
use Illuminate\Http\Request;
use Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends BaseController
{
    public function me(Request $request)
    {
        $users = JWTAuth::parseToken()->authenticate();
        $user_id = $users->id;
        //return JWTAuth::parseToken()->authenticate();
        return $this->collection(User::where('id', $user_id)->get(), new AuthTransformer);
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $email = $request->input('email');
        // Log::info("Login authenticate");
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        // Log::info("Login authenticate Success");
        $user = Auth::User();
        //  dd($user);
        Log::info($user);
        $serviceIds = '';
        $userId = $user->id;
        $a_role = $s_role = $current_role = $role = $designation = "";

        $userRole = $user->primary_role_id;

        //Current Role Check
        if (isset($user->lkp_role_id) && $user->lkp_role_id != '') {
            if ($user->lkp_role_id == '1') {
                $current_role = "Buyer";
            }
            if ($user->lkp_role_id == '2') {
                $current_role = "Seller";
            }
        } else {
            $current_role = "Buyer";
        }
        //Primary Role Check
        if (isset($user->primary_role_id) && $user->primary_role_id != '' && $user->primary_role_id == '1') {
            $p_role = "Buyer";
        }
        if (isset($user->primary_role_id) && $user->primary_role_id != '' && $user->primary_role_id == '2') {
            $p_role = "Seller";
        }

        //Secondary Role Check
        if (isset($user->secondary_role_id) && $user->secondary_role_id != '' && $user->secondary_role_id == '1') {
            $s_role = "Buyer";
        }
        if (isset($user->secondary_role_id) && $user->secondary_role_id != '' && $user->secondary_role_id == '2') {
            $s_role = "Seller";
        }

        if (!isset($user['designation']) && $user['designation'] != null) {
            $designation = "";
        }
        $role = '';
        $role = '';
        if ($current_role == 'Buyer') {
            $role = "Buyer";
            $customClaims = ['roleId' => $user->lkp_role_id, 'role' => $current_role, 'active_role_id' => $user->lkp_role_id, 'active_role_name' => $current_role, 'primary_role_id' => $user->primary_role_id, 'primary_role_name' => $p_role, 'secondary_role_id' => $user->secondary_role_id, 'secondary_role_name' => $s_role, "id" => $user->id, "firstname" => $user->username, "email" => $user->email, "phone" => $user->phone, "designation" => $designation];
        }

        if ($current_role == 'Seller') {
            //$service = new CatalogService();
            $serviceIds = CommonComponents::getServiceIds($userId);
            // $serviceIds = $service->getServiceIds($userId);
            //$s_Ids = implode(",", $serviceIds);
            $role = "Seller";
            $customClaims = ['roleId' => $user->lkp_role_id, 'role' => $current_role, 'active_role_id' => $user->lkp_role_id, 'active_role_name' => $current_role, 'primary_role_id' => $user->primary_role_id, 'secondary_role_id' => $user->secondary_role_id, 'secondary_role_name' => $s_role, "id" => $user->id, "firstname" => $user->username, "email" => $user->email, "phone" => $user->phone, "designation" => $designation, "sIds" => $serviceIds];
        }

        if ($userRole != '' && $userRole == '3') {
            $role = "ADMIN";
        }
        $token = \Tymon\JWTAuth\Facades\JWTAuth::fromUser($user, $customClaims);
        Log::info("Token" . $token);
        return $token;
    }

    public function switchRole($roleId)
    {
        // Log::info("Login authenticate");
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        DB::table('users')->where('id', $userId)->update(array('lkp_role_id' => $roleId));
        //  Session::put('last_login_role_id',$roleId);
        $user2 = User::where('id', $userId)->firstOrFail();
        $user = $user2['attributes'];
        $serviceIds = '';
        $userId = $user['id'];
        $a_role = $s_role = $role = $designation = "";
        $userRole = $user['primary_role_id'];

        //Current Role
        if (isset($user['lkp_role_id']) && $user['lkp_role_id'] != '') {
            if ($user['lkp_role_id'] == '1') {
                $a_role = "Buyer";
            }
            if ($user['lkp_role_id'] == '2') {
                $a_role = "Seller";
            }
        } else {
            $a_role = "Buyer";
        }

//Primary Role
        if (isset($user['primary_role_id']) && $user['primary_role_id'] != '' && $user['primary_role_id'] == '1') {
            $p_role = "Buyer";
        }
        if (isset($user['primary_role_id']) && $user['primary_role_id'] != '' && $user['primary_role_id'] == '2') {
            $p_role = "Seller";
        }

        //Secondary Role
        if (isset($user['secondary_role_id']) && $user['secondary_role_id'] != '' && $user['secondary_role_id'] == '1') {
            $s_role = "Buyer";
        }
        if (isset($user['secondary_role_id']) && $user['secondary_role_id'] != '' && $user['secondary_role_id'] == '2') {
            $s_role = "Seller";
        }

        if (!isset($user['designation']) && $user['designation'] != null) {
            $designation = "";
        }
        $role = '';
        if ($a_role == 'Buyer') {
            $role = "Buyer";
            $customClaims = ['roleId' => $user['lkp_role_id'], 'role' => $a_role, 'active_role_id' => $user['lkp_role_id'], 'active_role_name' => $a_role, 'primary_role_id' => $user['primary_role_id'], 'primary_role_name' => $p_role, 'secondary_role_id' => $user['secondary_role_id'], 'secondary_role_name' => $s_role, "id" => $user['id'], "firstname" => $user['username'], "email" => $user['email'], "phone" => $user['phone'], "designation" => $designation];
        }

        if ($a_role == 'Seller') {
            //$service = new CatalogService();
            $serviceIds = CommonComponents::getServiceIds($userId);
            //  dd($serviceIds);
            // $serviceIds = $service->getServiceIds($userId);
            //$s_Ids = implode(",", $serviceIds);
            $role = "Seller";
            $customClaims = ['roleId' => $user['lkp_role_id'], 'role' => $a_role, 'active_role_id' => $user['lkp_role_id'], 'active_role_name' => $a_role, 'primary_role_id' => $user['primary_role_id'], 'primary_role_name' => $p_role, 'secondary_role_id' => $user['secondary_role_id'], 'secondary_role_name' => $s_role, "id" => $user['id'], "firstname" => $user['username'], "email" => $user['email'], "phone" => $user['phone'], "designation" => $designation, "sIds" => $serviceIds];
        }
        if ($userRole != '' && $userRole == '3') {
            $role = "ADMIN";
        }
        $token = \Tymon\JWTAuth\Facades\JWTAuth::fromUser($user2, $customClaims);
        Log::info("Token" . $token);
        return $token;
    }

    public function refreshToken()
    {
        $token = JWTAuth::getToken();
        if (!$token) {
            throw new BadRequestHtttpException('Token not provided');
        }
        try {
            $token = JWTAuth::refresh($token);
            //  dd($token);
        } catch (TokenInvalidException $e) {
            throw new AccessDeniedHttpException('The token is invalid');
        }
        //  return $this->response->withArray(['token'=>$token]);
        return $token;
    }


    /*   public function authenticate(Request $request)
       {
           $credentials = $request->only('email', 'password');
           try {
               if (! $token = JWTAuth::attempt($credentials)) {
                   return response()->json(['error' => 'invalid_credentials'], 401);
               }
           } catch (JWTException $e) {
               return response()->json(['error' => 'could_not_create_token'], 500);
           }
           return response()->json(compact('token'));
       }
   */
    public function validateToken()
    {
        // Our routes file should have already authenticated this token, so we just return success here
        return API::response()->array(['status' => 'success'])->statusCode(200);
    }

    public function register(UserRequest $request)
    {
        $newUser = [
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password')),
        ];
        $user = User::create($newUser);
        $token = JWTAuth::fromUser($user);

        return response()->json(compact('token'));
    }

    public function index(Request $request)
    {
        $users = JWTAuth::parseToken()->authenticate();
        return $this->collection(User::get(), new AuthTransformer);
    }

    public function logoutUser()
    {
        $user = JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json($user);
    }


}