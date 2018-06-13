<?php
/**
 * Created by PhpStorm.
 * User: 10528
 * Date: 2/8/2017
 * Time: 1:24 PM
 */

namespace App\Http\Controllers;


interface IMasterController
{
    public function getPorts();

    public function getLocations();

    public function getZipcodes();
}