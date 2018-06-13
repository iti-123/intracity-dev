<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 2/6/17
 * Time: 4:10 PM
 */

namespace ApiV2\Requests;


class UserMenuBO
{

    // public $userRole;

    public $crumbs = [];

}

class ServiceCrumb
{

    public $name;

    public $invoiceServiceGroupId;

    public $groups = [];

}

class ServiceGroup
{
    public $name;

    public $services = [];
}


class Service
{

    public $serviceId;

    public $serviceName;

    public $fullName;

    public $imagePath;

    // public $opted;

}