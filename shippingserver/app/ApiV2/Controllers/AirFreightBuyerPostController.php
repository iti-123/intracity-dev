<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 2/23/2017
 * Time: 4:01 PM
 */

namespace ApiV2\Controllers;

use ApiV2\Modules\AirFreight\AirFreightBuyerPostFactory;

class AirFreightBuyerPostController extends AbstractBuyerPostController implements IBuyerPostController
{


    public function __construct()
    {
        $this->serviceFactory = new AirFreightBuyerPostFactory();

        //TODO find betterway to set the following variable as these not applicable in all cases
        /********************** Following settings are applicable for Spot. ********************/

        //Set the Maximum Rows of an Spot Excel file
        $this->mainSheetSpotMaxRowNum = 7;

        //Set the Details sheet Last Column
        $this->detailSheetSpotRange = 'AP';

        /********************** Following settings are applicable for Term. ********************/

        //Set the Maximum Rows of an Term Excel Uplaod
        $this->mainSheetTermMaxRowNum = 18;

        //Set Maximum number of Columns of a Term Excel file
        $this->detailSheetTermRange = 'AP';
        //Set the Details sheet Last Column


    }


}