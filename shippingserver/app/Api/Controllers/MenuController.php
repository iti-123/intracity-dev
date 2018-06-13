<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 2/6/17
 * Time: 4:08 PM
 */

namespace Api\Controllers;

use Api\Services\CatalogService;
use Log;


/**
 * Class MenuController
 * @package Api\Controllers
 */
class MenuController extends BaseController
{
    /**
     * MenuController constructor.
     */
    public function __construct()
    {

    }


    /**
     * Return user-menu as UserMenuBO
     *
     */
    public function getUserMenu($userId)
    {


        try {

            $service = new CatalogService();

            $catalog = $service->getServiceCatalog($userId);

            return response()->json($catalog);

        } catch (\Exception $e) {

            LOG::error($e->getMessage());
            return $this->errorResponse($e);

        }

    }

}