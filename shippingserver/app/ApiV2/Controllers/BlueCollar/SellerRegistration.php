<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 07-02-2017
 * Time: 19:37
 */

namespace ApiV2\Controllers\BlueCollar;


use ApiV2\Controllers\BaseController;
use ApiV2\Requests\BlueCollar\RegistrationRequest;
use ApiV2\Services\BlueCollar\SellerRegistrationService;
use ApiV2\Services\LogistiksCommonServices\SuggestionsServices;
use Exception;
use Illuminate\Http\Request;
use Response;

class SellerRegistration extends BaseController
{
    public function register(RegistrationRequest $request)
    {
        $response = SellerRegistrationService::register($request);
        return Response::json($response);
        try {
        } catch (Exception $e) {
            //return $e->message();
        }
    }

    public function citySuggestions(Request $request)
    {
        try {
            $results = SuggestionsServices::citySuggestions($request);
            return Response::json($results);
        } catch (Exception $e) {
            //return $e->message();
        }
    }

    public function vehicleTypes()
    {
        $results = SuggestionsServices::vehicleTypes();
        return Response::json($results);
        try {
        } catch (Exception $e) {
            //return $e->message();
        }
    }

    public function machineTypes()
    {
        $results = SuggestionsServices::machineTypes();
        return Response::json($results);
        try {
        } catch (Exception $e) {
            //return $e->message();
        }
    }
}
