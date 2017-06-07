<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ActionCommentApiRequest extends ApiRequest
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
            'comment'    => 'required',
            'appointment_id' => 'required',
            'action_id' => 'required',
            'entity_tag' => 'required'
        ];
    }
}
