<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CompanyRequest extends ApiRequest
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
            'entity_type'    => 'required',
            'name_of_business' => 'required',
            'company_registration_no' => 'required',
            'your_name' => 'required',
            'email' => 'required|email',
            'conf_email' => 'required|same:email',
            'password' => 'required',
            'conf_password' => 'required|same:password',
            /*'card_number' => 'required',
            'ccv_number' => 'required',
            'exp_month' => 'required',
            'exp_year' => 'required'*/
        ];
    }
}
