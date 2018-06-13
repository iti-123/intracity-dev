<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 2/15/17
 * Time: 12:53 AM
 */

namespace Api\Services;

use App\Exceptions\ApplicationException;
use Log;

class SolrSearchService implements ISearchService
{
    public function deltaImport($entity)
    {

        $url = env('SOLR_BASE_URL') . '/dataimport';

        LOG::info("delta importing " . $entity);

        $ch = curl_init();

        $postvar = "command=delta-import&verbose=false&clean=false&commit=true&optimize=false&core=logistiksdev&entity=" . $entity;

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postvar);

        $response = curl_exec($ch);
        LOG::info("Response from  solr after delta importing " . $entity);
        Log::info($response);

        /*$jsonResponse = json_decode($response);

        if ( $jsonResponse->responseHeader->status  != 0) {
            LOG:info("Failed delta importing to SOLR. See responses above. Hint Solr Status => " . $jsonResponse->responseHeader->status);
            throw new ApplicationException([], [ "Failed adding document(s) to SOLR Store" ]);
        }*/
    }

    public function add($doc)
    {

        $url = env('SOLR_BASE_URL') . '/update/json/docs?commit=true';

        LOG::info("adding document to SOLR");
        LOG::debug((array)$doc);

        //dd($doc);

        $ch = curl_init();

        $json = json_encode($doc);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

        $response = curl_exec($ch);
        Log::info($response);

        $jsonResponse = json_decode($response);

        if ($jsonResponse->responseHeader->status != 0) {
            LOG:
            info("Failed posting to SOLR. See responses above. Hint Solr Status => " . $jsonResponse->responseHeader->status);
            throw new ApplicationException([], ["Failed adding document(s) to SOLR Store"]);
        }

        /*if ( curl_getinfo($ch, CURLINFO_HTTP_CODE)  != 200 ) {

            LOG:info("Finished posting to SOLR. Response => " . $response);

            throw new ApplicationException([], [ "Failed adding document(s) to SOLR Store" ]);

        }*/


    }

    public function remove($query)
    {

        $url = env('SOLR_BASE_URL') . '/update?commitWithin=1000&overwrite=true&wt=json';

        LOG::info("removing documents from SOLR");

        $ch = curl_init($url);

        //curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        Log::info("URL => " . $url);
        Log::info($query);
        $response = curl_exec($ch);

        Log::info($response);

        $jsonResponse = json_decode($response);

        if ($jsonResponse->responseHeader->status != 0) {
            LOG:
            info("Failed removing from SOLR. See responses above. Hint Solr Status => " . $jsonResponse->responseHeader->status);
            throw new ApplicationException([], ["Failed removing document(s) to SOLR Store"]);
        }
        /*if ( curl_getinfo($ch, CURLINFO_HTTP_CODE)  != 200 ) {

            LOG:info("Finished removing from SOLR. Response => " . $response);

            throw new ApplicationException([], [ "Failed removing document(s) from SOLR Store" ]);

        }*/


    }


    public function search($q, $fq, $facets, $start = 0, $rows = 100, $sortFields = "", $groups = [])
    {

        $url = env('SOLR_BASE_URL') . '/select?wt=json';

        LOG::info("searching documents from SOLR");

        $url = $url . "&start=" . $start;

        $url = $url . "&rows=" . $rows;

        if ($sortFields != "") {
            $url = $url . "&sort=" . $sortFields;
        }

        if (count($groups) > 0) {
            $url = $url . "&group=true";
            foreach ($groups as $gf) {
                $url = $url . "&group.field=" . $gf;
            }
        }

        if (!isset($q)) {
            //No query specified. search against all documents
            $q = "*:*";
        }


        $url = $url . "&q=" . urlencode($q);

        if (isset($fq)) {
            $url = $url . "&fq=" . urlencode($fq);
        }


        //Build facet query
        if (count($facets) > 0) {

            $url = $url . "&facet=true";

            $facetFields = $facets["fields"];
            if (count($facetFields) > 0) {
                foreach ($facetFields as $ff) {
                    $url = $url . "&facet.field=" . $ff;
                }
            }

            $facetRanges = $facets["ranges"];
            if (count($facetRanges) > 0) {
                foreach ($facetRanges as $fr) {
                    $url = $url . "&facet.range=" . $fr;
                }
            }
        }

        LOG::info("Searching [" . $url . "]");

        $ch = curl_init($url);

        //curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));

        $response = curl_exec($ch);

        Log::info($response);

        $jsonResponse = json_decode($response);

        if ($jsonResponse->responseHeader->status != 0) {
            LOG:
            info("Failed searching SOLR. See responses above. Hint Solr Status => " . $jsonResponse->responseHeader->status);
            throw new ApplicationException([], ["Failed searching document(s) from SOLR Store"]);
        }

        return $response;

    }

}
