<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 07-02-2017
 * Time: 19:37
 */

namespace Api\Framework;


use Api\Controllers\BaseController;

class AbstractUsecaseController extends BaseController
{
    protected $serviceFactory;
    protected $mainSheetMaxRowNum;
    protected $detailSheetRange;
}