<?php

namespace App\Http\Requests;


class CreateInspectionRequest extends Request{
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

        $fields =  [
            'company_name' => 'required',
            'company_location' => 'required',
            'message' => 'required',
        ];


        return $fields;
    }

}