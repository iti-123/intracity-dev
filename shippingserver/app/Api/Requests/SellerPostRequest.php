<?php

namespace Api\Requests;

use Dingo\Api\Http\FormRequest;

class SellerPostRequest extends FormRequest
{
    public static function rules()
    {

        return unserialize(SELLER_RATE_CARD_RULES);;
    }

    public function authorize()
    {
        return true;
    }
}