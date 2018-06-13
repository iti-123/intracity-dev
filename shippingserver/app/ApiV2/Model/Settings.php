<?php
/**
 * Created by PhpStorm.
 * User: 10626
 * Date: 3/28/2017
 * Time: 6:10 PM
 */

namespace ApiV2\Model;


use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{

    protected $table = 'user_settings';

    protected $fillable = [
        'user_id', 
        'role_id',
        'service_id',
        'page_type',
        'settings',  
        'created_by',
        'created_at',
        'created_ip',
        'updated_by',
        'updated_at',
        'updated_ip',
    ];

}