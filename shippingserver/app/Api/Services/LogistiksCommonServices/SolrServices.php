<?php

namespace Api\Services\LogistiksCommonServices;

use Log;
use Api\Model\BlueCollar\SellerRegExperience;
use Api\Model\BlueCollar\SellerRegQualif;
use Api\Model\BlueCollar\SellerRegistration;
use Api\Services\BlueCollar\BaseServiceProvider;
use Tymon\JWTAuth\Facades\JWTAuth;
use Storage;
use App\UserMessageUpload;

class SolrServices extends BaseServiceProvider{


<<<<<<< HEAD
 // protected static $solr_base_url = "http://115.124.98.243:8983/solr/";
   protected static $solr_base_url = "http://localhost:8983/solr/";
=======
    protected static $solr_base_url = "http://115.124.98.243:8983/solr/";
 // protected static $solr_base_url = "http://localhost:8983/solr/";
>>>>>>> 47a6a719c728a73b32f742ffc01939b386364a6b
 // protected static $solr_base_url = "http://192.168.1.251:8983/solr/";


  public static function add($core, $data) {
      $ch = curl_init(self::$solr_base_url.$core."/update?wt=json");
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

      $url = self::$solr_base_url.$core."/select?wt=json";
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
