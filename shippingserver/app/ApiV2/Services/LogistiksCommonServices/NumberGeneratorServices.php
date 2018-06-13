<?php

namespace ApiV2\Services\LogistiksCommonServices;

use ApiV2\Services\BlueCollar\BaseServiceProvider;
use Carbon\Carbon;

class NumberGeneratorServices extends BaseServiceProvider
{
    private static $matchSize = ['1' => '00000', '2' => '0000', '3' => '000', '4' => '00', '5' => '0', '6' => ''];

    public static function generateTranscationId($modal, $service_id)
    {
        $id = 1;
        $modal = $modal->orderBy('id', 'desc')->select("id")->first();
        if ($modal != null) {
            $id = $modal->id + 1;
        }
        $generatedId = self::formatString($id);
        $service = '';
        if ($service_id == _INTRACITY_) {
            $service = 'INT';
        }
        if ($service_id == _HYPERLOCAL_) {
            $service = 'HYPER';
        }
        
        if ($service_id == _BLUECOLLAR_) {
            $service = 'BLCO';
        }

        // str_pad($id, 6, "0", STR_PAD_LEFT)
        return $service . '/' . Carbon::now()->year . '/' . $generatedId;
    }

    public static function formatString($id)
    {
        $size = strlen((string)$id);
        if (array_key_exists($size, self::$matchSize)) {
            return self::$matchSize[$size] . $id;
        }
        return "Provided wrong String";
    }

}
