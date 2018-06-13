<?php
/**
 * Created by PhpStorm.
 * User: 10528
 * Date: 2/9/2017
 * Time: 4:16 PM
 */

namespace Api\Services;

use DB;
use Input;
use Log;


class LocationService
{
    /***
     * @param $term for a location
     */
    public static function autocompleteCities($term)
    {

        $location = $term;
        $results = array();


        if (isset($location) && ($location <> '')) {
            $queries = DB::table('lkp_cities')->orderBy('city_name', 'asc')
                ->where('city_name', 'LIKE', $location . '%')
                ->get();
        }

        foreach ($queries as $query) {
            $results[] = ['id' => $query->id, 'value' => $query->city_name];
        }

        return $results;

    }

    /***
     * @param $term port name
     * @return array list of matching port names
     */
    public static function autocompletePorts($term)
    {

        $req_port = $term;
        $results = array();

        if (isset($req_port) && ($req_port <> '')) {
            $queries = DB::table('shp_lkp_seaports')->orderBy('seaport_name', 'asc')
                ->where('seaport_name', 'LIKE', $req_port . '%')
                ->get();
        }

        foreach ($queries as $query) {
            $results[] = ['id' => $query->id, 'value' => $query->seaport_name];
        }

        return $results;


    }

    /***
     * @param $term input zipcode
     * @return array list of zipcodes that match to be shown
     *
     */
    public static function autocompleteZipcodes($term)
    {

        $zipcode = $term;
        $results = array();

        if (isset($zipcode) && ($zipcode <> '')) {
            $queries = DB::table('lkp_ptl_pincodes')->orderBy('pincode', 'asc')
                ->where('pincode', 'LIKE', $zipcode . '%')
                ->get();
        }

        foreach ($queries as $query) {
            $results[] = ['pincode' => $query->pincode, 'postofficeName' => $query->postoffice_name];
        }

        return $results;


    }

    /***
     * @param $port port name
     * @return array list of matching port names
     */
    public static function autocompleteAirPorts($port)
    {

        $results = array();
        $queries = array();

        Log::info('User searching for Air port ' . $port);


        if (isset($port) && ($port <> '')) {
            $queries = DB::table('lkp_airports')->orderBy('airport_name', 'asc')
                ->where('airport_name', 'LIKE', $port . '%')
                ->get();
        }

        foreach ($queries as $query) {
            $results[] = ['id' => $query->id, 'value' => $query->airport_name];
        }

        return $results;

    }

    public static function isValidAirPort($port)
    {
        if (($port != null) && ($port != "")) {
            $result = DB::table('lkp_airports')->where('airport_name', $port)->get();
            if (count($result) > 0) {
                return 1; //true
            } else {
                return 0;
            }
        }
        return 0;  //false
    }

    public static function isValidSeaPort($port)
    {
        $flag = 0;  //false
        Log::debug('is this ValidSeaPort: ' . $port);
        if (($port != null) && ($port != "")) {
            $result = DB::table('shp_lkp_seaports')->where('seaport_name', $port)->get();
            Log::debug((array)$result);
            if (count($result) > 0) {
                Log::debug('Sea Port found');
                $flag = 1;  //true
            } else {
                Log::debug('Sea Port not found');
                $flag = 0;
            }
        }
        Log::debug('is Flag ' . $flag);

        return $flag;
    }

    public static function isValidLoaction($location)
    {
        if (($location != null) && ($location != "")) {
            $result = DB::table('lkp_cities')->where('city_name', $location)->get();
            if (count($result) > 0) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

}