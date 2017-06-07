<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class SubscriptionRequest extends ApiRequest
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
            'subscription_name' => 'required',
            'validity_period'   => 'required',
            'company_type'      => 'required',
            'price'             => 'required',
        ];
    }
}
