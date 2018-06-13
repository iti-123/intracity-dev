<?php

namespace Api\Requests\BlueCollar;

use Dingo\Api\Http\FormRequest;

//use Illuminate\Contracts\Validation\Validator;

class SellerSearchRequest extends FormRequest
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
            'location' => 'required',
            'vehicleType' => 'array',
            'machineType' => 'array',
            'experience.min' => 'required|numeric|max:50',
            'experience.max' => 'required|numeric|max:50',
            'employmentType.*' => 'required|in:PART_TIME,FULL_TIME,CONTRACT',
            'qualification.*' => 'required|in:SSLC,INTERMEDIATE,GRADUATE,POST_GRADUATE',
            'rating.*' => 'required|in:EXCELLENT,GOOD,AVERAGE',
            'salary.min' => 'required|numeric',
            'salary.max' => 'required|numeric',
        ];
    }
}
