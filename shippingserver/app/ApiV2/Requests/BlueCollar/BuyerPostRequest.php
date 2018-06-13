<?php

namespace ApiV2\Requests\BlueCollar;

use Dingo\Api\Http\FormRequest;
//use Illuminate\Contracts\Validation\Validator;
use Validator;
class BuyerPostRequest extends FormRequest
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

        return [
          'profileType' => 'required|in:DRIVER,CLEANER,SKILLED,SEMISKILLED',
          'location.state_id' => 'required|numeric',
          'location.city_id' => 'required|numeric',
          'location.district_id' => 'required|numeric',
          'vehicleTypes.*' => 'required_if:profileType,DRIVER|array',
          'vehicleTypes.*.id' => 'required_if:accountType,DRIVER|exists:bluecollar_vehicle_machine_types,id',
          'machineTypes.*' => 'required_if:profileType,SKILLED|array',
          'machineTypes.*.id' => 'required_if:accountType,DRIVER|exists:bluecollar_vehicle_machine_types,id',
          'experience' => 'required|numeric|max:50',
          'employmentType.*' => 'required|in:PART_TIME,FULL_TIME,CONTRACT',
          'qualification.*' => 'required|in:SSLC,INTERMEDIATE,GRADUATE,POST_GRADUATE',
          'salary' => 'required|numeric',
          'salaryType' => 'required|in:PER_DAY,PER_WEEK,PER_MONTH',
          'privacy' => 'required|in:PUBLIC,PRIVATE',
          'sellers' => 'required_if:privacy,PRIVATE|array',
          'sellers.*.id' => 'required|exists:bluecollar_seller_registration,id',
          'quotationType' => 'required|in:FIRM,COMPETITIVE'
        ];
    }
}
