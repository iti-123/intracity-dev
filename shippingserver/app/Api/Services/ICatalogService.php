<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 2/6/17
 * Time: 4:23 PM
 */

namespace Api\Services;


interface ICatalogService
{

    public function getServiceCatalog($userId);

}