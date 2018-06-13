<?php

namespace Api\Services\BlueCollar;

use Api\Model\BlueCollar\Post;
use Api\Services\LogistiksCommonServices\EncrptionTokenService;

class SellerPostService extends BaseServiceProvider
{
    public static function postDetails($id)
    {
        $details = Post::with('bcData', 'bcData.curCity', 'bcData.curDistrict', 'bcData.curState', 'vehMach', 'vehMach.detail', 'postedBy', 'city', 'state', 'district', 'quote')
            ->where('id', '=', EncrptionTokenService::idDecrypt($id))
            ->first();


        self::$data['data'] = $details;
        self::$data['success'] = true;
        self::$data['status'] = 200;
        return self::$data;
    }


    public static function postDetailPage($id)
    {

        $details = Post::with('bcData', 'bcData.curCity', 'bcData.curDistrict', 'bcData.curState', 'vehMach', 'vehMach.detail', 'postedBy', 'city', 'state', 'district', 'quote')
            ->where('id', '=', $id)
            ->first();
        self::$data['data'] = $details;
        self::$data['success'] = true;
        self::$data['status'] = 200;
        return self::$data;

    }

}
