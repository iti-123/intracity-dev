<?php

namespace ApiV2\Model\Intracity;

use Illuminate\Database\Eloquent\Model;

class SellerPostMasterSetting extends Model
{

	protected $table = 'user_settings';
	protected $fillable = [ 'user_id', 'user_type', 'role_id', 'service_id', 'page_type', 'setting_type', 'settings', 'created_by', 'updated_by'];

    /*    public static function save_Seller_PostMaster_Setting()
    {


    }*/
}