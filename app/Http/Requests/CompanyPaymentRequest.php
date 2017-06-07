<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Repositories\CompanyRepository;
use Auth;

class CompanyPaymentRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */

    private $company;
    public function __construct(CompanyRepository $company)
    {
        $this->company = $company;
        parent::__construct();
    }

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
        $company_id  = Auth::User()->company_id;
        $company_details = $this->company->find($company_id);

        if($company_details->foc == 1)
        {
            return [];
        }
        else{
            return [
                'card_number' => 'required',
                'ccv_number' => 'required',
                'exp_month' => 'required',
                'exp_year' => 'required'
            ];
        }

    }
}
