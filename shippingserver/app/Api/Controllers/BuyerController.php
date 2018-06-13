<?php

namespace Api\Controllers;

use Api\Model\IntraHyperBuyerPost;
use Api\Model\IntraHyperBuyerPostTerm;
use Api\Services\LogistiksCommonServices\DocumentServices;
use Api\Services\LogistiksCommonServices\MessageServices;
use Api\Services\LogistiksCommonServices\NumberGeneratorServices;
use Exception;
use Illuminate\Http\Request;
use Log;

class BuyerController extends BaseController
{

    /* Buyer Post Spots */
    public function buyerSpotsPost(Request $request)
    {
        try {
            return IntraHyperBuyerPost::saveBuyerSpotsPost($request);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    public function buyerTermPost(Request $request)
    {
        try {
            return IntraHyperBuyerPostTerm::saveBuyerTermPost($request);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }


    public function buyerSearch(Request $request)
    {
        try {
            return IntraHyperBuyerPostTerm::searchBuyer($request);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }


    /* Count Buyer Post Spots*/
    public function countBuyerPostSpots(Request $request)
    {
        try {
            return IntraHyperBuyerPost::countbuyerpost($request);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    /* For Listing */
    public function buyerlist(Request $request)
    {
        try {
            return IntraHyperBuyerPost::buyerlist($request);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }


    /* For Records */
    public function records(Request $request)
    {
        try {
            return IntraHyperBuyerPost::allrecords($request);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    public function countInboundRecords(Request $request)
    {
        try {
            return IntraHyperBuyerPost::countInboundRecords($request);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }    

    /* For Buyer Filters */
    public function buyerfilter(Request $request)
    {
        try {
            return IntraHyperBuyerPost::buyerFilterSearch($request);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    /* For Buyer Listing According to filters*/
    public function accorindfilter(Request $request)
    {
        try {
            return IntraHyperBuyerPost::filterAccordingSearch($request);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    /* For Buyer Message Data By Id */
    public function messageById(Request $request)
    {
        try {
            return IntraHyperBuyerPost::getMessageDetails($request);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    /* For Buyer Discounts */
    public function getDiscount(Request $request)
    {
        try {
            return IntraHyperBuyerPost::getBuyerDiscount();
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }


    public function getBuyerRouteDetail(Request $request)
    {
        try {
            return IntraHyperBuyerPost::getRouteDetails($request);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }


    public function uploadFiles(Request $request)
    {
        $location = DocumentServices::storeDoc($request, 'intracity/docs/');
    }

    public function sendMessage(Request $request)
    {

        try {
            $message = MessageServices::send($request);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
        return response()->json([
            'payload' => $message,
            'isSuccessful' => true
        ], 200);
    }

    public function retriveFiles($file)
    {
        return DocumentServices::getDoc($file, 'intracity/docs/');
    }

    public function buyerPostDetails(Request $request)
    {
        try {
            return IntraHyperBuyerPost::getbuyerPostDetails($request);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    public function deleteBuyerPost(Request $request)
    {
        try {
            return IntraHyperBuyerPost::deleteBuyerPost($request);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    public function getPostDataById(Request $request, $postId)
    {
        try {
            return IntraHyperBuyerPost::getPostDataById($postId);
        } catch (Exception $e) {
            LOG::error($e->getMessage());
            return $this->errorResponse($e);
        }
    }

    public function generateTxn()
    {
        dd(NumberGeneratorServices::generateTranscationId(new IntraHyperBuyerPost, 3));
    }


}