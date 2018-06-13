<?php
/**
 * Created by PhpStorm.
 * User: 10528
 * Date: 2/8/2017
 * Time: 3:02 PM
 */

namespace Api\Utils;

use Auth;
use DB;
use Illuminate\Http\JsonResponse;
use Log;

class MasterLocationData
{

    public static function autocompleteCities($fromlocation, $source, $term)
    {
        try {
            Log::info('Seller Auto complete cities: ' . Auth::id(), array('c' => '1'));
            $term = $term;
            $fromlocation_loc = $fromlocation;
            $results = array();
            if (isset($fromlocation_loc)) {
                $queries = DB::table('lkp_cities')->orderBy('city_name', 'asc')
                    ->where('city_name', 'LIKE', $term . '%')
                    ->where('city_name', '<>', $fromlocation_loc)
                    ->get();
            } else {
                $queries = DB::table('lkp_cities')->orderBy('city_name', 'asc')
                    ->where('city_name', 'LIKE', $term . '%')
                    ->get();
            }
            foreach ($queries as $query) {
                $results[] = ['id' => $query->id, 'value' => $query->city_name];
            }
            //Log::info($results);

            if ($results) {
                return new JsonResponse(['isSuccessful' => 'true', 'errorCode' => '', 'errorMessage' => '', 'source' => $source, 'payload' => $results]);
            } else {
                return new JsonResponse(['isSuccessful' => 'false', 'errorCode' => '', 'errorMessage' => 'Empty Result set', 'source' => $source, 'payload' => $results]);
            }

        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }


    public static function autocompletePorts($fromlocation, $source, $term)
    {

        try {
            //Log::info('Seller Auto complete ports: ' . Auth::id(), array('c' => '1'));
            $term = $term;
            $fromlocation_loc = $fromlocation;
            $results = array();
            if (isset($fromlocation_loc)) {

                //Do not change the table name shipping will use lkp
                $queries = DB::table('shp_lkp_seaports')->orderBy('seaport_name', 'asc')
                    ->where('seaport_name', 'LIKE', $term . '%')
                    ->where('seaport_name', '<>', $fromlocation_loc)
                    ->get();
            } else {
                $queries = DB::table('shp_lkp_seaports')->orderBy('seaport_name', 'asc')
                    ->where('seaport_name', 'LIKE', $term . '%')
                    ->get();
            }
            foreach ($queries as $query) {
                $results[] = ['id' => $query->id, 'value' => $query->seaport_name];
            }

            //Log::info($results);

            /* $payload = new JsonResponse($results);
             return BaseShippingResponse::ok($results);*/

            if ($results) {
                return new JsonResponse(['isSuccessful' => 'true', 'errorCode' => '', 'errorMessage' => '', 'source' => $source, 'payload' => $results]);
            } else {
                return new JsonResponse(['isSuccessful' => 'false', 'errorCode' => '', 'errorMessage' => 'Empty Result set', 'source' => $source, 'payload' => $results]);
            }


        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }

    // Codded by MindzTechnology
    public static function getCity()
    {

        try {
            return $queries = DB::table('lkp_cities')->select('id', 'city_name')->orderBy('city_name', 'asc')
                ->where('is_active', 1)
                ->where('is_intracity', 1)
                ->get();
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }

    }

    public static function getVehiletype()
    {
        try {
            return $queries = DB::table('lkp_vehicle_types')->select('id', 'vehicle_type', 'dimension')->orderBy('vehicle_type', 'asc')
                ->where('is_active', 1)
                ->where('is_intracity', 1)
                ->get();
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }

    }


    //Get Location by city
    public static function getLocality($id)
    {
        // dd($id);
        try {
            return $queries = DB::table('lkp_localities')->select('id', 'locality_name')->orderBy('locality_name')
                ->where('is_active', 1)
                ->where('lkp_city_id', $id)
                ->get();
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }

    public static function intracityBuyerQuotesPost()
    {
        try {

            $query = DB::table('lkp_services')->insert(['lkp_invoice_service_group_id' => 1]);

            $selectQuery = DB::table('lkp_services')->select('id')
                ->where('id', $query['id'])
                ->get();

            //dd($query->last_insert_id());

            $queries = DB::table('intracity_buyer_quotes')->insert(['lkp_service_id' => 1, 'lkp_lead_type_id' => 1, 'lkp_quote_access_id' => 1, 'lkp_post_status_id' => 1]);

        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }


    //Get Buyer Details
    public static function getbuyerdetails()
    {
        try {
            return $queries = DB::table('buyer_details')->select('user_id as id', DB::raw('CONCAT(firstname, " ", lastname) AS full_name'))->orderBy('id', 'DESC')
                ->get();
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }


    //Get Location by city
    public static function hourDistanceLabs()
    {
        try {
            return DB::table('intracity_hour_distance_slabs')->select('id', DB::raw('CONCAT(hour, "hr/", distance, "KM") AS distance_hour'))
                ->where('is_active', 1)
                ->get();
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }

    //Get Location by city
    public static function locationType()
    {
        try {
            return $queries = DB::table('lkp_location_types')->select('id', 'location_type_name')
                ->where('is_active', 1)
                ->get();
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }

    //Get Location by city
    public static function packagingType()
    {
        try {
            return $queries = DB::table('lkp_packaging_types')->select('id', 'packaging_type_name')
                ->where('is_active', 1)
                ->get();
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }

    //Get Seller Details
    public static function sellerDetails()
    {
        try {
            return $queries = DB::table('seller_details')->select('id', 'name')->orderBy('id', 'DESC')
                ->get();
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }


    // Codded by MindzTechnology


}
