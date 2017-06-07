<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class EditInviteEmployeeRequest extends ApiRequest
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
            "name" => 'required',
            "email_address" => 'required',
            "permission" => 'required',
            "company_id" => 'required'
        ];
    }
}
