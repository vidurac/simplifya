<?php

namespace App\Http\Controllers\Web;

use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\RosterRepository;
use App\Repositories\RosterTasksRepository;
use App\Repositories\RosterAssigneesRepository;
use App\Repositories\UsersRepository;
use Auth;

class RosterController extends Controller
{
    private $roster,$rosterTask,$question;

    /**
     * RosterController constructor.
     * @param RosterRepository $roster
     * @param RosterTasksRepository $rosterTask
     * @param UsersRepository $user
     */
    public function __construct(RosterRepository $roster,RosterTasksRepository $rosterTask,RosterAssigneesRepository $rosterAssignees,UsersRepository $user
    )
    {
        $this->roster = $roster;
        $this->rosterTask = $rosterTask;
        $this->rosterAssignees = $rosterAssignees;
        $this->user = $user;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('roster.roster_list')->with('page_title', 'Checklist List');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * new roster
     * @return $this
     */
    public function NewRoster()
    {
        return view('roster.new_roster')->with('page_title', 'Add New Checklist');
    }

    /**
     * Get all tasks for each roster
     * @return $this
     */
    public function getTasks($roster_id){
        $roster=$this->roster->getRosterById($roster_id);
        return view('roster.task_list')->with('page_title', 'Add a checklist task - '.$roster->name);
    }


    public function saveTasks(Request $request){
        $requests = $request->all();
        $roster_task_check= \Validator::make($requests,[
            'taskName' =>'unique:rosters_tasks,name,id'
        ]);

        $roster_task_status = $roster_task_check->passes();
        if($roster_task_status==true){
            $data=array('name'=>$request->taskName,'roster_id'=>$request->rosterId,'status'=>1);
            $query = $this->rosterTask->create($data);
            if($query){
                return response()->json(array('success' => 'true','message'=>'Task is successfully saved'),200);
            }

        }else{
            $roster='Duplicate Checklist Task';
            return response()->json(array('success' => 'false','message'=>'Could not create checklist Task - Duplicate Checklist Task','data'=>$roster),405);

        }
    }
    public function deleteTasks(Request $request){

        $query = $this->rosterTask->update(array('status' => 0), $request->rosterTaskId);
        if($query){
            return response()->json(array('success' => 'false','message'=>'Task is deleted successfully'),200);
        }
    }
    public function deleteRoster(Request $request){
        $query = $this->roster->update(array('status' => 0), $request->rosterId);
        if($query){
            return response()->json(array('success' => 'false','message'=>'Checklist is deleted successfully'),200);
        }
    }
    public function getAllTasks(Request $request){
        $query=$this->rosterTask->getAllTasks($request);
        if($query){
            return response()->json(array('success'=>'true','data'=>$query),200);
        }
    }

    public function getAllRosters(Request $request){
        $company_id=Auth::User()->company_id;
        $query=$this->roster->getAllRosters($request,$company_id);
        if($query){
            return response()->json(array('success'=>'true','data'=>$query),200);
        }
    }

    public function getRosterAssignee(Request $request){
        $user_company=Auth::User()->company_id;
        $roster_id=$request->rosterId;
        $results=$this->rosterAssignees->getAllRosterAssignees($user_company,$roster_id);
        $i=0;
        foreach ($results as $result){
            $data[$i]['id']=$result->id;
            $data[$i]['name']=$result->name;
            $data[$i]['roster_name']=$result->roster_name;
            $data[$i]['start_date']=date("m/d/Y", strtotime($result->start_date));
            $data[$i]['start_date_timestamp'] = Carbon::parse($result->start_date)->getTimestamp();
            $data[$i]['end_date']=date("m/d/Y", strtotime($result->end_date));
            $data[$i]['end_date_timestamp'] = Carbon::parse($result->end_date)->getTimestamp();
            $data[$i]['due_date']=date("m/d/Y", strtotime($result->due_date));
            $data[$i]['due_date_timestamp'] = Carbon::parse($result->due_date)->getTimestamp();

            switch ($result->frequency){
                case 1:
                    $data[$i]['frequency']='Daily';
                    break;
                case 7:
                    $data[$i]['frequency']='Weekly';
                    break;
                case 14:
                    $data[$i]['frequency']='Bi-weekly';
                    break;
                case 15:
                    $data[$i]['frequency']='Semi-monthly';
                    break;
                case 30:
                    $data[$i]['frequency']='Monthly';
                    break;

            }
            $i++;

        }

        return response()->json(array('success'=>'true','data'=>$data),200);
    }

    public function getUsers(){
        $company_id=Auth::User()->company_id;
        $query=$this->user->getRosterUsers($company_id);
        if($query){
            return response()->json(array('success'=>'true','data'=>$query),200);
        }
    }

    public function assignTasks(Request $request){
        $requests = $request->all();
        $roster_task_assign= \Validator::make($requests,[
            'userId' =>'unique:rosters_assign,user_id,NULL,id,frequency,'.$request->selectedFrequency.',roster_id,'.$request->rosterId
        ]);

        $roster_task_assign_status = $roster_task_assign->passes();
        if($roster_task_assign_status==true){
            $data=array('user_id'=>$request->userId,'roster_id'=>$request->rosterId,'start_date'=>$request->dtStart,'end_date'=>$request->dtEnd,'due_date'=>$request->dtStart,'frequency'=>$request->selectedFrequency);
            $query = $this->rosterAssignees->create($data);
            if($query){
                return response()->json(array('success' => 'true','message'=>'Checklist is assigned'),200);
            }
        }else{
            return response()->json(array('success' => 'false','message'=>'Same Checklist is already assigned',),405);

        }
    }

    public function getAllJobTasks(Request $request){
        return response()->json(array('success' => 'true','message'=>'Checklist is assigned'),200);
    }

    public function getJobs(){
        return view('roster.roster_jobs')->with('page_title', 'Checklist Jobs');
    }
    public function getAllRosterJobs(Request $request){
        $userId=Auth::User()->id;
        $userGroup=Auth::User()->master_user_group_id;
        $userCompany=Auth::User()->company_id;
        $rosterId=$request->rosterId;
        $today=new \DateTime();
        $results=$this->rosterAssignees->getAllRosterJobs($userId,$userGroup,$userCompany,$rosterId);
        $i=0;
        foreach ($results as $result){
            $data[$i]['id']=$result->id;
            $data[$i]['name']=$result->name;
            $data[$i]['rosterName']=$result->roster_name;
            $data[$i]['rosterTaskCount']=$result->task_count;
            $data[$i]['rosterTaskCompleteCount']=$result->task_complete_count;

            if ($result->task_complete_count!=0){

                $data[$i]['rosterTaskCompletinPercentage']=ceil(($result->task_complete_count/$result->task_count) * 100) ;
            }else{
                $data[$i]['rosterTaskCompletinPercentage']=0;
            }
            $data[$i]['rosterId']=$result->roster_id;
            switch ($result->frequency){
                case 1:
                    $data[$i]['frequency']='Daily';
                    break;
                case 7:
                    $data[$i]['frequency']='Weekly';
                    break;
                case 14:
                    $data[$i]['frequency']='Bi-weekly';
                    break;
                case 15:
                    $data[$i]['frequency']='Semi-monthly';
                    break;
                case 30:
                    $data[$i]['frequency']='Monthly';
                    break;

            }
            $created_date=$result->created_at;
            $data[$i]['date']=date("m/d/Y", strtotime($created_date->format('Y-m-d')));
            $data[$i]['date_timestamp']=Carbon::parse($created_date->format('Y-m-d'))->getTimestamp();
            if($result->status==1){
                $data[$i]['status']='Completed';
            }else if((date_diff(date_create($created_date->format('Y-m-d')),date_create($today->format('Y-m-d')))->format("%R%a")==0) && $result->status==0){
                $data[$i]['status']='Pending';

            }else if((date_diff(date_create($created_date->format('Y-m-d')),date_create($today->format('Y-m-d')))->format("%R%a")>0) && $result->status==0){

                $data[$i]['status']='Over Due';

            }else if((date_diff(date_create($created_date->format('Y-m-d')),date_create($today->format('Y-m-d')))->format("%R%a")<0) && $result->status==0){
                $data[$i]['status']='Sheduled';
            }
            $i++;

        }
        return response()->json(array('success'=>'true','data'=>$data,'userGroup'=>$userGroup),200);
    }
    public function getAllJobs(){
        $userId=Auth::User()->id;
        $userGroup=Auth::User()->master_user_group_id;
        $userCompany=Auth::User()->company_id;
        $today=new \DateTime();
        $results=$this->rosterAssignees->getAllJobs($userId,$userGroup,$userCompany);
        $i=0;
        foreach ($results as $result){
            $data[$i]['id']=$result->id;
            $data[$i]['name']=$result->name;
            $data[$i]['rosterName']=$result->roster_name;
            $data[$i]['rosterTaskCount']=$result->task_count;
            $data[$i]['rosterTaskCompleteCount']=$result->task_complete_count;

           if ($result->task_complete_count!=0){

            $data[$i]['rosterTaskCompletinPercentage']=ceil(($result->task_complete_count/$result->task_count) * 100) ;
           }else{
               $data[$i]['rosterTaskCompletinPercentage']=0;
           }
            $data[$i]['rosterId']=$result->roster_id;
            switch ($result->frequency){
                case 1:
                $data[$i]['frequency']='Daily';
                    break;
                case 7:
                $data[$i]['frequency']='Weekly';
                    break;
                case 14:
                $data[$i]['frequency']='Bi-weekly';
                    break;
                case 15:
                $data[$i]['frequency']='Semi-monthly';
                    break;
                case 30:
                $data[$i]['frequency']='Monthly';
                    break;

            }
            $created_date=$result->created_at;
            $data[$i]['date']=date("m/d/Y", strtotime($created_date->format('Y-m-d')));
            if($result->status==1){
                $data[$i]['status']='Completed';
            }else if((date_diff(date_create($created_date->format('Y-m-d')),date_create($today->format('Y-m-d')))->format("%R%a")==0) && $result->status==0){
                $data[$i]['status']='Pending';

            }else if((date_diff(date_create($created_date->format('Y-m-d')),date_create($today->format('Y-m-d')))->format("%R%a")>0) && $result->status==0){

                $data[$i]['status']='Over Due';

            }else if((date_diff(date_create($created_date->format('Y-m-d')),date_create($today->format('Y-m-d')))->format("%R%a")<0) && $result->status==0){
                $data[$i]['status']='Sheduled';
            }
            $i++;

        }
        return response()->json(array('success'=>'true','data'=>$data,'userGroup'=>$userGroup),200);

    }

    public function getAssignees(){
        return view('roster.roster_assignees')->with('page_title', 'Checklist Assignee List');
    }

    public function getAllAssignees(){
        $user_company=Auth::User()->company_id;
        $results=$this->rosterAssignees->getAllAssignees($user_company);
        $i=0;
        foreach ($results as $result){
            $data[$i]['id']=$result->id;
            $data[$i]['name']=$result->name;
            $data[$i]['roster_name']=$result->roster_name;
            $data[$i]['start_date']=date("m/d/Y", strtotime($result->start_date));
            $data[$i]['start_date_timestamp'] = Carbon::parse($result->start_date)->getTimestamp();
            $data[$i]['end_date']=date("m/d/Y", strtotime($result->end_date));
            $data[$i]['end_date_timestamp'] = Carbon::parse($result->end_date)->getTimestamp();
            $data[$i]['due_date']=date("m/d/Y", strtotime($result->due_date));
            $data[$i]['due_date_timestamp'] = Carbon::parse($result->due_date)->getTimestamp();

            switch ($result->frequency){
                case 1:
                    $data[$i]['frequency']='Daily';
                    break;
                case 7:
                    $data[$i]['frequency']='Weekly';
                    break;
                case 14:
                    $data[$i]['frequency']='Bi-weekly';
                    break;
                case 15:
                    $data[$i]['frequency']='Semi-monthly';
                    break;
                case 30:
                    $data[$i]['frequency']='Monthly';
                    break;

            }
            $i++;

        }

        return response()->json(array('success'=>'true','data'=>$data),200);
    }

    public function getAssignee(Request $request){
        $assignee=$this->rosterAssignees->getAssigneeById($request->assignId);
        return response()->json(array('success'=>'true','data'=>$assignee),200);
    }
    public function updateAssignee(Request $request){

        $query = $this->rosterAssignees->update(array('end_date'=>$request->dtEnd), $request->id);
        if($query){
            return response()->json(array('success' => 'true','message'=>'Updated successfully'),200);
        }
    }

    public function saveRoster(Request $request) {

        $requests = $request->all();
        $roster_check= \Validator::make($requests,[
            'name' =>'unique:rosters,name,NULL,id,company_id,'.Auth::User()->company_id
        ]);

        $roster_status = $roster_check->passes();

        if($roster_status==true){
            $createRoster = $this->roster->create(
                array(
                    'name' => $request->name,
                    'company_id' => Auth::User()->company_id,
                    'status' => 1
                )
            );

            if (!$createRoster) {
                return response()->json(array('success' => 'false','message'=>'Could not create checklist'),405);
            }

            return response()->json(array('success' => 'true','message'=>'Checklist successfully created'),200);
        }else{
            $roster='Duplicate Checklist';
            return response()->json(array('success' => 'false','message'=>'Could not create Checklist - Duplicate Checklist','data'=>$roster),405);

        }
    }

    public function getRosterCount(Request $request){
        $today=new \DateTime();
        $assignee=$this->rosterAssignees->getRosterCount($request->rosterId,$today->format('Y-m-d'));
        return response()->json(array('success'=>'true','data'=>$assignee),200);
    }
}
