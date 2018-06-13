<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 07-02-2017
 * Time: 19:37
 */

namespace ApiV2\Framework;


use ApiV2\Controllers\BaseController;

class AbstractUsecaseController extends BaseController
{
    protected $serviceFactory;
    protected $mainSheetMaxRowNum;
    protected $detailSheetRange;
}