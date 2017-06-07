<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class InspectionSaveRequest extends ApiRequest
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
            'start_time'          => 'required',
            'end_time'            => 'required',
            'start_latitude'      => 'required',
            'start_longitude'     => 'required',
            'finish_latitude'     => 'required',
            'finish_longitude'    => 'required',
            'appointment_id'      => 'required'
        ];
    }
}
