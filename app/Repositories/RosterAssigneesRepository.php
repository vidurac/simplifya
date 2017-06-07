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


class RosterAssigneesRepository extends Repository
{
    public function model() {
        return 'App\Models\RosterAssign';
    }

    public function getAllTasks($values){
        $result=$this->model->where('roster_id',$values->rosterId)
            ->where('status',1)
            ->get();
        return $result;
    }

    public function getRoasterAssignees($date){
        $result=$this->model
            ->join('rosters','rosters_assign.roster_id','=','rosters.id')
            ->where('due_date',$date)
            ->where('rosters.status','1')
            ->select('rosters_assign.id','rosters_assign.user_id','rosters_assign.roster_id','rosters_assign.start_date','rosters_assign.end_date','rosters_assign.due_date','rosters_assign.frequency')
            ->get();
        return $result;
    }

    public function updateRosterAssigneeDueData($rosterAssingneeId, $newDue){


        $user=$this->model->find($rosterAssingneeId);
        $user->due_date=$newDue;

        if($user->save()){
            return array('success' => 'true', 'lastInsertedId' =>$user->id );
        }
    }
    public function getUserRosters($id,$date){
        $result= $this->model->join('rosters_jobs','rosters_jobs.roster_assign_id','=','rosters_assign.id')
            ->join('rosters','rosters.id','=','rosters_assign.roster_id')
            ->where('rosters_assign.user_id','=',$id)
            ->where('rosters_jobs.status','=',0)
            ->whereDate('rosters_jobs.created_at', '=', $date)
            ->select('rosters_jobs.id','rosters_assign.roster_id','rosters_assign.frequency','rosters_assign.user_id','rosters.name')
            ->get();
        return $result;

    }
    public function getNewTasks($job_id,$roster_id){
        return $result=$this->model
            ->with('rosterJobs')
            ->join('rosters_jobs','rosters_jobs.roster_assign_id','=','rosters_assign.id')
            ->join('rosters_tasks','rosters_assign.roster_id','=','rosters_tasks.roster_id')
            ->where('rosters_jobs.id','=',$job_id)
            ->select('rosters_tasks.name','rosters_tasks.id as rosters_task_id','rosters_tasks.roster_id','rosters_jobs.id as job_id',DB::raw('0 as status'))
            ->get();
    }

    public function getAllJobs($id,$group,$userCompany){
        $query=$this->model
            ->join('rosters_jobs','rosters_assign.id','=','rosters_jobs.roster_assign_id')
            ->join('users','rosters_assign.user_id','=','users.id')
            ->join('rosters','rosters.id','=','rosters_assign.roster_id')
            ->leftjoin('rosters_task_results','rosters_jobs.id','=','rosters_task_results.job_id')
            ->where('users.company_id','=',$userCompany);
        if(isset($group) && ($group==3 || $group==4)){
            $query->where('users.id','=',$id);
        }
        $query->select('rosters_jobs.id','users.name','rosters_jobs.created_at','rosters_assign.roster_id','rosters.name as roster_name','rosters_assign.frequency','rosters_jobs.status',\DB::raw("COUNT(rosters_task_results.job_id) AS task_count"),\DB::raw("COUNT(case when rosters_task_results.status = 1 then rosters_task_results.status end) AS task_complete_count"));
        $query->groupBy('rosters_jobs.id');
        $result=$query->get();

        return $result;
    }
    public function getAllRosterJobs($id,$group,$userCompany,$roster_id){
        $query=$this->model
            ->join('rosters_jobs','rosters_assign.id','=','rosters_jobs.roster_assign_id')
            ->join('users','rosters_assign.user_id','=','users.id')
            ->join('rosters','rosters.id','=','rosters_assign.roster_id')
            ->leftjoin('rosters_task_results','rosters_jobs.id','=','rosters_task_results.job_id')
            ->where('users.company_id','=',$userCompany)
            ->where('rosters_assign.roster_id','=',$roster_id);
        if(isset($group) && ($group==3 || $group==4)){
            $query->where('users.id','=',$id);
        }
        $query->select('rosters_jobs.id','users.name','rosters_jobs.created_at','rosters_assign.roster_id','rosters.name as roster_name','rosters_assign.frequency','rosters_jobs.status',\DB::raw("COUNT(rosters_task_results.job_id) AS task_count"),\DB::raw("COUNT(case when rosters_task_results.status = 1 then rosters_task_results.status end) AS task_complete_count"));
        $query->groupBy('rosters_jobs.id');
        $result=$query->get();

        return $result;
    }

    public function getAllAssignees($user_company){

        return $this->model
            ->join('rosters','rosters_assign.roster_id','=','rosters.id')
            ->join('users','rosters_assign.user_id','=','users.id')
            ->where('users.company_id','=',$user_company)
            ->select('rosters_assign.id','rosters_assign.user_id','rosters_assign.roster_id','rosters_assign.start_date','rosters_assign.end_date','rosters_assign.due_date','rosters_assign.frequency','rosters.name as roster_name','users.name')
            ->get();
    }
    public function getAllRosterAssignees($user_company,$roster_id){

        return $this->model
            ->join('rosters','rosters_assign.roster_id','=','rosters.id')
            ->join('users','rosters_assign.user_id','=','users.id')
            ->where('users.company_id','=',$user_company)
            ->where('rosters_assign.roster_id','=',$roster_id)
            ->select('rosters_assign.id','rosters_assign.user_id','rosters_assign.roster_id','rosters_assign.start_date','rosters_assign.end_date','rosters_assign.due_date','rosters_assign.frequency','rosters.name as roster_name','users.name')
            ->get();
    }
    public function getAssigneeById($assigneeId){
        return $this->model
            ->join('rosters','rosters_assign.roster_id','=','rosters.id')
            ->join('users','rosters_assign.user_id','=','users.id')
            ->where('rosters_assign.id','=',$assigneeId)
            ->select('rosters_assign.id','rosters_assign.user_id','rosters_assign.roster_id','rosters_assign.start_date','rosters_assign.end_date','rosters_assign.due_date','rosters_assign.frequency','rosters.name as roster_name','users.name')
            ->get();
    }

    public function getRosterCount($rosterId,$date){
        return $this->model
            ->where('rosters_assign.roster_id','=',$rosterId)
            ->where('rosters_assign.end_date','>=',$date)
            ->count();

    }

}