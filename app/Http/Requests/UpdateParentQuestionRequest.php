<?php

namespace App\Http\Requests;


class UpdateParentQuestionRequest extends Request{
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
            'questionId' => 'required',
            'visibility' => 'required',
            'mandatory' => 'required',
            'question' => 'required',
            'explanation' => 'required',
            'actionItems' => 'required',
            'mainCategory' => 'required',
            'country' => 'required',
            'state' => 'required',
            'cities' => 'required',
            'license' => 'required',
            'answers' => 'required',
        ];
        return $fields;
    }

}