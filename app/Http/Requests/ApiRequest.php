<?php

namespace App\Http\Requests;

/**
 * Created by PhpStorm.
 * User: Harsha
 * Date: 5/5/2016
 * Time: 4:46 PM
 */

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Validator;
use Illuminate\Http\Exception\HttpResponseException;

class ApiRequest extends Request
{
    /**
     * Override function to force json response
     */
    public function wantsJson()
    {
        return true;
    }

    /**
     * Override the function to handle a failed validation attempt.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return mixed
     */
    public function response(array $errors){
        if ($this->ajax() || $this->wantsJson()){
            $data = array();
            foreach ($errors as $key => $value){
                $data[] = $value[0];
            }
            $errors_list = ['success'=> false, 'error' => $data];
            return new JsonResponse($errors_list, 422);
        }
        return $this->redirector->to($this->getRedirectUrl())->withInput($this->except($this->dontFlash))->withErrors($errors, $this->errorBag);
    }
}
