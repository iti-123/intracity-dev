<?php

namespace Api\Requests\Message;

use Dingo\Api\Http\FormRequest;

//use Illuminate\Contracts\Validation\Validator;

class MessageRequest extends FormRequest
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
        // Validator::extend('is_image',function($attribute, $value, $params, $validator) {
        //     $value = explode(',', $value);
        //     $image = base64_decode($value[1]);
        //     $f = finfo_open();
        //     $result = finfo_buffer($f, $image, FILEINFO_MIME_TYPE);
        //     return $result == 'image/png'||$result == 'image/jpg'||$result == 'image/jpeg';
        // });
        return
            [
                'itle' => 'required'
            ]
        // return [
        //     'accountType' => 'required|in:DRIVER,CLEANER,SKILLED_OPERATOR,UNSKILLED_OPERATOR',
        //     'firstName' => 'required|min:2',
        //     'lastName'  => '',
        //     'bg' => 'required|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
        //     'dob' => 'required|date',
        //     'currentAddress.address' => 'required',
        //     'currentAddress.street' => 'required',
        //     'currentAddress.locality' => 'required',
        //     'currentAddress.pincode' => 'required|min:6|max:6|regex:/^[0-9]+$/',
        //     'currentAddress.city' => 'required|exists:lkp_cities,id',
        //     'currentAddress.landline' => 'required|min:6|regex:/^[0-9]+$/',
        //     'currentAddress.mobile' => 'required|min:10|regex:/^[0-9]+$/',
        //     'currentAddress.mobile2' => 'required|min:10|regex:/^[0-9]+$/',
        //     'permanentAddress.address' => '',
        //     'permanentAddress.street' => '',
        //     'permanentAddress.locality' => '',
        //     'permanentAddress.pincode' => 'min:6|max:6|regex:/^[0-9]+$/',
        //     'permanentAddress.city' => 'exists:lkp_cities,id',
        //     'permanentAddress.landline' => 'min:6|regex:/^[0-9]+$/',
        //     'permanentAddress.mobile' => 'min:10|regex:/^[0-9]+$/',
        //     'permanentAddress.mobile2' => 'min:10|regex:/^[0-9]+$/',
        //     'ids.email' => 'required|email|unique:bluecollar_seller_registration,email',
        //     'ids.adhar' => 'required|min:12|max:12|regex:/^[0-9]+$/',
        //     'ids.pan' => 'required|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/',
        //     'ids.ration' => 'required|min:12|max:12|regex:/^[0-9]+$/',
        //     'ids.adharDoc' => 'required|is_image',
        //     'ids.panDoc' => 'required|is_image',
        //     'ids.adharDoc' => 'required|is_image',
        //     'licence.no' => 'required_if:accountType,DRIVER|min:13|numeric',
        //     'licence.validFrom' => 'required_if:accountType,DRIVER|date',
        //     'licence.validTo' => 'required_if:accountType,DRIVER|date',
        //     'licence.transportEndorsement' => 'required_if:accountType,DRIVER',
        //     'licence.licenceDoc' => 'required_if:accountType,DRIVER|is_image',
        //     'vehicleTypes' => 'required_if:accountType,DRIVER|array',
        //     'vehicleTypes.*' => 'required_if:accountType,DRIVER|in:BIKE,LMV,HMV,MMV',
        //     'qualifications' => 'required|array',
        //     'qualifications.*.qualification' => 'required',
        //     'qualifications.*.board' => 'required',
        //     'qualifications.*.university' => 'required',
        //     'qualifications.*.city' => 'required',
        //     'qualifications.*.percentage' => 'required|regex:/^[0-9]{2}(\.[0-9]{1,2})?$/',
        //     'qualifications.*.doc' => 'required|is_image',
        //     'healthPol.status' => 'boolean',
        //     'medicalPol.status' => 'boolean',
        //     'lic.status' => 'boolean',
        //     'experience' => 'required_if:accountType,DRIVER|array',
        //     'experience.*.vehicleType' => 'required',
        //     'experience.*.experience' => 'required|numeric|max:50',
        //     'experience.*.employerName' => 'required',
        //     'experience.*.location' => 'required',
        //     'experience.*.salary' => 'required|numeric',
        //     'languages' => 'required|array',
        //     'languages.*.language' => 'required',
        //     'languages.*.speak' => 'required|boolean',
        //     'languages.*.read' => 'required|boolean',
        //     'languages.*.write' => 'required|boolean',
        //     'employmentType.*' => 'required|in:FULL_TIME,PART_TIME,CONTRACT',
        // ];
    }
}
