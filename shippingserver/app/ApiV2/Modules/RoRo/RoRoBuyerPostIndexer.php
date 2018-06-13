<?php
/**
 * Created by PhpStorm.
 * User: chetan
 * Date: 23/2/17
 * Time: 11:58 PM
 */

namespace ApiV2\Modules\RoRo;

use ApiV2\BusinessObjects\BuyerPostBO;
use ApiV2\BusinessObjects\BuyerPostSearchBO;
use ApiV2\Framework\IBuyerPostIndexer;
use ApiV2\Model\RoRoSearchBuyerPost;
use ApiV2\Services\SolrSearchService;
use App\Exceptions\ApplicationException;
use Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class RoRoBuyerPostIndexer implements IBuyerPostIndexer
{

    public function rebuildIndex(BuyerPostBO $bo)
    {

        LOG:
        info("Rebuilding search index for buyerpost " . $bo->postId);

        try {

            $fclSearchRec = new RoRoSearchBuyerPost();

            $fclSearchRec->entity = "buyerpost";
            $fclSearchRec->serviceName = "RoRo";

            $fclSearchRec->buyerPostId = $bo->postId;
            $fclSearchRec->serviceId = $bo->serviceId;

            $fclSearchRec->leadType = $bo->leadType;

            if ($bo->leadType == 1) {
                $fclSearchRec->leadTypeName = "spot";
            } else {
                $fclSearchRec->leadTypeName = "term";
            }

            $fclSearchRec->lastDateTimeOfQuoteSubmission = $bo->lastDateTimeOfQuoteSubmission;
            $fclSearchRec->title = $bo->title;
            $fclSearchRec->buyerId = $bo->buyerId;

            $fclSearchRec->buyerName = JWTAuth::parseToken()->getPayload()->get('firstname');

            $fclBuyerPostAttributes = $bo->attributes;

            if (!empty($fclBuyerPostAttributes)) {
                $this->fillRoutes($fclBuyerPostAttributes, $fclSearchRec);
            }

            return true;

        } catch (\Exception $e) {

            LOG::error($e);

            throw new ApplicationException([], ["Failed posting buyerpost to Search Store"]);
        }
    }


    function fillRoutes(RoRoBuyerPostAttributes $attributes, RoRoSearchBuyerPost $fclSearchRec)
    {

        LOG::info("Filling routing information");

        if (!empty($attributes)) {

            $routes = $attributes->routes;

            foreach ($routes as $route) {

                $fclSearchRec->loadPort = $route->loadPort;
                $fclSearchRec->dischargePort = $route->dischargePort;
                $fclSearchRec->serviceSubType = $route->serviceSubType;
                $fclSearchRec->originLocation = $route->originLocation;
                $fclSearchRec->destinationLocation = $route->destinationLocation;

                $fclSearchRec->commodity = $route->commodity;
                $fclSearchRec->cargoReadyDate = $route->cargoReadyDate;

                $this->fillContainers($route, $fclSearchRec);
            }

        }

    }

    function fillContainers(Route $routeBo, RoRoSearchBuyerPost $fclSearchRec)
    {

        if (!empty($routeBo)) {

            $fclContainers = $routeBo->containers;

            foreach ($fclContainers as $container) {

                $fclSearchRec->containerType = $container->containerType;
                $fclSearchRec->containerQuantity = $container->quantity;
                $fclSearchRec->weightUnit = $container->weightUnit;
                $fclSearchRec->grossWeight = $container->grossWeight;

                //Now we have flattened the hierarchical buyer. Let us add this single record to SOLR.
                $service = new SolrSearchService();
                $service->add($fclSearchRec);

            }

        }

    }


    /**
     * @param $postId
     * @throws ApplicationException
     */
    public function dropIndex($postId)
    {
        LOG::info('Dropping existing search index for buyerpost = ' . $postId);
        try {

            $delQuery = "{'delete': {'query': 'entity:buyerpost and buyerPostId:$postId'}}";

            $service = new SolrSearchService();
            $service->remove($delQuery);

        } catch (Exception $e) {

            LOG::error($e);

            throw new ApplicationException(["postid" => $postId], ["Error deleting existing buyerposts from search store"]);
        }
    }

    public function searchIndex(BuyerPostSearchBO $bo)
    {

        $jsonResponse = null;

        LOG::info('Searching index for buyerposts');

        try {

            $fq = $this->generateSearchQuery($bo);

            LOG::info("filter query generated is " . $fq);

            $service = new SolrSearchService();

            $jsonResponse = $service->search(null, $fq, null, $bo->start, $bo->rows);

        } catch (Exception $e) {

            LOG::error($e);

            throw new ApplicationException([], ["Error searching buyerposts from search store"]);
        }

        return $jsonResponse;

    }


    private function generateSearchQuery(RoRoBuyerPostSearchBO $bo)
    {

        //Generated query should look like this.

        //fq=
        // entity:buyerpost
        //   AND serviceName:RoRo
        //   AND loadPort:Chennai
        //   AND dischargePort:Dubai
        //   AND commodity:Cement
        //   AND cargoReadyDate:  1486540738988
        //   AND (
        //        (
        //         containerType: 20-Feet
        //         AND containerQuantity: 2
        //         AND grossWeight: 33
        //         AND weightUnit : MT)
        //     OR (
        //         containerType: 40-Feet
        //         AND containerQuantity: 1
        //         AND grossWeight: 20
        //         AND weightUnit : MT
        //        )
        //     )


        $fq = "entity:buyerpost AND serviceName:RoRo ";

        if (isset($bo->loadPort)) {
            $fq .= " AND loadPort:" . $bo->loadPort;
        }

        if (isset($bo->dischargePort)) {
            $fq .= " AND dischargePort:" . $bo->dischargePort;
        }

        if (isset($bo->commodity)) {
            $fq .= " AND commodity:" . $bo->commodity;
        }

        //TODO: Check if CargoReadyDate should be an exact match or a lesser than match
        /*if(isset($bo->cargoReadyDate)){
            $fq .= "AND cargoReadyDate:" . $bo->cargoReadyDate;
        }*/


        $fqContainers = null;

        foreach ($bo->containers as $container) {

            if (isset($container->containerType)) {
                $fqContainers .= " containerType: " . $container->containerType;
            }

            if (isset($container->containerQuantity)) {
                $fqContainers .= " AND containerQuantity: " . $container->containerQuantity;
            }

            if (isset($container->grossWeight)) {
                $fqContainers .= " AND grossWeight: " . $container->grossWeight;
            }

            if (isset($container->weightUnit)) {
                $fqContainers .= " AND weightUnit: " . $container->weightUnit;
            }

            $fqContainers = ltrim($fqContainers, ' AND');

            $fqContainers = " ( " . $fqContainers . " ) OR ";
        }

        $fqContainers = rtrim($fqContainers, ' OR');

        if (count($bo->containers) > 0) {
            $fq = $fq . " AND ( " . $fqContainers . " )";
        }

        return $fq;

    }

}