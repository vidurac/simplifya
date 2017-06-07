<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CouponCreateRequest extends ApiRequest
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
            'code'                      => 'required',
            'description'               => 'required',
            'start_date'                => 'required',
            'end_date'                  => 'required',
            'master_subscription_id'    => 'required',
            'coupon_details'            => 'required'
        ];
    }
}
