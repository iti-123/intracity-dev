<?php

namespace Api\Services\BlueCollar;

use Api\Model\BlueCollar\SellerRegistration;
use Api\Services\LogistiksCommonServices\EncrptionTokenService;
use Api\Services\LogistiksCommonServices\SolrServices;
use DB;

class BuyerSearchService extends BaseServiceProvider
{

    public static function locationSearch($request)
    {
        $searchTerm = $request->search;
        $suggestions = DB::table('bluecollar_seller_registration')
            ->join('lkp_cities', 'lkp_cities.id', '=', 'bluecollar_seller_registration.cur_city_id')
            ->join('lkp_districts', 'lkp_districts.id', '=', 'bluecollar_seller_registration.cur_district_id')
            ->join('lkp_states', 'lkp_states.id', '=', 'bluecollar_seller_registration.cur_state_id')
            ->where('bluecollar_seller_registration.verified', '=', 'YES')
            ->orWhere('lkp_cities.city_name', 'LIKE', '%' . $searchTerm . '%')
            ->orWhere('lkp_districts.district_name', 'LIKE', '%' . $searchTerm . '%')
            ->orWhere('lkp_states.state_name', 'LIKE', '%' . $searchTerm . '%')
            ->groupBy('lkp_cities.id')
            ->select('lkp_cities.id as city_id', 'lkp_cities.city_name as city_name', 'lkp_states.id as state_id', 'lkp_states.state_name as state_name', 'lkp_districts.id as district_id', 'lkp_districts.district_name as district_name')
            ->limit(10)
            ->get();

        self::$data['data'] = $suggestions;
        self::$data['success'] = true;
        self::$data['status'] = 200;
        return self::$data;
    }

    public static function search($request)
    {
        $input = $request->all();
        // $input['location'] = explode(',', $input['location']);
        $query = "seller_profile_type:{$input['profileType']}";
        if ($input['profileType'] == 'DRIVER') {
            $query .= self::solrFilterQueryBuilder($input['vehicleType'],
                'vehicle_type', true);
        } else if ($input['profileType'] == 'SKILLED') {
            $query .= self::solrFilterQueryBuilder($input['machineType'],
                'vehicle_type', true);
        }
        $query .= self::solrFilterQueryBuilder($input['employmentType'],
            'employment_type');
        $query .= self::solrFilterQueryBuilder($input['qualification'],
            'qualification');
        $query .= self::solrFilterQueryBuilder($input['rating'], 'rating');
        $query .= " AND seller_salary:[{$input['salary']['min']} TO {$input['salary']['max']}]";
        $query .= " AND seller_experience:[{$input['experience']['min']} TO {$input['experience']['max']}]";
        $query .= " AND seller_state:" . $input['location']['state_id'];
        $query .= " AND seller_city:" . $input['location']['city_id'];
        $query .= " AND seller_district:" . $input['location']['district_id'];
        $query .= self::solrFilterQueryBuilder($input['salaryType'], 'salary_type');
        $suggestions = SolrServices::search('bluecollar', $query);
        $suggestions->response->docs = EncrptionTokenService::idEncrypt($suggestions->response->docs);
//         foreach ($suggestions->response->docs as $key => $value) {
//             $value->dec_id = EncrptionTokenService::idDecrypt($value->id);
//             return $value;
//         }
        self::$data['data'] = $suggestions;
        self::$data['success'] = true;
        self::$data['status'] = 200;
        return self::$data;
    }

    public static function solrFilterQueryBuilder($arr, $attr, $isString = false)
    {
        if (!empty($arr)) {
            $query = " AND seller_{$attr}:(";
            foreach ($arr as $lKey => $value) {
                if ($lKey > 0) {
                    $query .= " OR ";
                }
                if ($isString) {
                    $query .= "\"{$value}\"";
                } else {
                    $query .= "{$value}";
                }
            }
            return $query . ")";
        }
    }

    public static function sellerDetails($request)
    {
        $id = $request->id;
        $sellerData = SellerRegistration::
        // with(['experience', 'qualification', 'curCity', 'perCity'])
        // ->selectRaw('*, AES_ENCRYPT(id, ?) as encId', [$userId])
        // ->whereRaw('id = AES_DECRYPT(id, ?)) = id', [utf8_decode($sellerId)])
        where('id', '=', $id)
            // ->where('verified', '=', 'NO')
            ->first();
        self::$data['data'] = $sellerData;
        self::$data['success'] = true;
        self::$data['status'] = 200;
        return self::$data;
    }

}
