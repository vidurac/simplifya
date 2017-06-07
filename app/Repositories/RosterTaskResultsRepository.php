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


class RosterTaskResultsRepository extends Repository
{
    public function model() {
        return 'App\Models\RosterTaskResult';
    }

 public function getTaskResultCount($job_id){
        return $count=$this->model->where('job_id',$job_id)->count();
 }
 public function getTaskResults($job_id){
        return $count=$this->model
            ->join('rosters_tasks','rosters_task_results.task_id','=','rosters_tasks.id')
            ->select('rosters_tasks.name','rosters_tasks.id as rosters_task_id',\DB::raw("CAST(rosters_task_results.status AS UNSIGNED) as status") ,'rosters_tasks.roster_id','rosters_task_results.job_id')
            ->where('job_id',$job_id)
            ->get();
 }
 public function updateTaskResults(array $taskResults,$status,$comment){
        $result=$this->model->firstOrNew($taskResults);
        $result->task_id=$taskResults['task_id'];
        $result->job_id=$taskResults['job_id'];
        $result->status=$status;
        $result->comment=$comment;
        $result->save();
    }

}