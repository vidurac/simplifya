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


class RosterRepository extends Repository
{
    public function model() {
        return 'App\Models\Roster';
    }

    public function getRosterById($roster_id){
        return $this->model->where('id',$roster_id)->first();
    }

    public function getAllRosters($request,$company_id){

        return $result=$this->model
            ->leftJoin('rosters_tasks', 'rosters_tasks.roster_id', '=', 'rosters.id')
            ->where('company_id',$company_id)
            ->where('rosters.status','1')
            ->select('rosters.id','rosters.name','rosters.company_id','rosters.status','rosters.created_at','rosters.updated_at',\DB::raw("COUNT(rosters_tasks.id) as task_count"))
            ->groupBy('rosters.id')
            ->get();

    }




}