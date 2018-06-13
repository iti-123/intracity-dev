<?php

namespace Api\Controllers;

use Api\Framework\AbstractSellerPostFactory;
use Api\Requests\BaseShippingResponse as shipres;
use App\Exceptions\ApplicationException;
use Exception;
use Illuminate\Http\Request;
use Log;
use PHPExcel_IOFactory;

//use PHPExcel_IOFactory;


class AbstractSellerPostController extends BaseController
{
    /**
     * @var AbstractSellerPostFactory
     */
    protected $serviceFactory;

    /**
     * Retreive  Posts by Seller Id
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPostsBySeller($id)
    {
        $this->postService = $this->serviceFactory->makeService();

        //TODO Soumya to inject securioty principal object in the middleware.
        //TODO $request->attributes->add(['securityPrincipal' => 'myValue']);

        $this->postService = $this->serviceFactory->makeService();
        //$this->postService->setSecurityPrincipal($request->attributes->get("securityPrincipal"));

        //Set sellerPost Factory
        $this->postService->setServicefactory($this->serviceFactory);

        //Delegate request to sellerPostService
        $sp = $this->postService->getPostById($id);

        //Return Response
        LOG::info('response  from seller Post Service ', (array)$sp);
        return response()->json($sp);
    }

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

            //TODO Soumya to inject securioty principal object in the middleware.
            //TODO $request->attributes->add(['securityPrincipal' => 'myValue']);

            $this->postService = $this->serviceFactory->makeService();
            //$this->postService->setSecurityPrincipal($request->attributes->get("securityPrincipal"));

            //Set sellerPost Factory
            $this->postService->setServicefactory($this->serviceFactory);

            //Delegate request to sellerPostService
            $sp = $this->postService->getPostById($id);

            //Return Response
            LOG::info('response  from seller Post Service ', (array)$sp);
            return response()->json($sp);

        } catch (\Exception $e) {

            LOG::error("Get seller post failed", (array)$e);
            return $this->errorResponse($e);

        }
    }

    /**
     * Save or Update the given Post
     * @param $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveOrUpdate(Request $request)
    {
        try {
            $payload = $request->getContent();

            LOG::info("Request is => " . $payload);

            //Convert the request JSON into a BO
            $bo = $this->serviceFactory->makeTransformer()->ui2bo_save($payload);

            LOG::info('Getting sellerpost service instance');
            $this->postService = $this->serviceFactory->makeService();

            LOG::info('Invoking service instance');

            //Set Security principal
            //$this->postService->setSecurityPrincipal($request->attributes->get("securityPrincipal"));

            //Set sellerPost Factory
            $this->postService->setServicefactory($this->serviceFactory);

            //Delegate request to sellerPostService
            $boSaved = $this->postService->saveOrUpdate($bo);

            LOG::info('Service retuned ', (array)$boSaved);

            //Return Response
            return shipres::ok($boSaved);

        } catch (\Exception $e) {

            LOG::error('Exception while saving Buyer Post ', (array)$e->getMessage());
            return $this->errorResponse($e);

        }
    }


    /**
     * Save or Update the given Post
     * @param $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkSaveOrUpdate(Request $request)
    {
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
            $topRow = $masterSheet->getHighestRow();
            $topColumn = $masterSheet->getHighestColumn();

            $master = [];

            for ($row = 1; $row <= $topRow; ++$row) {
                $masterRows = $masterSheet->rangeToArray('A' . $row . ':' . $topColumn . $row, NULL, TRUE, FALSE);
                array_push($master, $masterRows[0][1]);
            }

            //Parse Details sheet
            $detailSheet = $objPHPExcel->getSheet(1);
            $topRow = $detailSheet->getHighestRow();

            $details = [];

            LOG::debug("Rows found in Details sheet [" . $topRow . "]");

            for ($row = 2; $row <= $topRow; ++$row) {
                $rowData = $detailSheet->rangeToArray('A' . $row . ':' . 'R' . $row, NULL, TRUE, FALSE);
                array_push($details, $rowData[0]);
            }

            //Parse Discounts sheet
            $discountSheet = $objPHPExcel->getSheet(2);
            $topRow = $discountSheet->getHighestRow();

            $discounts = [];

            LOG::debug("Rows found in Discount sheet [" . $topRow . "]");

            for ($row = 2; $row <= $topRow; ++$row) {
                $rowData = $discountSheet->rangeToArray('A' . $row . ':' . 'F' . $row, NULL, TRUE, FALSE);
                array_push($discounts, $rowData[0]);
                //$discounts = $rowData;
            }

            LOG::debug("Converting xls to bo");

            //Transform Excel rows into a BO.
            $bo = $this->serviceFactory->makeTransformer()->xls2bo_save($master, $details, $discounts);

            LOG::debug("Converted to bo");

            //Get the service
            $this->postService = $this->serviceFactory->makeService();

            //Set sellerPost Factory
            $this->postService->setServicefactory($this->serviceFactory);

            //Delegate request to sellerPostService
            $boSaved = $this->postService->saveOrUpdate($bo);

            LOG::info('Service returned ', (array)$boSaved);

            //Return Response
            return shipres::ok($boSaved);

        } catch (\Exception $e) {

            LOG::error('Exception while bulkSaveOrUpdateof SellerPost ', (array)$e->getMessage());
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

            //Convert the request JSON into a BO
            $bo = $this->serviceFactory->makeTransformer()->ui2bo_filter($payload);

            $this->postService = $this->serviceFactory->makeService();

            //Set Security principal
            //$this->postService->setSecurityPrincipal($request->attributes->get("securityPrincipal"));

            //Set sellerPost Factory
            $this->postService->setServicefactory($this->serviceFactory);

            //Delegate request to sellerPostService
            $sp = $this->postService->filterPost($bo);

            LOG::info('response from filterPost ', (array)$sp);

            //Return Response
            return shipres::ok($sp);

        } catch (\Exception $e) {

            LOG::error('Exception while bulkSaveOrUpdateof SellerPost ', (array)$e->getMessage());
            return $this->errorResponse($e);
        }

    }


    /**
     * Retreive All Posts By Current Seller.
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

        } catch (Exception $e) {

            LOG::error('Exception while getAllPosts ', (array)$e->getMessage());
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

            LOG::error('Exception while getAllPostsByPostPrivacy ', (array)$e->getMessage());
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
            LOG::info("Request is -- => " . $payload);

            //Convert the request JSON into a BO
            $bo = $this->serviceFactory->makeTransformer()->ui2bo_postmaster_filter($payload);

            LOG::info('Getting sellerpost service instance');
            $this->postService = $this->serviceFactory->makeService();

            LOG::info('Invoking service instance');

            //Set sellerPost Factory
            $this->postService->setServicefactory($this->serviceFactory);

            //Delegate request to sellerPostService
            $boSaved = $this->postService->postMasterFilters($bo);

            //Return Response
            return shipres::ok($boSaved);


        } catch (\Exception $e) {

            LOG::error('Exception while postMasterFilter ', (array)$e->getMessage());
            return $this->errorResponse($e);
        }
    }

    public function postMasterInbound(Request $request)
    {

        try {
            $payload = $request->getContent();

            LOG::info("Request is => " . $payload);

            //Convert the request JSON into a BO
            $bo = $this->serviceFactory->makeTransformer()->ui2bo_postmasterinbound_filter($payload);
            LOG::info('Getting buyerpost service instance');

            $this->postService = $this->serviceFactory->makeService();

            LOG::info('Invoking service instance');

            //Set sellerPost Factory
            $this->postService->setServicefactory($this->serviceFactory);

            //Delegate request to sellerPostService
            $results = $this->postService->postMasterInbound($bo);

            //Return Response
            return shipres::ok($results);

        } catch (\Exception $e) {

            LOG::error("Postmaster inbound failed", (array)$e->getMessage());
            return $this->errorResponse($e);
        }
    }

}
