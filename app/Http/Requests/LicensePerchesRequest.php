<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class LicensePerchesRequest extends ApiRequest
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
            'location_id' => 'required',
            'license_id' => 'required',
            'license_no' => 'required'
        ];
    }
}
