<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 2/6/17
 * Time: 4:53 PM
 */

namespace Api\Model;

use Illuminate\Database\Eloquent\Model;


class UserDetails extends Model
{
    public $timestamps = false;
    protected $table = 'users';

}