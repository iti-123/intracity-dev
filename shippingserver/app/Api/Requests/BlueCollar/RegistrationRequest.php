<?php

namespace Api\Requests\BlueCollar;

use Dingo\Api\Http\FormRequest;
use Validator;

//use Illuminate\Contracts\Validation\Validator;

class RegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
//        Validator::extend('is_image', function ($attribute, $value, $params, $validator) {
//            $value = explode(',', $value);
//            $image = base64_decode($value[1]);
//            $f = finfo_open();
//            $result = finfo_buffer($f, $image, FILEINFO_MIME_TYPE);
//            return $result == 'image/png' || $result == 'image/jpg' || $result == 'image/jpeg';
//        });

        return [
            'accountType' => 'required|in:DRIVER,CLEANER,SKILLED,SEMISKILLED',
            'firstName' => 'required|min:2',
            'lastName' => '',
            'bg' => 'required|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'dob' => 'required|date',
            'currentSalary' => 'required|numeric',
            'totalExperience' => 'required|numeric',
            'salaryType' => 'required|in:PER_DAY,PER_WEEK,PER_MONTH',
            'currentAddress.address' => 'required',
            'currentAddress.street' => 'required',
            'currentAddress.locality' => 'required',
            'currentAddress.pincode' => 'required|min:6|max:6|regex:/^[0-9]+$/',
            'currentAddress.city.city_id' => 'required|exists:lkp_cities,id',
            'currentAddress.city.district_id' => 'required|exists:lkp_districts,id',
            'currentAddress.city.state_id' => 'required|exists:lkp_states,id',
            'currentAddress.landline' => 'min:6|regex:/^[0-9]+$/',
            'currentAddress.mobile' => 'required|min:10|regex:/^[0-9]+$/',
            'currentAddress.mobile2' => 'min:10|regex:/^[0-9]+$/',
            'permanentAddress.address' => '',
            'permanentAddress.street' => '',
            'permanentAddress.locality' => '',
            'permanentAddress.pincode' => 'min:6|max:6|regex:/^[0-9]+$/',
            'permanentAddress.city.city_id' => 'exists:lkp_cities,id',
            'permanentAddress.city.district_id' => 'exists:lkp_districts,id',
            'permanentAddress.city.state_id' => 'exists:lkp_states,id',
            'permanentAddress.landline' => 'min:6|regex:/^[0-9]+$/',
            'permanentAddress.mobile' => 'min:10|regex:/^[0-9]+$/',
            'permanentAddress.mobile2' => 'min:10|regex:/^[0-9]+$/',
            'ids.email' => 'required|email|unique:bluecollar_seller_registration,email',
            'ids.adhar' => 'required|min:12|max:12|regex:/^[0-9]+$/',
            'ids.pan' => 'required|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/',
            'ids.ration' => 'required|min:12|max:12|regex:/^[0-9]+$/',
            'ids.adharDoc.doc' => 'required',
            'ids.panDoc.doc' => 'required',
            'ids.adharDoc.doc' => 'required',
            'licence.no' => 'required_if:accountType,DRIVER|min:13|regex:/^[A-Z]{2}[0-9]{11}$/',
            'licence.validFrom' => 'required_if:accountType,DRIVER|date',
            'licence.validTo' => 'required_if:accountType,DRIVER|date',
            'licence.transportEndorsement' => 'required_if:accountType,DRIVER',
            'licence.licenceDoc.doc' => 'required_if:accountType,DRIVER',
            'vehicleTypes' => 'required_if:accountType,DRIVER|array',
            'vehicleTypes.*.id' => 'required_if:accountType,DRIVER|exists:bluecollar_vehicle_machine_types,id',
            'machineTypes' => 'required_if:accountType,SKILLED|array',
            'machineTypes.*.id' => 'required_if:accountType,SKILLED|exists:bluecollar_vehicle_machine_types,id',
            'qualifications' => 'required|array',
            'qualifications.*.qualification' => 'required',
            'qualifications.*.board' => 'required',
            'qualifications.*.university' => 'required',
            'qualifications.*.city' => 'required',
            'qualifications.*.percentage' => 'required|regex:/^[0-9]{2}(\.[0-9]{1,2})?$/',
            'qualifications.*.doc.doc' => 'required',
            'healthPol.status' => 'boolean',
            'medicalPol.status' => 'boolean',
            'lic.status' => 'boolean',
            'experience' => 'required_if:accountType,DRIVER|array',
            'experience.*.vehicleType' => 'required',
            'experience.*.experience' => 'required|numeric|max:55',
            'experience.*.employerName' => 'required',
            'experience.*.location' => 'required',
            'experience.*.salary' => 'required|numeric',
            'languages' => 'required|array',
            'languages.*.language' => 'required',
            'languages.*.speak' => 'required|boolean',
            'languages.*.read' => 'required|boolean',
            'languages.*.write' => 'required|boolean',
            'employmentType.*' => 'required|in:FULL_TIME,PART_TIME,CONTRACT',

        ];
    }
}
