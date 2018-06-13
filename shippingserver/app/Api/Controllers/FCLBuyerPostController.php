<?php

namespace Api\Controllers;

use Api\Modules\FCL\FCLBuyerPostFactory;

/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 04-02-2017
 * Time: 16:13
 *
 */
class FCLBuyerPostController extends AbstractBuyerPostController implements IBuyerPostController
{
    public function __construct()
    {
        $this->serviceFactory = new FCLBuyerPostFactory();

        //TODO find betterway to set the following variable as tehse not applicable in all cases
        //Set the Maximum Rows of an Excel Uplaod
        $this->mainSheetSpotMaxRowNum = 6;

        //Set the Details sheet Last Column
        $this->detailSheetSpotRange = 'AX';
        /********************** Following settings are applicable for Term. ********************/

        //Set the Maximum Rows of an Term Excel Uplaod
        $this->mainSheetTermMaxRowNum = 21;

        //Set Maximum number of Columns of a Term Excel file
        $this->detailSheetTermRange = 'AU';
        //Set the Details sheet Last Column
    }

}