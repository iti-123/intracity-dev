<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 2/22/2017
 * Time: 5:32 PM
 */

namespace ApiV2\Controllers;

use ApiV2\Modules\LCL\LCLBuyerPostFactory;


class LCLBuyerPostController extends AbstractBuyerPostController implements IBuyerPostController
{


    public function __construct()
    {
        $this->serviceFactory = new LCLBuyerPostFactory();

        //TODO find betterway to set the following variable as tehse not applicable in all cases
        //Set the Maximum Rows of an Excel Uplaod
        $this->mainSheetSpotMaxRowNum = 6;

        //Set the Details sheet Last Column
        $this->detailSheetSpotRange = 'AI';
        //   $this->mainSheetTermMaxRowNum = '';
        //  $this->detailSheetTermRange = '';

    }

}