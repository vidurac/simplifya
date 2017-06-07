<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class AppointmentCreateRequest extends ApiRequest
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
            'to_company_id'     => 'required', //marijuana company
            'from_company_id'   => 'required', //compliance company
            'company_location'  => 'required',
            'assign_to'         => 'required',
            'startDate'         => 'required',
            'amount_cost'       => 'required'
        ];
    }
}