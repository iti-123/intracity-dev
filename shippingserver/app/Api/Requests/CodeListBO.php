<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 2/1/17
 * Time: 7:15 PM
 */

namespace Api\Requests;


class CodeListBO
{
    public $codeListType;

    public $values = [];

}

class Value
{

    public $key;

    public $value;

    public $childEntity;

    public $attributes = [];
}
