<?php

namespace ApiV2\Services\BlueCollar;

use ApiV2\Model\BlueCollar\SellerRegistration;
use Illuminate\Support\Facades\Crypt;
use Tymon\JWTAuth\Facades\JWTAuth;

class SellerVerificationService extends BaseServiceProvider
{

    public static function getSellerData($request)
    {
        $sellerId = $request->sellerId;
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $sellerData = SellerRegistration::
        with(['vehicleTypes.vehicleType', 'experience', 'qualification', 'curCity', 'curState', 'curDistrict', 'perCity', 'perState', 'perDistrict'])
            ->where('id', '=', Crypt::decrypt($sellerId))
            ->where('verified', '=', 'NO')
            ->first();
        
        foreach($sellerData->qualification as $key=>$val){
          $docDocs = explode('/',$val->document);
          $docTypDocs = explode('.',$docDocs[2]);
          $sellerData->qualification[$key]->doc = $docTypDocs[1];
        }
        
        $adharTypeDocs = explode('/',$sellerData->aadhar_document);
        $adhTypDocs = explode('.',$adharTypeDocs[2]);
        $sellerData->adhDocs = $adhTypDocs[1];

        $licenseTypeDocs = explode('/',$sellerData->licence_doc);
        $licTypDocs = explode('.',$licenseTypeDocs[2]);
        $sellerData->licDocs = $licTypDocs[1];

        $panTypeDocs = explode('/',$sellerData->pan_document);
        $panTypDocs = explode('.',$panTypeDocs[2]);
        $sellerData->panDocs = $panTypDocs[1];

        $rationTypeDocs = explode('/',$sellerData->ration_card_document);
        $ratTypDocs = explode('.',$rationTypeDocs[2]);
        $sellerData->ratDocs = $ratTypDocs[1];

        self::$data['data'] = $sellerData;
        self::$data['success'] = true;
        self::$data['status'] = 200;
        return self::$data;
    }

    public static function getAllUnverified()
    {
        //dd(EncrptionTokenService::token());
        // select('*', function(){
        //   DB::raw('AES_ENCRPT(id, '+EncrptionTokenService::token()+') as eId');
        // })
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $sellerData = SellerRegistration::
        with(['vehicleTypes.vehicleType', 'curCity', 'curState', 'curDistrict', 'perCity', 'perState', 'perDistrict'])
            // ->selectRaw('*, ENCODE(id, ?) as encId', [$userId])
            // ->selectRaw('*, AES_ENCRYPT(id, ?) as encId', [$userId])
            ->where('verified', '=', 'NO')
            ->orderBy('created_at','DESC')
            ->paginate(10);
        self::$data['data'] = $sellerData;
        self::$data['success'] = true;
        self::$data['status'] = 200;
        return self::$data;
    }

    public static function sellerVerify($request)
    {
        $sellerId = $request->sellerId;
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $sellerData = SellerRegistration::
        with('experience', 'qualification', 'curCity', 'curState', 'curDistrict', 'perCity', 'perState', 'perDistrict')
            ->where('id', '=', Crypt::decrypt($sellerId))
            ->first();
        if ($sellerData->verified != 'YES') {

            // $experience = array();
            // $salary = array();
            // $qualification = array();
            // foreach ($sellerData->experience as $e) {
            //   $experience[] = $e->experience;
            //   $salary[] = $e->salary;
            // }
            // $qualification = array();
            // foreach ($sellerData->qualification as $e) {
            //   $qualification[] = $e->qualification;
            // }
            //
            // $vehicleType = array();
            // if($sellerData->vehicle_type!=""&&$sellerData->vehicle_type!=null){
            //   $vehicleType = explode(",", $sellerData->vehicle_type);
            // }
            // $employmentType = array();
            // if($sellerData->employment_type!=""&&$sellerData->employment_type!=null){
            //   $employmentType = explode(",", $sellerData->employment_type);
            // }
            // $solrData = array(
            //   "id" => 'seller_'.$sellerData->id,
            //   "seller_first_name"=> $sellerData->first_name,
            //   "seller_last_name"=> $sellerData->last_name,
            //   "seller_profile_type"=> $sellerData->profile_type,
            //   "seller_city"=> str_replace(' ', '_', $sellerData->curCity->id),
            //   "seller_state"=> str_replace(' ', '_', $sellerData->curState->id),
            //   "seller_district"=> str_replace(' ', '_', $sellerData->curDistrict->id),
            //   "seller_available"=>"true",
            //   "seller_salary"=>$sellerData->current_salary,
            //   "seller_experience"=>$sellerData->total_experience,
            //   "seller_salary_type"=>$sellerData->salary_type
            // );
            //
            // if(!empty($qualification))
            //   $solrData["seller_qualification"]=$qualification;
            // if(!empty($vehicleType))
            //   $solrData["seller_vehicle_type"]=$vehicleType;
            // if(!empty($employmentType))
            //   $solrData["seller_employment_type"]=$employmentType;

            //$response = SolrServices::add('bluecollar', $solrData);
            // if(!isset($response->error)){
            $sellerData->verified = 'YES';
            $sellerData->verified_by = $userId;
            $sellerData->save();
            self::$data['success'] = true;
            self::$data['status'] = 200;
            // }else{
            //   self::$data['success'] = false;
            //   self::$data['status'] = 500;
            // }
        } else {
            self::$data['success'] = false;
            self::$data['status'] = 500;
        }
        return self::$data;
    }

}
