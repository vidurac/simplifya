<?php
/**
 * Created by PhpStorm.
 * User: Harsha
 * Date: 7/13/2016
 * Time: 4:38 PM
 */

namespace App\Repositories;
use Bosnadev\Repositories\Eloquent\Repository;

class CompanyCardRepository extends Repository
{
    public function model()
    {
        return 'App\Models\CompanyCards';
    }




    public function isCompanyCardAdded($company_id)
    {
        return $this->model
            ->where('company_id', $company_id)
            ->get();
    }


    public function updateCompanyCard($company_id,$cardId)
    {
       $this->model
            ->where('company_id', $company_id)
            ->update(array('status' => 0));

        return $this->model
            ->where('company_id','=', $company_id)
            ->where('card_id','=',$cardId)
            ->update(array('status' => 1));
    }
}