<?php

namespace App\Http\Requests;


class AddQuestionRequest extends Request{
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
            'visibility' => 'required',
            'mandatory' => 'required',
            'question' => 'required',
            'explanation' => 'required',
            'mainCategory' => 'required',
            'auditTypes' => 'required',
            'country' => 'required',
            'state' => 'required',
            'cities' => 'required',
            'actionItems' => 'required',
            'license' => 'required',
            'answers' => 'required',

        ];


        return $fields;
    }

}