<?php

namespace ApiV2\Services\LogistiksCommonServices;

use Log;
use ApiV2\Model\BlueCollar\SellerRegExperience;
use ApiV2\Model\BlueCollar\SellerRegQualif;
use ApiV2\Model\BlueCollar\SellerRegistration;
use ApiV2\Services\BlueCollar\BaseServiceProvider;
use Tymon\JWTAuth\Facades\JWTAuth;
use Storage;
use App\UserMessageUpload;

class SolrServices extends BaseServiceProvider{

    
  // protected static $solr_base_url = "http://localhost:8983/solr/";

    public function __construct() {

      
      
    }

    private static function getSolrUrl() {
        if($_SERVER['HTTP_HOST'] == '115.124.98.243') {
            return  "http://115.124.98.243:8983/solr/";
        } else {
            return "http://localhost:8983/solr/";
        }
    }

    public static function add($core, $data) {
      $ch = curl_init(self::getSolrUrl().$core."/update?wt=json");
      $data = array(
          "add" => array(
              "doc" => $data,
              "commitWithin" => 1000,
              "overwrite"=> true
          ),
      );
      $data_string = json_encode($data, true);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_POST, TRUE);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
      $response = curl_exec($ch);
      return json_decode($response);
  }

  // Solr Search Service

  public static function search($core, $q, $start=0, $rows=20) {
        $url = self::getSolrUrl().$core."/select?wt=json";
      $url = $url . "&start=" . $start;
      $url = $url . "&rows=" . $rows;
      $url = $url . "&q=" . urlencode($q);
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_POST, TRUE);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
      $response = curl_exec($ch);
      return json_decode($response);

  }

  // public static function json_split_objects($json){
  //     $q = FALSE;
  //     $len = strlen($json);
  //     for($l=$c=$i=0;$i<$len;$i++)
  //     {
  //         $json[$i] == '"' && ($i>0?$json[$i-1]:'') != '\\' && $q = !$q;
  //         if(!$q && in_array($json[$i], array(" ", "\r", "\n", "\t"))){continue;}
  //         in_array($json[$i], array('{', '[')) && !$q && $l++;
  //         in_array($json[$i], array('}', ']')) && !$q && $l--;
  //         (isset($objects[$c]) && $objects[$c] .= $json[$i]) || $objects[$c] = $json[$i];
  //         $c += ($l == 0);
  //     }
  //     return $objects;
  // }
  public static function json_split_objects($s, $assoc = false, $depth = 512, $options = 0) {
    if(substr($s, -1) == ',')
        $s = substr($s, 0, -1);
    return json_decode("[$s]", $assoc, $depth, $options);
  }

}
