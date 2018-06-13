<?php

namespace Api\Requests\BlueCollar;

use Dingo\Api\Http\FormRequest;
//use Illuminate\Contracts\Validation\Validator;
use Validator;
use Api\Model\BlueCollar\Quote;
use Tymon\JWTAuth\Facades\JWTAuth;
class BuyerNegotiationRequest extends FormRequest
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
        // Validator::extend('quote_exists',function($attribute, $value, $params, $validator) {
        //     $userID = JWTAuth::parseToken()->getPayload()->get('id');
        //     $quote = Quote::where('buyer_id', '=', $userID)
        //                   ->where('post_id', '=', $value)
        //                   ->count();
        //     if($quote>0){
        //       return true;
        //     }else{
        //       return false;
        //     }
        // });
        return [
          'action' => 'required|in:OFFER,ACCEPT,DENY',
          'quoteId' => 'required|exists:intra_hp_post_quotations,id',
          'postId' => 'required|exists:bluecollar_posts,id',
          'quoteDays' => 'required_if:action,OFFER|numeric',
          'quotePrice' => 'required_if:action,OFFER&quotationType,COMPETITIVE|numeric',
        ];
    }
}
