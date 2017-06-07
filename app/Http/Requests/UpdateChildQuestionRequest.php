<?php

namespace App\Http\Requests;


class UpdateChildQuestionRequest extends Request{
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
            'answers' => 'required',
            'answerId' => 'required',
            'parentQuestionId' => 'required'

        ];


        return $fields;
    }

}