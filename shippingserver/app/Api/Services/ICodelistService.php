<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 2/6/17
 * Time: 11:44 PM
 */

namespace Api\Services;

/**
 * Interface ICodelistService is the gateway to codelist management.
 * @package Api\Services
 */
interface ICodelistService
{

    public function getCodelists();

}