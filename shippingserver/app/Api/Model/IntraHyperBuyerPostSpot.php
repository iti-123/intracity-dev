<?php

namespace Api\Model;

use DB;
use Illuminate\Database\Eloquent\Model;

class IntraHyperBuyerPostSpot extends Model
{

    protected $fillable = ['fk_buyer_id', 'product_type', 'type_basis', 'last_date', 'last_time', 'is_public_private', 'accept_term_cond'];

    protected $table = 'intra_hp_buyerpost_spots';

    public static function saveBuyerSpotsPost($data)
    {
        try {
            return self::insertBuyerSpotData($data);
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }

    }

    public static function insertBuyerSpotData($data)
    {

        $spotData = self::jsonDecode($data->spotData);

        $buyer_post = new IntraHyperBuyerPostSpot();
        $buyer_post->is_seller_buyer = 2;//for buyer
        $buyer_post->product_type = 1;//Intracity
        $buyer_post->type_basis = self::getTypeBasis($spotData);
        $buyer_post->accept_term_cond = self::has($spotData, 'term_condition');
        $buyer_post->is_public_private = self::has($spotData, 'post_type_term');
        $buyer_post->last_date = self::has($spotData, 'last_date');
        $buyer_post->last_time = self::has($spotData, 'last_time');

        /****************** Start Transaction Code here *******************/
        DB::transaction(function () use ($buyer_post, $data, $spotData) {
            $buyer_post->save();
            // Get Inserted Id
            $buyer_post_id = $buyer_post->id;
            $BuyerRoute = self::jsonDecode($data->attribute);

            // Check if Post is Private

            if (self::has($spotData, 'post_type_term') == 1) {
                $seller_ids = self::explode(self::has($spotData, 'visibleToSellers'));
                self::saveSeller($seller_ids, $buyer_post_id);
            }

            // Lets insert in buyer route list
            $insertdata = array();
            foreach ($BuyerRoute as $key => $value) {
                $insertdata[$key] = array(
                    'is_seller_buyer' => 2,// for buyer
                    'buyerpost_spot_id' => $buyer_post_id,
                    'post_type' => self::has($value, 'type'),
                    'type_basis' => self::getTypeBasis($value),
                    'city_id' => self::has($value, 'city'),
                    'hour_dis_slab' => self::has($value, 'hd_slab'),
                    'vehicle_type_id' => self::getVehicleType($value),
                    'valid_from' => self::getValidFrom($value),
                    'valid_to' => self::getValidTo($value),
                    'number_of_veh_need' => self::getTotalVehicle($value),
                    'vehicle_rep_location' => self::has($value, 'vehicle_reporting_location'),
                    'vehicle_rep_time' => self::getReportingTime($value),
                    'weight' => self::getWeight($value),
                    'material_type' => self::getMaterial($value),
                    'from_location' => self::getFromLocation($value),
                    'to_location' => self::getToLocation($value)
                );
            }
            // dd($insertdata);
            $buyer_routes = DB::table('intra_hp_buyer_seller_routes')->insert($insertdata);

        });
        return response()->json([
            'status' => 'success',
            'payload' => $buyer_post
        ], 200);

    }

    public static function jsonDecode($data)
    {
        return json_decode($data);
    }

    public static function getTypeBasis($value)
    {
        $return_value = '';
        $type_basis = self::has($value, 'type_basis');
        if (!empty($type_basis)) {
            if ($type_basis == 'hours' || $type_basis == 'term_hours') {
                $return_value = 1;
            } else if ($type_basis == 'distance_basis' || $type_basis == 'term_distance') {
                $return_value = 2;
            }
        }
        return $return_value;
    }

    public static function has($object, $property)
    {
        return property_exists($object, $property) ? $object->$property : '';
    }

    public static function explode($value)
    {
        if (!empty($value)) {
            return explode(',', $value);
        }
        return false;
    }

    public static function saveSeller($seller_ids, $buyer_post_id)
    {
        if ($seller_ids) {
            $ids = array();
            foreach ($seller_ids as $key => $value) {
                $ids[$key] = array(
                    'buyer_seller_post_id' => $buyer_post_id,
                    'buyer_seller_id' => $value,
                    'type' => 2, // for buyer
                    'is_active' => 0
                );
            }
            DB::table('intra_hp_assigned_seller_buyer')->insert($ids);
        }
    }

    public static function getVehicleType($value)
    {
        $vehicle_type = '';
        if (!empty(self::has($value, 'd_vehicle_type_any'))) {
            $vehicle_type = self::has($value, 'd_vehicle_type_any');
        }
        return $vehicle_type;
    }

    public static function getValidFrom($value)
    {
        $valid_from = '';
        if (!empty(self::has($value, 'd_valid_from'))) {
            $valid_from = self::has($value, 'd_valid_from');
        } else if (!empty(self::has($value, 'departure'))) {
            $valid_from = self::has($value, 'departure');
        }
        return $valid_from;
    }

    public static function getValidTo($value)
    {
        $valid_to = '';
        if (!empty(self::has($value, 'd_valid_to'))) {
            $valid_to = self::has($value, 'd_valid_to');
        }
        return $valid_to;
    }

    public static function getTotalVehicle($value)
    {
        $return_value = '';
        if (!empty(self::has($value, 'd_no_of_vehicle'))) {
            $return_value = self::has($value, 'd_no_of_vehicle');
        } else if (!empty(self::has($value, 'no_of_vehicles'))) {
            $return_value = self::has($value, 'no_of_vehicles');
        }
        return $return_value;
    }

    public static function getReportingTime($value)
    {
        $vehicle_rep_time = '';
        if (!empty(self::has($value, 'vehicle_reporting_time'))) {
            $vehicle_rep_time = self::has($value, 'vehicle_reporting_time');
        } else if (!empty(self::has($value, 'd_vehicle_reporting_time'))) {
            $vehicle_rep_time = self::has($value, 'd_vehicle_reporting_time');
        }
        return $vehicle_rep_time;
    }

    public static function getWeight($value)
    {
        $weight = '';
        if (!empty(self::has($value, 'd_weight'))) {
            $weight = self::has($value, 'd_weight');
        }
        return $weight;
    }

    public static function getMaterial($value)
    {
        $material_type = '';
        if (!empty(self::has($value, 'd_material_type'))) {
            $weight = self::has($value, 'd_material_type');
        }
        return $material_type;
    }

    public static function getFromLocation($value)
    {
        $return_value = '';
        if (!empty(self::has($value, 'd_from_location'))) {
            $return_value = self::has($value, 'd_from_location');
        }
        return $return_value;
    }

    public static function getToLocation($value)
    {
        $return_value = '';
        if (!empty(self::has($value, 'd_to_location'))) {
            $return_value = self::has($value, 'd_to_location');
        }
        return $return_value;
    }

    public static function saveBuyerTermPost($data)
    {
        try {
            return self::insertBuyerTermData($data);
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }

    public static function getType($value)
    {
        $return_value = '';
        $type = self::has($value, 'type');
        if (!empty($type)) {
            if ($type == 'term') {
                $return_value = 1;
            } else if ($type == 'spot') {
                $return_value = 2;
            }
        }
        return $return_value;
    }

    /* Count Buyer Post Spots*/
    public static function countbuyerpost()
    {

        /** For Total Number of Buyer Post Spots **/
        $get_total_spots = DB::table('intra_hp_buyerpost_spots')->where('is_active', 1)->count();


        /** For Total Number Private **/
        $get_total_private = DB::table('intra_hp_buyerpost_spots')
            ->where(['is_public_private' => 1])
            ->where('is_active', 1)
            ->count();

        /** For Total Number Public  **/
        $get_total_public = DB::table('intra_hp_buyerpost_spots')
            ->where(['is_public_private' => 2])
            ->where('is_active', 1)
            ->count();

        /** For Total Number of Buyer Post Terms **/
        $get_total_terms = DB::table('intra_hp_buyerpost_terms')
            ->where('is_active', 1)
            ->count();

        return response()->json([

            'total_buyerpost_spots' => $get_total_spots,
            'total_private_post' => $get_total_private,
            'total_public_post' => $get_total_public,
            'get_total_terms' => $get_total_terms


        ]);

    }
    /* Count Buyer Post Spots*/


    /** Buyer List Spots Table **/
    public static function buyerlist()
    {

        $get_buyer_list = DB::table('intra_hp_buyerpost_spots')
            ->select('intra_hp_buyerpost_spots.*')
            ->where('is_active', 1)
            ->orderBy('id', 'DESC')
            ->get();

        return $get_buyer_list;

    }
    /** Buyer List Spots Table **/


    /** All Records According to Filters **/
    public static function allrecords($request)
    {

        $payload = '';


        if ($request->type == 'all') {
            $payload = DB::table(DB::raw('intra_hp_buyerpost_spots s, intra_hp_buyerpost_terms t'))
                ->select(DB::raw('s.*,t.*'))
                ->where('s.is_active', 1)
                ->where('t.is_active', 1)
                ->get();
        } else if ($request->type == 'spot') {
            $payload = DB::table('intra_hp_buyerpost_spots')
                ->select('intra_hp_buyerpost_spots.*')
                ->where('is_active', 1)
                ->orderBy('id', 'DESC')
                ->get();

        } else if ($request->type == 'term') {
            $payload = DB::table('intra_hp_buyerpost_terms')
                ->select('intra_hp_buyerpost_terms.*')
                ->where('is_active', 1)
                ->orderBy('id', 'DESC')
                ->get();
        } else if ($request->type == 'public') {
            $payload = DB::table('intra_hp_buyerpost_spots')
                ->where(['is_public_private' => 2])
                ->where('is_active', 1)
                ->get();
        } else if ($request->type == 'private') {
            $payload = DB::table('intra_hp_buyerpost_spots')
                ->where(['is_public_private' => 1])
                ->where('is_active', 1)
                ->get();

        }

        return response()->json([
            "payload" => $payload
        ]);

    }

    /** All Records According to Filters **/

    public static function buyerFilterSearch()
    {

        $buyerfilters = DB::table(DB::raw('intra_hp_buyerpost_spots s, intra_hp_buyerpost_terms t'))
            ->select(DB::raw('s.*,t.*'))
            ->where('s.is_active', 1)
            ->where('t.is_active', 1)
            ->get();


    }

}
