<?php
/**
 * Created by PhpStorm.
 * User: sainath
 * Date: 2/21/17
 * Time: 7:11 PM
 */

namespace Api\Framework;


interface IOrderTransformer
{
    public function ui2bo_save($payload);
}