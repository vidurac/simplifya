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


class RosterTasksRepository extends Repository
{
    public function model() {
        return 'App\Models\RosterTask';
    }

    public function getAllTasks($values){
        $result=$this->model->where('roster_id',$values->rosterId)
            ->where('status',1)
            ->get();
        return $result;
    }

}