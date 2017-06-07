<?php
/**
 * Created by PhpStorm.
 * User: Harsha
 * Date: 5/26/2016
 * Time: 10:34 AM
 */

namespace App\Repositories;

use Bosnadev\Repositories\Eloquent\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;


class PaymentRepository extends Repository
{
    public function model()
    {
        return 'App\Models\payment';
    }


    public function getAllPayments($fromDate, $toDate, $txId, $responseId, $companyName,$txStatus,$txType)
    {
        $qry = $this->model->with(array('company'))->where('id', '!=', "");

        if($fromDate != "" && $toDate != ""){
            $qry->whereBetween('req_date_time', array($fromDate, $toDate));
        }
        else if($fromDate != ""){
            $qry->where('req_date_time', '>=', $fromDate);
        }
        else if($toDate != ""){
            $qry->where('req_date_time', '<=', $toDate);
        }

        if($txId != ""){
            $qry->where('tx_id', $txId);
        }

        if($responseId != ""){
            $qry->where('res_id', $responseId);
        }

        if($txStatus != ""){
            $qry->where('tx_status', $txStatus);
        }

        if($txType != ""){
            $qry->where('tx_type', $txType);
        }

        if($companyName != 0){
            $qry->where('company_id', $companyName);
        }

        $response = $qry->orderBy('created_at', 'desc')->get();

        return $response;
    }
}