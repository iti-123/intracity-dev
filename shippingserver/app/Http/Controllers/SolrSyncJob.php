<?php

namespace App\Http\Controllers;

class SolrSyncJob extends Controller
{
    private $curlUrl = 'http://logistiks.solr.techwave.net:80/solr/phani-logistiks-shipping/update/json/docs?commit=true';
    //private $curlUrl='http://logistiks.solr.techwave.net/solr/phani-logistiks-shipping/';
    private $curlObj;

    function syncToSolr($rawData)
    {
        $data = array(
            "add" => array(
                "doc" => array(
                    "id" => "HW2212",
                    "title" => "Hello World 2"
                ),
                "commitWithin" => 1000,
            ),
        );
        $data_string = json_encode($data);
        curl_setopt($this->curlObj, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($this->curlObj, CURLOPT_POST, TRUE);
        curl_setopt($this->curlObj, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($this->curlObj, CURLOPT_POSTFIELDS, $data_string);
        $response = curl_exec($this->curlObj);
    }
}