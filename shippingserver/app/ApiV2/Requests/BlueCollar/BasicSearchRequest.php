<?php

namespace ApiV2\Requests\BlueCollar;

use Dingo\Api\Http\FormRequest;

//use Illuminate\Contracts\Validation\Validator;

class BasicSearchRequest extends FormRequest
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
            'vehicleType' => 'required_if:profileType,DRIVER|in:BIKE,LMV,MMV,HMV',
            'experience' => 'required|numeric|max:50',
            'employmentType' => 'required|in:PART_TIME,FULL_TIME,CONTRACT'
        ];
    }
}
