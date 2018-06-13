<?php

namespace Api\Requests\Hyperlocal;

use Dingo\Api\Http\FormRequest;

//use Illuminate\Contracts\Validation\Validator;

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
            //'city' => 'required',
            //'hp_depart_date' => 'required',
            //'hp_servicetype' => 'required|in:1,2,3',
            // 'hp_fragile' => 'required|in:0,1',
            // 'hp_category' => 'required|in:1,2,3,4',
            // 'from_location' => 'required',
            // 'to_location' => 'required',
            // 'hp_max_weight' => 'required|min:1|max:18|regex:/^[0-9]+$/',
            // 'hp_max_no_parcel' => 'required|min:1|max:18|regex:/^[0-9]+$/',

        ];
    }
}
