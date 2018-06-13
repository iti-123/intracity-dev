<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 30-Jan-17
 * Time: 11:06 AM
 */

namespace Api\Requests;

class IntraHyperBuyerPostRequest
{
    public static function getVehicleTypeName($value)
    {
        $vehicle_type = '';
        if (!empty(self::has($value, 'd_vehicle_type_any'))) {
            $vehicle_type = $value->d_vehicle_type_any->vehicle_type;
        } else if (!empty(self::has($value, 'd_vehicle_type_term'))) {
            $vehicle_type = $value->d_vehicle_type_term->vehicle_type;
        } else if (!empty(self::has($value, 'vehicle_type_any_dTerm'))) {
            $vehicle_type = $value->vehicle_type_any_dTerm->vehicle_type;
        }


        return $vehicle_type;
    }

    public static function getType($value)
    {
        $return_value = '';
        $type = self::has($value, 'type');
        if (!empty($type)) {
            if ($type == 'term') {
                $return_value = INTRA_HYPER_TERM;
            } else if ($type == 'spot') {
                $return_value = INTRA_HYPER_SPOT;
            }
        }
        return $return_value;
    }

    public static function getCityName($value)
    {
        $return_value = '';
        if (!empty(self::has($value, 'city'))) {
            $return_value = $value->city->city_name;
        } else if (!empty(self::has($value, 'term_city_id'))) {
            $return_value = $value->term_city_id->city_name;
        } else if (!empty(self::has($value, 'term_distance_city_id'))) {
            $return_value = $value->term_distance_city_id->city_name;
        }
        return $return_value;
    }

    public static function getHDSlabsName($value)
    {
        $return_value = '';
        if (!empty(self::has($value, 'hd_slab'))) {
            $return_value = $value->hd_slab->distance_hour;
        } else if (!empty(self::has($value, 'hd_slab_term'))) {
            $return_value = $value->hd_slab_term->distance_hour;
        }

        return $return_value;
    }

    public static function explode($value)
    {
        if (!empty($value)) {
            return explode(',', $value);
        }
        return false;
    }

    public static function jsonDecode($data)
    {
        return json_decode($data);
    }

    public static function isPublic($input) {
        if(isset($input) && !empty($input)) {
            if($input==1) {
                return $input;
            } elseif ($input==2) {
                return 0;
            }
        }   
    }

    public function routeArray($routes, $id, $primaryData)
    {
        foreach ($routes as $key => $value):
            $insertdata[$key] = array(
                'is_seller_buyer' => BUYER,// for buyer  
                'fk_buyer_seller_post_id' => $id,
                'lkp_service_id' => _INTRACITY_, // Buyer Post Id               
                'type_basis' => self::getTypeBasis($value),
                'city_id' => self::getCity($value),
                'hour_dis_slab' => self::getHDSlabs($value),
                'vehicle_type_id' => self::getVehicleType($value),
                'valid_from' => self::getValidFrom($value),
                'valid_to' => self::getValidTo($value),
                'number_of_veh_need' => self::getTotalVehicle($value),
                'vehicle_rep_location' => self::getReportingLocation($value),
                'vehicle_rep_time' => self::getReportingTime($value),
                'weight' => self::getWeight($value),
                'loads' => json_encode(self::has($value, 'loads')),
                'material_type' => self::getMaterial($value),
                'from_location' => self::getFromLocation($value),
                'to_location' => self::getToLocation($value),
                'is_active' => IS_ACTIVE
            );

            //$service = new Solr();
            //$service->add($insertdata[$key],$value,$primaryData);

        endforeach;
        return $insertdata;
    }

    public static function getTypeBasis($value)
    {
        $return_value = '';
        $type_basis = self::has($value, 'type_basis');
        if (!empty($type_basis)) {
            if ($type_basis == 'hours' || $type_basis == 'term_hours') {
                $return_value = INTRA_HYPER_HOURS;
            } else if ($type_basis == 'distance_basis' || $type_basis == 'term_distance') {
                $return_value = INTRA_HYPER_DISTANCE;
            }
        }
        return $return_value;
    }

    public static function has($object, $property)
    {
        return property_exists($object, $property) ? $object->$property : '';
    }

    public static function getCity($value)
    {
        $return_value = '';
        if (!empty(self::has($value, 'city'))) {
            $return_value = $value->city->id;
        } else if (!empty(self::has($value, 'term_city_id'))) {
            $return_value = $value->term_city_id->id;
        } else if (!empty(self::has($value, 'term_distance_city_id'))) {
            $return_value = $value->term_distance_city_id->id;
        }
        return $return_value;
    }

    public static function getHDSlabs($value)
    {
        $return_value = '';
        if (!empty(self::has($value, 'hd_slab'))) {
            $return_value = $value->hd_slab->id;
        } else if (!empty(self::has($value, 'hd_slab_term'))) {
            $return_value = $value->hd_slab_term->id;
        }

        return $return_value;
    }

    public static function getVehicleType($value)
    {
        $vehicle_type = '';
        if (!empty(self::has($value, 'd_vehicle_type_any'))) {
            $vehicle_type = $value->d_vehicle_type_any->id;
        } else if (!empty(self::has($value, 'd_vehicle_type_term'))) {
            $vehicle_type = $value->d_vehicle_type_term->id;
        } else if (!empty(self::has($value, 'vehicle_type_any_dTerm'))) {
            $vehicle_type = $value->vehicle_type_any_dTerm->id;
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
        } else if (!empty(self::has($value, 'valid_from_term'))) {
            $valid_from = self::has($value, 'valid_from_term');
        }

        return $valid_from;
    }

    public static function getValidTo($value)
    {
        $valid_to = '';
        if (!empty(self::has($value, 'd_valid_to'))) {
            $valid_to = self::has($value, 'd_valid_to');
        } else if (!empty(self::has($value, 'valid_to_term'))) {
            $valid_to = self::has($value, 'valid_to_term');
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
        } else if (!empty(self::has($value, 'getTotalVehicle'))) {
            $return_value = self::has($value, 'getTotalVehicle');
        } else if (!empty(self::has($value, 'no_of_vehicle_dTerm'))) {
            $return_value = self::has($value, 'no_of_vehicle_dTerm');
        } else if (!empty(self::has($value, 'no_of_vehicles_term'))) {
            $return_value = self::has($value, 'no_of_vehicles_term');
        }

        return $return_value;
    }

    public static function getReportingLocation($value)
    {
        $vehicle_reporting_location = '';
        if (!empty(self::has($value, 'vehicle_reporting_location'))) {
            $vehicle_reporting_location = self::has($value, 'vehicle_reporting_location')->id;
        } else if (!empty(self::has($value, 'vehicle_reporting_location_term'))) {
            $vehicle_reporting_location = self::has($value, 'vehicle_reporting_location_term')->id;
        }

        return $vehicle_reporting_location;
    }

    public static function getReportingTime($value)
    {
        $vehicle_rep_time = '';
        if (!empty(self::has($value, 'vehicle_reporting_time'))) {
            $vehicle_rep_time = self::has($value, 'vehicle_reporting_time');
        } else if (!empty(self::has($value, 'd_vehicle_reporting_time'))) {
            $vehicle_rep_time = self::has($value, 'd_vehicle_reporting_time');
        } else if (!empty(self::has($value, 'vehicle_reporting_time_term'))) {
            $vehicle_rep_time = self::has($value, 'vehicle_reporting_time_term');
        } else if (!empty(self::has($value, 'vehicle_reporting_time_dTerm'))) {
            $vehicle_rep_time = self::has($value, 'vehicle_reporting_time_dTerm');
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

// Return Hour distance slab id 

    public static function getMaterial($value)
    {
        $material_type = '';
        if (!empty(self::has($value, 'd_material_type'))) {
            $material_type = self::has($value, 'd_material_type') ? $value->d_material_type->id : '';
        } else if (!empty(self::has($value, 'material_type_dTerm'))) {
            $material_type = self::has($value, 'material_type_dTerm') ? $value->material_type_dTerm->id : '';
        }
        return $material_type;
    }

// Return Hour distance slab name

    public static function getFromLocation($value)
    {
        $return_value = '';
        if (!empty(self::has($value, 'd_from_location'))) {
            $return_value = self::has($value, 'd_from_location');
        } else if (!empty(self::has($value, 'from_location'))) {
            $return_value = $value->from_location->id;
        }
        return $return_value;
    }

    public static function getToLocation($value)
    {
        $return_value = '';
        if (!empty(self::has($value, 'd_to_location'))) {
            $return_value = self::has($value, 'd_to_location');
        } else if (!empty(self::has($value, 'to_location'))) {
            $return_value = $value->to_location->id;
        }
        return $return_value;
    }

    public function priceArray($requestData, $id)
    {
        $prices = array();

        if (!empty(self::has($requestData, 'price_inclusive'))) {
            foreach ($requestData->price_inclusive as $key => $value) {
                $prices[$key] = array(
                    'buyerpost_terms_id' => $id,
                    'price_type' => 'inclusive',
                    'price' => $value
                );
            }
        }
        if (!empty(self::has($requestData, 'price_exclusive'))) {
            foreach ($requestData->price_exclusive as $key => $value) {
                array_push($prices, array(
                    'buyerpost_terms_id' => $id,
                    'price_type' => 'exclusive',
                    'price' => $value
                ));
            }
        }
        return $prices;
    }

}
