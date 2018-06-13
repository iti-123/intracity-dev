<?php

namespace ApiV2\Controllers;

use ApiV2\Requests\BaseShippingResponse as ShipRes;
use ApiV2\Services\LocationService;
use ApiV2\Utils\MasterLocationData;
use App\Exceptions\ValidationBuilder;
use DB;
use Log;
use Response;

class LocationController extends BaseController
{

    public function filterLocations($term)
    {


        try {

            //Prompt user to enter atleast 3 characters to search for
            if (strlen($term) < 3) {
                //atleast one validation error is found.
                ValidationBuilder::create()->error("Location", "Enter atleast 2 characters")->raise();
            }

            $matchingLocations = LocationService::autocompleteCities($term);
            return ShipRes::ok($matchingLocations);


        } catch (\Exception $e) {

            LOG::error($e->getMessage());
            return $this->errorResponse($e);

        }
    }

    public function filterPorts($term)
    {

        try {

            //Prompt user to enter atleast 3 characters to search for
            if (strlen($term) < 3) {
                //atleast one validation error is found.
                ValidationBuilder::create()->error("Port", "Enter atleast 3 characters")->raise();
            }

            $matchingPorts = LocationService::autocompletePorts($term);
            return ShipRes::ok($matchingPorts);

        } catch (\Exception $e) {

            LOG::error($e->getMessage());
            return $this->errorResponse($e);

        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * Function to retrieve all the available Ports
     *
     */

    public function filterZipcodes($term)
    {

        try {

            //Prompt user to enter atleast 3 characters to search for
            if (strlen($term) < 2) {
                ValidationBuilder::create()->error("Zipcode", "Enter atleast 3 characters")->raise();
            }

            $matchingZipCodes = LocationService::autocompleteZipcodes($term);
            return ShipRes::ok($matchingZipCodes);

        } catch (\Exception $e) {

            LOG::error($e->getMessage());
            return $this->errorResponse($e);

        }
    }

    /*
    |--------------------------------------------------------------------------
    | (Start) Codded By MindzTechnology
    |--------------------------------------------------------------------------
    */

    /**
     * @return \Illuminate\Http\JsonResponse
     * Function to retrieve all the available City
     *
     */
    public function getCity()
    {
        try {
            return Response::json(MasterLocationData::getCity());
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    public function getVehiletype()
    {
        try {
            return Response::json(MasterLocationData::getVehiletype());
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }

    }


    public function getLocality($id)
    {
        try {
            return Response::json(MasterLocationData::getLocality($id));
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }

    }

    public function intracityBuyerQuotesPost()
    {
        try {
            return Response::json(MasterLocationData::intracityBuyerQuotesPost());
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }

    }

    /* For Buyer Details */
    public function getbuyerdetails()
    {
        try {
            return Response::json(MasterLocationData::getbuyerdetails());
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }

    }

    /* For Location Type */
    public function locationType()
    {
        try {
            return Response::json(MasterLocationData::locationType());
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }

    }

    /* For Packaging Type */
    public function packagingType()
    {
        try {
            return Response::json(MasterLocationData::packagingType());
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }

    }

    /* For Seller Details */
    public function sellerDetails()
    {
        try {
            return Response::json(MasterLocationData::sellerDetails());
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);

        }

    }

    /* For getLoadType  */
    public function getLoadType()
    {
        try {
            $lkpLoadType = DB::table('lkp_load_types')
                ->select('id', 'load_type as name')
                ->where('is_active', 1)
                ->where('is_intracity', 1)
                ->get();
            return Response::json($lkpLoadType);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);

        }

    }


    /*
    |--------------------------------------------------------------------------
    | (End) Codded By MindzTechnology
    |--------------------------------------------------------------------------
    */


}
