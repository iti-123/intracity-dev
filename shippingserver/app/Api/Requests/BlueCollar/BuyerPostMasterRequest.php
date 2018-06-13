<?php

namespace Api\Requests\BlueCollar;

use Dingo\Api\Http\FormRequest;

//use Illuminate\Contracts\Validation\Validator;

class BuyerPostMasterRequest extends FormRequest
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
            'profileType.*' => 'in:DRIVER,CLEANER,SKILLED,SEMISKILLED',
            'location.city_id' => 'numeric',
            'location.state_id' => 'numeric',
            'location.district_id' => 'numeric',
            'vehicleType.*' => 'exists:bluecollar_vehicle_machine_types,id',
            'machineType.*' => 'exists:bluecollar_vehicle_machine_types,id',
            'employmentType.*' => 'in:PART_TIME,FULL_TIME,CONTRACT',
            'qualification.*' => 'in:SSLC,INTERMEDIATE,GRADUATE,POST_GRADUATE',
            'status.*' => 'in:ACTIVE,INACTIVE,DELETED',
            'salaryType.*' => 'in:PER_DAY,PER_WEEK,PER_MONTH',
        ];
    }
}
