<?php

namespace ApiV2\Controllers;


use ApiV2\Model\IntraHyperSellerPost;
use ApiV2\Modules\Intracity\IntracityPostSearch;
use Exception;
use Illuminate\Http\Request;
use ApiV2\Services\LogistiksCommonServices\NegotationServices;

class SellerController extends BaseController
{

    /* Seller Rate Cart */
    public function sellerRateCartPost(Request $request)
    {

        try {
            return IntraHyperSellerPost::sellerPost($request);
        } catch (Exception $e) {
            //LOG::error($e->getMessage());
            return $this->errorResponse($e);

        }
    }

    public function sellerDraftRateCartPost(Request $request)
    {
        try {
            return IntraHyperSellerPost::sellerDraftPosts($request);
        } catch (Exception $e) {
            //LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    public function buyerPostSearchIntra(Request $request)
    {

        $data = json_decode($request->getContent());

        $searchService = new IntracityPostSearch();

        $result = $searchService->buyerSearch($data);
        
        return response()->json($result);
        
    }

    public function countSellerPostSpots(Request $request)
    {

        try {
            return IntraHyperSellerPost::sellerPostCount();
        } catch (Exception $e) {

            return $this->errorResponse($e);
        }
    }

    public function sellerPostLists(Request $request)
    {

        try {
            return IntraHyperSellerPost::sellerPostLists($request);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function sellerLeadsDetails(Request $request)
    {
        try {
            return IntraHyperSellerPost::sellerLeadsDetails($request);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function getSellerdetailsById(Request $request)
    {
        try {
            return IntraHyperSellerPost::getdetailsById($request->sellerid);

        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function messageById(Request $request)
    {
        try {
            return IntraHyperSellerPost::getMessageById($request);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    /*******seller post details**********************/
    public function sellerPostDetails(Request $request)
    {
        try {
            return IntraHyperSellerPost::getPostDetails($request);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }

    }

    public function sellerPostDraftDetails(Request $request)
    {
        try {
            return IntraHyperSellerPost::getPostDraftDetails($request);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }

    }

    /********************seller post Delete *************/
    public function sellerPostDelete(Request $request)
    {

        try {
            return IntraHyperSellerPost::deleteSellerPost($request);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    /***************seller post discount details **********/
    public function sellerPostDiscount(Request $request)

    {

        try {
            return IntraHyperSellerPost::getDiscountDetails($request);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }

    }

    /********* seller prediscount***************/
    public function sellerPreDiscount(Request $request)

    {

        try {
            return IntraHyperSellerPost::getPreDetails($request);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }

    }

    /**********seller final discount or discount of all route********/
    public function sellerFinalDiscount(Request $request)
    {

        try {
            return IntraHyperSellerPost::getFinalDetails($request);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }

    }

    public function sellerQuoteAction(Request $request)
    {
        try {
            return NegotationServices::sellerQuoteAction($request);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }

    }

}
