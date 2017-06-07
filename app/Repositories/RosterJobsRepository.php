<?php
/**
 * Created by PhpStorm.
 * User: Harsha
 * Date: 5/5/2016
 * Time: 4:06 PM
 */

namespace App\Repositories;
use Bosnadev\Repositories\Contracts\RepositoryInterface;
use Bosnadev\Repositories\Eloquent\Repository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use DB;


class RosterJobsRepository extends Repository
{
    public function model() {
        return 'App\Models\RosterJob';
    }


    public function jobComplete($jobId){
        $result=$this->model
            ->where('id',$jobId)
            ->update(['status'=>1]);
        return $result;

    }

}