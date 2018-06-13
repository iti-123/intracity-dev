<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 4/3/17
 * Time: 1:18 AM
 */

namespace Api\Modules\AirFreight;


use Api\BusinessObjects\AbstractSearchBO;
use Api\BusinessObjects\BuyerPostBO;
use Api\BusinessObjects\SellerPostBO;
use Api\Services\CacheControlService;
use DB;
use Log;


class AirFreightBuyerSellerPostRecommender //implements IBuyerSellerPostRecommender
{
    public function filterSellerInboundPostMaster($sellerId, AbstractSearchBO $filter)
    {
        return $this->_filterSellerInboundPostMaster($sellerId, $filter);
    }

    private function _filterSellerInboundPostMaster($sellerId, FCLSellerPostMasterInboundSearchBO $filter)
    {

        $rebuild = false;

        //Has the user requested for cache validity verification
        if ($filter->cacheControl == "check") {

            //check if the cache has expired.
            if (CacheControlService::isExpired($sellerId, CacheControlService::SELLER_INBOUND_LEADS_ENQUIRIES)) {
                //Recompute seller leads and enquiries

                $rebuild = true;

            }

        } elseif ($filter->cacheControl == "rebuild") {

            $rebuild = true;

        }


        if ($rebuild == true) {

            $this->computeSellerLeadsEnquiries($sellerId);

            LOG::info("Rebuilt seller lead cache for " . $sellerId);

        }


        $baseDataQuery = "
                 select category, if(count(*) = 1, buyer_name, 'Multi') title, 
                        min(last_datetime_for_quote) minLastDateTimeForQuote, 
                        max(last_datetime_for_quote) maxLastDateTimeForQuote,
                        count(*) as countOfPosts,
                        group_concat(distinct concat(buyer_id, '-', buyer_name)) buyer, 
                        group_concat(distinct price_type) priceType,
                        group_concat(distinct post_id) postIds
                 from shp_seller_leads 
                 where service_id = ? and seller_id = ? and spot_term = 1 
                 ";

        $groupBy = " group by category";

        //Setup additional filters as desired.

        $where = null;

        $bindings = [FCL, $sellerId];

        if (count($filter->priceType) == 1 && ($filter->priceType[0] == 1 || $filter->priceType[0] == 2)) {
            //either user has not chosen Negotiation or Firm (not both).
            $where = $where . " and price_type = ?";

            //See how we are not appending the SQL directly, but building a bindings array.
            //This is a best practice to prevent SQL injection attacks.
            array_push($bindings, $filter->priceType[0]);

        }

        if ($filter->cargoReadyDate > 0) {
            //user has supplied a maximum cargo ready date. show all entries less than this date
            $where = $where . " and cargo_ready_date <=  ?";
            array_push($bindings, $filter->cargoReadyDate);
        }


        if ($filter->lastDateForQuoteTime > 0) {
            //user has supplied a maximum lastDateTimeForQuote. show all entries less than this date
            $where = $where . " and last_datetime_for_quote <=  ?";
            array_push($bindings, $filter->lastDateForQuoteTime);
        }

        if (count($filter->buyer) > 0) {
            //user has supplied some buyer filters

            //A beautiful way to solve SQL injection problems with IN Clasue
            $buyerBindings = trim(str_repeat('?,', count($filter->buyer)), ',');

            $where = $where . " and buyer_id in (" . $buyerBindings . ")";
            $bindings = array_merge($bindings, $filter->buyer);
        }

        //Execute the data query

        $dataQuery = $baseDataQuery . $where . $groupBy;
        LOG::debug("Seller Inbound PostMaster Data query and bindings => " . $dataQuery);
        LOG::debug((array)$bindings);

        $rows = DB::select($dataQuery, $bindings);

        $results = new FCLSellerPostMasterInboundSearchResultsBO();

        $buyers = [];
        $priceTypes = [];

        if (count($rows) > 0) {
            //Atleast one row returned.

            foreach ($rows as $row) {

                $buyers = array_merge($buyers, str_getcsv($row->buyer));
                $priceTypes = array_merge($priceTypes, str_getcsv($row->priceType));

                unset($row->buyer);
                unset($row->priceType);

                array_push($results->groups, $row);
            }

            $buyers = array_unique($buyers);
            $priceTypes = array_unique($priceTypes);

            $results->facets["buyer"] = $buyers;
            $results->facets["priceType"] = $priceTypes;

        }

        return $results;

    }

    public function computeSellerLeadsEnquiries($sellerId)
    {

        LOG::info("Computing leads and enquiries for seller [" . $sellerId . "]");

        //Step 1: Delete previous computed leads and enquiries

        $rowsAffected = DB::delete("delete from shp_seller_leads where service_id = ? and seller_id = ? ", [FCL, $sellerId]);

        Log::debug($rowsAffected . " deleted from seller leads cache for serviceId = " . FCL . ", sellerId = " . $sellerId);

        //Step 2 : Insert all private and public posts from all buyers against this seller

        $query1 = "

        insert into shp_seller_leads (service_id,seller_id,buyer_id,buyer_name,post_id,title,price_type,spot_term,lead_enquiry,
                                      last_datetime_for_quote,cargo_ready_date,bid_type,allotments,status,
                                      port_pair,load_port,discharge_port,port_match_type,category)
              select distinct ?, ?, buyerId,buyerName, postId, title, 
                    IF(priceType='Negotiation',1,2) price_type , IF(leadType='spot',1,2) spot_term, 2 lead_enquiry,
                    lastDateTimeForQuote,cargoReadyDate, 0 as bid_type, 0 as allotments,status
                    ,concat(loadPort, '-', dischargePort) as port_pair, loadPort, dischargePort
                    ,3 as port_match_type, 0 as category
              from shp_buyer_post_index
              where isPublic = false and visibleToSellerId = ?
              
              union
              
              select distinct ?, ?, buyerId,buyerName, postId, title, 
                    IF(priceType='Negotiation',1,2) price_type , IF(leadType='spot',1,2) spot_term, 1 lead_enquiry,
                    lastDateTimeForQuote,cargoReadyDate, 0 as bid_type, 0 as allotments,status
                    ,concat(loadPort, '-', dischargePort) as port_pair, loadPort, dischargePort
                    ,3 as port_match_type, 0 as category              
              from shp_buyer_post_index
              where visibleToSellerId = 0 and isPublic = true
              
        ";

        DB::insert($query1, [FCL, $sellerId, $sellerId, FCL, $sellerId]);

        //Step3: Set matching port pairs

        $query2 = "

        update shp_seller_leads set port_match_type = 1 where port_pair in (
           select distinct concat(loadPort, '-', dischargePort)
            from shp_seller_post_index
            where sellerId = ?
              and service_id = ?
        ) and seller_id = ? and service_id = ?


        ";

        DB::update($query2, [$sellerId, FCL, $sellerId, FCL]);

        //Step4: Set matching load ports

        $query3 = "

        update shp_seller_leads set port_match_type = 2 where load_port in (
           select distinct loadPort
            from shp_seller_post_index
            where sellerId = ?
              and service_id = ?
        ) and seller_id = ? and service_id = ? and port_match_type = 3;


        ";

        DB::update($query3, [$sellerId, FCL, $sellerId, FCL]);

        //Step 5: Apply settings
        //Drop unrelated items if not selected for this user.

//        $settingsService = new SettingService();
//        $settingsService->getSettingsById("SellerPostMaster");


        //Step5: Categorise the results
        DB::update("update shp_seller_leads set category = (spot_term * 100) + (port_match_type * 10) + lead_enquiry");


        //Mark cache as rebuilt.
        CacheControlService::markCached($sellerId, CacheControlService::SELLER_INBOUND_LEADS_ENQUIRIES);

    }

    public function computeBuyerLeadsEnquiries($buyerId)
    {

    }

    public function handleBuyerPostTermAdded(BuyerPostBO $bo)
    {


        LOG::info("Handling Buyer Post Term Addition");
        //  NotificationService::notifyBuyerPostTermCreated($bo);
        //0 indicates expire cache for all users.
        CacheControlService::markExpired(0, CacheControlService::SELLER_INBOUND_LEADS_ENQUIRIES);

    }

    public function handleSellerPostAdded(SellerPostBO $bo)
    {

        LOG::info("Handling Seller Post Addition");
        //NotificationService::notifySellerPostCreated($bo);
        //0 indicates expire cache for all users.
        CacheControlService::markExpired(0, CacheControlService::BUYER_INBOUND_LEADS_OFFERS);

    }
}