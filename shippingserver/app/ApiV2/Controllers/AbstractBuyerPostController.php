<?php

namespace ApiV2\Controllers;

use ApiV2\Framework\AbstractUsecaseController;
use ApiV2\Requests\BaseShippingResponse as shipres;
use App\Exceptions\ApplicationException;
use Exception;
use Illuminate\Http\Request;
use Log;
use PHPExcel_IOFactory;

class AbstractBuyerPostController extends AbstractUsecaseController
{

    /**
     * Retreive  GeneratedContracts based on the Idenfier passed
     * @param $id = BuyerPostId
     * * @return \Illuminate\Http\JsonResponse
     */
    public function getGeneratedContractsByPostId($id)
    {

        try {
            $this->postService = $this->serviceFactory->makeService();

            //Set sellerPost Factory
            $this->postService->setServicefactory($this->serviceFactory);

            //Delegate request to sellerPostService
            $sp = $this->postService->getGeneratedContractsByPostId($id);

            //Return Response
            LOG::info('response  from seller Post Service ', (array)$sp);
            return shipres::ok($sp);

        } catch (\Exception $e) {

            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    /**
     * Retreive  GeneratedContracts based on the Idenfier passed
     * @param $id = BuyerPostId
     * * @return \Illuminate\Http\JsonResponse
     */
    public function getGeneratedContractsByTermContractId($id)
    {


        try {
            $this->postService = $this->serviceFactory->makeService();

            //Set sellerPost Factory
            $this->postService->setServicefactory($this->serviceFactory);

            //Delegate request to sellerPostService
            $sp = $this->postService->getGeneratedContractsByTermContractId($id);

            //Return Response
            LOG::info('response  from seller Post Service ', (array)$sp);
            return shipres::ok($sp);

        } catch (\Exception $e) {

            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }

    }


    /**  Business Methods - Starts */

    /**
     * Retreive  Post based on the Idenfier passed
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPostById($id)
    {

        try {
            $this->postService = $this->serviceFactory->makeService();

            //Set sellerPost Factory
            $this->postService->setServicefactory($this->serviceFactory);

            //Delegate request to sellerPostService
            $sp = $this->postService->getPostById($id);

            //Return Response
            LOG::info('response  from seller Post Service ', (array)$sp);
            return shipres::ok($sp);

        } catch (Exception $e) {

            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveOrUpdateTerm(Request $request)
    {

        try {
            $payload = $request->getContent();

            //LOG::info("Request is => " . $payload);

            //Convert the request JSON into a BO
            $bo = $this->serviceFactory->makeTransformer()->ui2bo_save($payload, "term");

            LOG::info('Getting buyerpost service instance ', (array)$bo);
            $this->postService = $this->serviceFactory->makeService();

            LOG::info('Invoking service instance');

            //Set Security principal
            //$this->postService->setSecurityPrincipal($request->attributes->get("securityPrincipal"));

            //Set BuyerPost Factory
            $this->postService->setServicefactory($this->serviceFactory);

            //Delegate request to sellerPostService
            $boSaved = $this->postService->saveOrUpdateTerm($bo);

            //Return Response
            return shipres::ok($boSaved);

        } catch (\Exception $e) {

            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveOrUpdateSpot(Request $request)
    {


        try {

            $payload = $request->getContent();

            //LOG::info("Request is => " . $payload);

            //Convert the request JSON into a BO
            $bo = $this->serviceFactory->makeTransformer()->ui2bo_save($payload, "spot");

            $this->postService = $this->serviceFactory->makeService();

            LOG::info('Invoking service instance');

            //Set Security principal
            //$this->postService->setSecurityPrincipal($request->attributes->get("securityPrincipal"));

            //Set sellerPost Factory
            $this->postService->setServicefactory($this->serviceFactory);

            //Delegate request to sellerPostService
            $boSaved = $this->postService->saveOrUpdateSpots($bo);

            //LOG::info('Service retuned ', (array)$boSaved);

            //Return Response
            return shipres::ok($boSaved);

        } catch (\Exception $e) {

            LOG::error($e->getMessage());
            return $this->errorResponse($e);

        }
    }


    public function saveGenerateContract(Request $request)
    {

        try {

            $payload = $request->getContent();

            //Convert the request JSON into a BO
            $bo = $this->serviceFactory->makeTransformer()->ui2bo_save($payload, "contract");

            $this->postService = $this->serviceFactory->makeService();

            LOG::info('Invoking service instance');

            //Set Factory
            $this->postService->setServicefactory($this->serviceFactory);

            //Delegate request to BuyerPostService
            $boSaved = $this->postService->saveGenerateContract($bo);

            //Return Response
            return shipres::ok($boSaved);

        } catch (\Exception $e) {

            LOG::error($e->getMessage());
            return $this->errorResponse($e);

        }
    }

    /**
     * Upload Term Via an Excel Workbook
     * @param $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadSpotExcel(Request $request)
    {

        $bos = [];

        try {

            if (!$request->hasFile('uploadFile')) {
                throw new ApplicationException([], ["001" => "uploadFile needs to be specified"]);
            }

            //get the file
            $file = $request->file('uploadFile');

            //Load Excel
            $objPHPExcel = PHPExcel_IOFactory::load($file);

            //Parse Master Sheet
            $masterSheet = $objPHPExcel->getSheet(0);
            $topRow = $masterSheet->getHighestRow();

            $master = [];
            $totalRowsInMainSheet = $this->mainSheetSpotMaxRowNum;
            $totalColumnsInDeatilsSheet = $this->detailSheetSpotRange;

            //DO not comment out this  below line pl. update your respective controller as in AirFreightBuyerPostController
            for ($row = 1; $row <= $totalRowsInMainSheet; ++$row) {

                $masterRows = $masterSheet->rangeToArray('A' . $row . ':' . 'B' . $row, NULL, TRUE, FALSE);
                array_push($master, $masterRows[0][1]);
            }

            //Parse Details sheet
            $detailSheet = $objPHPExcel->getSheet(1);
            $topRow = $detailSheet->getHighestRow();

            $details = [];

            LOG::debug("Rows found in Details sheet [" . $topRow . "]");

            for ($row = 3; $row <= $topRow; ++$row) {
                //$rowData = $detailSheet->rangeToArray('A' . $row . ':' . $this->detailSheetRange . $row, NULL, TRUE, FALSE);
                $rowData = $detailSheet->rangeToArray('A' . $row . ':' . $totalColumnsInDeatilsSheet . $row, NULL, TRUE, FALSE);
                array_push($details, $rowData[0]);
            }

            //Parse Sellers sheet
            $sellersSheet = $objPHPExcel->getSheet(2);
            $topRow = $sellersSheet->getHighestRow();

            $sellers = [];

            LOG::debug("Rows found in Sellers sheet [" . $topRow . "]");

            for ($row = 2; $row <= $topRow; ++$row) {
                $rowData = $sellersSheet->rangeToArray('A' . $row . ':' . 'A' . $row, NULL, TRUE, FALSE);
                array_push($sellers, $rowData[0]);
            }

            LOG::debug("Converting xls to bo");

            //Transform Excel rows into a BO.
            $bos = $this->serviceFactory->makeTransformer()->spot_xls2bo_save($master, $details, $sellers);

            LOG::debug("Converted to bo");

            //Get the service
            $this->postService = $this->serviceFactory->makeService();

            //Set BuyerPost Factory
            $this->postService->setServicefactory($this->serviceFactory);

            //Delegate request to BuyerPostService
            $boSaved = $this->postService->saveOrUpdateSpots($bos);

            LOG::info('Service returned ', (array)$boSaved);

            //Return Response
            return shipres::ok($boSaved);

        } catch (\Exception $e) {

            LOG::error($e->getMessage());
            return $this->errorResponse($e);

        }
    }


    /**
     * Upload Term Via an Excel Workbook
     * @param $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadTermExcel(Request $request)
    {

        $bo = null;

        try {

            if (!$request->hasFile('uploadFile')) {
                throw new ApplicationException([], ["uploadFile needs to be specified"]);
            }

            //get the file
            $file = $request->file('uploadFile');

            //Load Excel
            $objPHPExcel = PHPExcel_IOFactory::load($file);

            //Parse Master Sheet
            $masterSheet = $objPHPExcel->getSheet(0);

            $master = [];

            $totalRowsInMainSheet = $this->mainSheetTermMaxRowNum;
            $totalColumnsInDeatilsSheet = $this->detailSheetTermRange;


            for ($row = 1; $row <= $totalRowsInMainSheet; ++$row) {
                $masterRows = $masterSheet->rangeToArray('A' . $row . ':' . 'B' . $row, NULL, TRUE, FALSE);
                array_push($master, $masterRows[0][1]);
            }

            //Parse Details sheet
            $detailSheet = $objPHPExcel->getSheet(1);
            $topRow = $detailSheet->getHighestRow();

            $details = [];

            LOG::debug("Rows found in Details sheet [" . $topRow . "]");


            for ($row = 3; $row <= $topRow; ++$row) {
                $rowData = $detailSheet->rangeToArray('A' . $row . ':' . $totalColumnsInDeatilsSheet . $row, NULL, TRUE, FALSE);
                array_push($details, $rowData[0]);
            }

            //Parse Sellers sheet
            $sellersSheet = $objPHPExcel->getSheet(2);
            $topRow = $sellersSheet->getHighestRow();

            $sellers = [];

            LOG::debug("Rows found in Sellers sheet [" . $topRow . "]");

            for ($row = 2; $row <= $topRow; ++$row) {
                $rowData = $sellersSheet->rangeToArray('A' . $row . ':' . 'A' . $row, NULL, TRUE, FALSE);
                array_push($sellers, $rowData[0]);
            }

            LOG::debug("Converting xls to bo");

            //Transform Excel rows into a BO.
            $bo = $this->serviceFactory->makeTransformer()->term_xls2bo_save($master, $details, $sellers);

            LOG::debug("Converted to bo");

            //Get the service
            $this->postService = $this->serviceFactory->makeService();

            //Set BuyerPost Factory
            $this->postService->setServicefactory($this->serviceFactory);

            //Delegate request to BuyerPostService
            $boSaved = $this->postService->saveOrUpdateTerm($bo);

            LOG::info('Service returned ', (array)$boSaved);

            //Return Response
            return shipres::ok($boSaved);

        } catch (\Exception $e) {

            LOG::error($e->getMessage());
            return $this->errorResponse($e);

        }
    }

    /**
     * Retreive Posts that matches the given criteria
     * @param $criteria
     * @return \Illuminate\Http\JsonResponse
     */
    public function filter(Request $request)
    {
        try {
            $payload = $request->getContent();
            LOG::info("Request is => " . $payload);

            //Convert the request JSON into a BO
            $bo = $this->serviceFactory->makeTransformer()->ui2bo_filter($payload);

            LOG::info('Getting buyerpost service instance');
            $this->postService = $this->serviceFactory->makeService();

            LOG::info('Invoking service instance');

            //Set Security principal
            //$this->postService->setSecurityPrincipal($request->attributes->get("securityPrincipal"));

            //Set sellerPost Factory
            $this->postService->setServicefactory($this->serviceFactory);

            //Delegate request to sellerPostService
            $boSaved = $this->postService->filterPost($bo);

            LOG::info('Service retuned ', (array)$boSaved);

            //Return Response
            return shipres::ok($boSaved);

        } catch (\Exception $e) {

            LOG::error($e->getMessage());
            return $this->errorResponse($e);

        }
    }

    /**
     * Retreive Posts that matches the given criteria
     * @param $criteria
     * @return \Illuminate\Http\JsonResponse
     */
    public function postMasterFilter(Request $request)
    {

        try {

            $payload = $request->getContent();
            LOG::info("Request is => " . $payload);

            //Convert the request JSON into a BO
            $bo = $this->serviceFactory->makeTransformer()->ui2bo_postmaster_filter($payload);

            LOG::info('Getting buyerpost service instance');
            $this->postService = $this->serviceFactory->makeService();

            LOG::info('Invoking service instance');

            //Set sellerPost Factory
            $this->postService->setServicefactory($this->serviceFactory);

            //Delegate request to sellerPostService
            $boSaved = $this->postService->postMasterFilters($bo);

            //Return Response
            return shipres::ok($boSaved);

        } catch (\Exception $e) {

            LOG::error($e->getMessage());
            return $this->errorResponse($e);

        }
    }


    public function postMasterInboundFilter(Request $request)
    {

        try {

            $payload = $request->getContent();
            LOG::info("Request is => " . $payload);

            //Convert the request JSON into a BO
            $bo = $this->serviceFactory->makeTransformer()->ui2bo_postmaster_filter($payload);

            LOG::info('Getting buyerpost service instance');
            $this->postService = $this->serviceFactory->makeService();

            LOG::info('Invoking service instance');

            //Set sellerPost Factory
            $this->postService->setServicefactory($this->serviceFactory);

            //Delegate request to sellerPostService
            $boSaved = $this->postService->filterPostMasterInbound($bo);

            //Return Response
            return shipres::ok($boSaved);

        } catch (\Exception $e) {

            LOG::error($e->getMessage());
            return $this->errorResponse($e);

        }
    }


    /**
     * Retreive All Posts By Current Buyer.
     *
     */
    public function getAllPosts()
    {

        try {
            $this->postService = $this->serviceFactory->makeService();

            //Set sellerPost Factory
            $this->postService->setServicefactory($this->serviceFactory);

            //Delegate request to sellerPostService
            $sp = $this->postService->getAllPosts();

            //Return Response
            return shipres::ok($sp);

        } catch (\Exception $e) {

            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    /**
     * Retreive All Spot Posts By Current Buyer.
     *
     */
    public function getAllSpotPosts()
    {

        try {
            $this->postService = $this->serviceFactory->makeService();

            //Set sellerPost Factory
            $this->postService->setServicefactory($this->serviceFactory);

            //Delegate request to sellerPostService
            $sp = $this->postService->getAllSpotPosts();

            //Return Response
            return shipres::ok($sp);//response()->json($sp);

        } catch (\Exception $e) {

            LOG::error($e->getMessage());
            return $this->errorResponse($e);

        }
    }

    /**
     * Retreive All Term Posts By Current Buyer.
     *
     */
    public function getAllTermPosts()
    {

        try {
            $this->postService = $this->serviceFactory->makeService();

            //Set sellerPost Factory
            $this->postService->setServicefactory($this->serviceFactory);

            //Delegate request to sellerPostService
            $sp = $this->postService->getAllTermPosts();

            //Return Response
            return shipres::ok($sp);

        } catch (Exception $e) {

            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    /**
     * Retreive All Posts By Post Privacy.
     *
     */
    public function getAllPostsByPostPrivacy($postType)
    {

        try {
            $this->postService = $this->serviceFactory->makeService();

            //Set sellerPost Factory
            $this->postService->setServicefactory($this->serviceFactory);

            //Delegate request to sellerPostService
            $sp = $this->postService->getAllPostsByPostPrivacy($postType);

            //Return Response
            return shipres::ok($sp);

        } catch (Exception $e) {

            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }


}
