<?php

namespace Api\Services\BlueCollar;

use Api\Model\BlueCollar\SellerRegExperience;
use Api\Model\BlueCollar\SellerRegistration;
use Api\Model\BlueCollar\SellerRegQualif;
use Api\Model\BlueCollar\SellerRegVehMach;
use Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class SellerRegistrationService extends BaseServiceProvider
{

    public static function register($request)
    {
        $userId = JWTAuth::parseToken()->getPayload()->get('id');
        $seller = new SellerRegistration();

        $seller->profile_type = $request->accountType;
        $seller->created_by = $userId;
        $seller->first_name = $request->firstName;
        $seller->last_name = $request->lastName;
        $seller->blood_group = $request->bg;
        $seller->date_of_birth = $request->dob;
        $seller->email = $request->ids['email'];
        $seller->pan_no = $request->ids['pan'];
        $seller->pan_document = /*self::storeDoc*/($request->ids['panDoc']['doc']);
        $seller->aadhar_no = $request->ids['adhar'];
        $seller->aadhar_document = /*self::storeDoc*/($request->ids['adharDoc']['doc']);
        $seller->ration_card_no = $request->ids['ration'];
        $seller->ration_card_document = /*self::storeDoc*/($request->ids['rationDoc']['doc']);
        $seller->cur_house_no = $request->currentAddress['address'];
        $seller->cur_street = $request->currentAddress['street'];
        $seller->cur_locality = $request->currentAddress['locality'];
        $seller->cur_state_id = $request->currentAddress['city']['state_id'];
        $seller->cur_city_id = $request->currentAddress['city']['city_id'];
        $seller->cur_district_id = $request->currentAddress['city']['district_id'];
        $seller->cur_pincode = $request->currentAddress['pincode'];
        $seller->cur_landline = $request->currentAddress['landline'];
        $seller->cur_mobile = $request->currentAddress['mobile'];
        $seller->cur_alt_mobile = $request->currentAddress['mobile2'];
        if ($request->permanentAddress['address'] != '') {
            $seller->per_house_no = $request->permanentAddress['address'];
            $seller->per_street = $request->permanentAddress['street'];
            $seller->per_locality = $request->permanentAddress['locality'];
            $seller->per_state_id = $request->permanentAddress['city']['state_id'];
            $seller->per_city_id = $request->permanentAddress['city']['city_id'];
            $seller->per_district_id = $request->permanentAddress['city']['district_id'];
            $seller->per_pincode = $request->permanentAddress['pincode'];
            $seller->per_landline = $request->permanentAddress['landline'];
            $seller->per_mobile = $request->permanentAddress['mobile'];
            $seller->per_alt_mobile = $request->permanentAddress['mobile2'];
        }
        if ($request->accountType == 'DRIVER' || $request->accountType == 'SKILLED') {
            $seller->licence_no = $request->licence['no'];
            $seller->licence_transport_endorsement = $request->licence['transportEndorsement'];
            $seller->licence_state = $request->licence['state'];
            $seller->licence_valid_from = $request->licence['validFrom'];
            $seller->licence_valid_to = $request->licence['validTo'];
            // $seller->vehicle_type = self::allTypes($request->vehicleTypes);
            $seller->licence_doc = /*self::storeDoc*/($request->licence['licenceDoc']['doc']);

        }

        if ($request->lic['status'] instanceOf boolean) {
            $seller->lic_policy = $request->lic['status'];
        }
        if ($request->medicalPol['status'] instanceOf boolean) {
            $seller->medical_policy = $request->medicalPol['status'];
        }
        if ($request->healthPol['status'] instanceOf boolean) {
            $seller->health_policy = $request->healthPol['status'];
        }

        // $languages = '';
        // foreach ($request->languages as $key => $value) {
        //   $languages .= $value;
        // }
        $seller->languages = json_encode($request->languages);
        foreach ($request->employmentType as $key => $value) {
            if (isset($seller->employment_type)) {
                $seller->employment_type .= ',';
            }
            $seller->employment_type .= $value;
        }

        $seller->salary_type = $request->salaryType;
        $seller->current_salary = $request->currentSalary;
        $seller->total_experience = $request->totalExperience;
        $seller->save();

        foreach ($request->qualifications as $q) {
            $sellerQul = new SellerRegQualif();
            $sellerQul->qualification = $q['qualification'];
            $sellerQul->board = $q['board'];
            $sellerQul->institution = $q['university'];
            $sellerQul->state_or_city = $q['city'];
            $sellerQul->percentage = $q['percentage'];
            $sellerQul->Document = /*self::storeDoc*/($q['doc']['doc']);
            $seller->qualification()->save($sellerQul);
        }

        if ($request->accountType == 'DRIVER' || $request->accountType == 'SKILLED') {
            foreach ($request->experience as $e) {
                $sellerExp = new SellerRegExperience();
                $sellerExp->vehicle_type = $e['vehicleType'];
                $sellerExp->experience = $e['experience'];
                $sellerExp->employer_name = $e['employerName'];
                $sellerExp->location = $e['location'];
                $sellerExp->salary = $e['salary'];
                $seller->experience()->save($sellerExp);
            }
        }

        if ($request->accountType == 'DRIVER' || $request->accountType == 'SKILLED') {
            if ($request->accountType == 'DRIVER') {
                $vmType = $request->vehicleTypes;
            } else {
                $vmType = $request->machineTypes;
            }
            foreach ($vmType as $key => $value) {
                $vehicles = new SellerRegVehMach();
                $vehicles->vm_id = $value['id'];
                $vehicles->vm_id = $value['id'];
                $seller->vehicleTypes()->save($vehicles);
            }

        }

        self::$data['success'] = true;
        self::$data['status'] = 200;
        return self::$data;

    }

    public static function storeDoc($file)
    {
        $file = explode(',', $file);
        $image = base64_decode($file[1]);
        $f = finfo_open();
        $result = finfo_buffer($f, $image, FILEINFO_MIME_TYPE);
        $filePath = 'bluecollar/docs/' . uniqid();
        if ($result == 'image/jpg' || $result == 'image/jpeg') {
            $filePath .= '.jpg';
        } else {
            $filePath .= '.png';
        }
        Storage::disk('local')->put($filePath, $image);
        return $filePath;
    }

    public static function allTypes($vehicleTypes)
    {
        $types = '';
        foreach ($vehicleTypes as $i => $t) {
            if ($i == 0) {
                $types = $t;
            } else {
                $types .= ',' . $t;
            }
        }
        return $types;
    }

}
