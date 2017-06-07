<?php

namespace App\Http\Controllers\Web;

use App\Models\DashBoardModule;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\EntityTypeRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\AppointmentCommentsNotifyUsersRepository;
use App\Repositories\AppointmentRepository;
use App\Repositories\AppointmentActionItemUsersRepository;
use App\Repositories\RequestsRepository;
use App\Repositories\LicenseLocationRepository;
use App\Repositories\DashboardModuleRepository;
use App\Repositories\RosterAssigneesRepository;
use App\Repositories\RosterTaskResultsRepository;
use App\Repositories\RosterJobsRepository;
use App\Repositories\MasterReferralsRepository;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\DashboardModuleRequest;
use App\Lib\sendMail;
use Auth;
use DB;

class DashboardController extends Controller
{
    private $entity;
    public function __construct(EntityTypeRepository $entity, 
        CompanyRepository $company,
        AppointmentCommentsNotifyUsersRepository $notification,
        AppointmentRepository $appointment,
        RequestsRepository $requestRepo,
        LicenseLocationRepository $licenses ,
        DashboardModuleRepository $dashboard_module,
                                RosterAssigneesRepository $rosterAssignees,RosterTaskResultsRepository $rosterTaskResult,
                                RosterJobsRepository $rosterJobs,
                                AppointmentActionItemUsersRepository $appointment_action_item_users,
                                MasterReferralsRepository $referral)
    {
        $this->entity       = $entity;
        $this->company      = $company;
        $this->notification = $notification;
        $this->appointment  = $appointment;
        $this->requestRepo  = $requestRepo;
        $this->licenses     = $licenses;
        $this->dashboard_module     = $dashboard_module;
        $this->rosterAssignees = $rosterAssignees;
        $this->rosterTaskResult=$rosterTaskResult;
        $this->rosterJobs=$rosterJobs;
        $this->appointment_action_item_users=$appointment_action_item_users;
        $this->referral=$referral;
    }
    /**
     * Display a list of pending companies.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get user's master group ID
        $role = Auth::user()->master_user_group_id;

        $module = $this->dashboard_module->getDashboardModuleId(Auth::user()->id);
        if($module != null)
        {
            $module_id = $module->module_id;
        }else{
            $module_id = null;
        }

        return view('dashboard.dashboard_info')->with(array('page_title' => 'Dashboard', 'role' => $role, 'module_id'=>$module_id));
    }

    /**
     * get all pending commissions
     *
     */
    public function getCommissions()
    {
        $data = array();
        $commissions = $this->referral->getAllCommissions();

        foreach($commissions as $commission)
        {
            $data[] = array(
                "<a href='/configuration/referrals/edit/".$commission->id."' >".$commission->name."</a>",
                //$commission->subscription_name,
                "<a href='/configuration/referrals/edit/".$commission->id."' >".$commission->referral_commission."</a>"
            );
        }

        return response()->json(["data" => $data]);
    }

    /**
    * get all pending companies
    *
    *
    */
    public function getAllPendingCompany()
    {
        //declare all variables
        $data = array();
        $status_txt = '';
        $companies = $this->company->getAllPendingCompanies();
        foreach($companies as $key=>$company) {
            $key += 1;
            switch($company['status']){
                case 1:
                    $status_txt ="<span class=\"badge badge-warning\">Pending</span>";
                    break;
            }
            $date = date_create($company['created_at']);
            $data[] = array(
                date_format($date, 'm-d-Y'),
                $company['name'],
                $company['masterEntityType'],
                $status_txt,
                "<a class='btn btn-sm btn-info' data-toggle='tooltip' data-target='#locationInfo' data-loc_id='".$company['id']."' onclick='viewCompanyDetails({$company['id']}, 0)'>Change Status</a>"
            );
        }
        return response()->json(["data" => $data]);
    }

    /**
     * get company summary
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRegisterCompnaySummary ()
    {
        //get all relavant data from company repository
        $mj_business = $this->company->getRegisterCompanyCount(2);
        $compliance_company = $this->company->getRegisterCompanyCount(3);
        $government_entity  = $this->company->getRegisterCompanyCount(4);
        $data = array (
            array ('MJ Business', $mj_business->count),
            array ('Compliance Company', $compliance_company->count),
            array ('Government Entity', $government_entity->count)
            );

        return response()->json(["data" => $data]); 
    }

    /**
     * get user notifications
     * @return array
     */
    public function getUserNotifications ()
    {
        $user = Auth::user();
        $data = ['status_code' => 0];
        $data['action_items_status_code'] = 0;
        $notifications = $this->notification->getUserNotifications (Auth::user()->id);
        $appointment_questions = $this->appointment_action_item_users->getActionItemsForUser($user->id);
        //\Log::info("==== company id: 111111111".print_r($appointment_questions,true));
        if (count ($notifications) > 0)
        {
            $data['result'] = $notifications;
        } else
        {
            $data['status_code'] = 3;
            $data['message'] = 'No notifications available';
        }

        if(count($appointment_questions) > 0)
        {
            foreach ($appointment_questions as $appointment_question)
            {
                //\Log::info("==== company id: ac......".print_r($appointment_question,true));
                $question_action_items = $this->appointment_action_item_users->getActionItemsForAppointment($appointment_question->appointment_id,$user->id);
                $appointment_question->question_action_items = $question_action_items;
            }
        }
        else
        {
            $data['action_items_status_code'] = 3;
            $data['action_items_message'] = 'No action items available';
        }
        $data['action_items'] = $appointment_questions;

        return $data;
    }
    /**
     * get user rosters
     * @return array
     */
    public function getUserRosters(){
        $today=new \DateTime();
        $value=$this->rosterAssignees->getUserRosters(Auth::user()->id,$today->format('Y-m-d'));
        if($value){
        $data['status']=1;
        $data['result']=$value;
        }
        return $data;
    }

    /**
     * update user notifications
     * @param $id
     * @return array
     */
    public function updateUserNotifications ($id)
    {
        $data = ['status_code' => 0];
        $this->notification->updateReadStatus ($id);
        $data['result'] = 'Status changed successfully'; 
        return $data;   
    }

    public function getNewNotification ()
    {
        $data = ['status_code' => 0];
        $notifications = $this->notification->getUserNotificationsCount (Auth::user()->id);

        if (count ($notifications) > 0)
        {
            $data['result'] = $notifications->total;    
        } else
        {
            $data['status_code'] = 3;
            $data['result'] = 0;
        }

        return $data;    
    }

    /**
     * Show upcoming appointments
     *
     * @param user role
     * @param compnay type
     * 
     * @throws
     * @author CK
     * @return json
     * @link <ulr>
     * @since 1.0.0
     */
    public function showUpcomingAppointments ()
    {
        //declare and initialize variables
        $user = Auth::user();
        $data = ['status_code' => 0];
        $getAppointments = $this->appointment->getAppointmentForDashboard ($user->master_user_group_id, Session('entity_type'), $user->company_id, $user->id);         
        
        if (count ($getAppointments) > 0)
        {
            $data['result'] = $getAppointments;
        } else 
        {
            $data['status_code'] = 3;
            $data['message'] = 'No appointment available';
        }

        return $data;
    }

    /**
     * show request to compliance company admin
     * @return array
     */
    public function showRequestToCCAdmin ()
    {
        //declare and initialize variables
        $user = Auth::user();
        $data = ['status_code' => 0];
        $requests = $this->requestRepo->getRequests($user->company_id);

        if (count ($requests) > 0)
        {
            $data['result'] = $requests;
        } else
        {
            $data['status_code'] = 3;
            $data['message'] = 'No request found';
        }

        return $data;
    }

    /**
     * show license list
     * @return array
     */
    public function showLicensesList ()
    {
        //declare and initialize variables
        $user = Auth::user();
        $data = ['status_code' => 0];
        $licenses = $this->licenses->getLicenseList ($user->company_id);

        if (count ($licenses) > 0)
        {
            $data['result'] = $licenses;
        } else
        {
            $data['status_code'] = 3;
            $data['message'] = 'No licenses found';
        }

        return $data;
    }

    /**
     * update when report status changed
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateWhenReportStatusChanged ($id)
    {
        //get notified users list
        $users = $this->appointment->getNotifiedUsers($id);

        foreach ($users as $key => $user) 
        {
            if ($user != Auth::user()->id)
            {
                $notifyData = array (
                'user_id' => $user,
                'appointment_action_item_comments_id' => $id,
                'type' => 2,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id
                );
                $this->notification->create($notifyData); 
            }   
        }

        return Response()->json(array('success' => 'true'), 200);
    }

    /**
     * get user notifications
     * @return array
     */
    public function getReportNotifications ()
    {
        $data = ['status_code' => 0];
        $notifications = $this->notification->getReportNotifications (Auth::user()->id);

        if (count ($notifications) > 0)
        {
            $data['result'] = $notifications;    
        } else
        {
            $data['status_code'] = 3;
            $data['message'] = 'No notifications available';
        }

        return $data;
    }

    /**
     * send mail
     */
    public function sendMail ()
    {
        $mail = new sendMail;
        $mail->mailSender('emails.mjb_welcome', 'chandime.scit@gmail.com', 'chandime', 'welcome', array('from' => 'noreply@simplifya.com', 'system' => 'Simplifya', 'name' => 'name', 'company' => 'sdsfsdfsd'));
    }

    /**
     * send push notification to android
     */
    public function pushNotificationToAndroid()
    {
        // API access key from Google API's Console
        define( 'API_ACCESS_KEY', 'AIzaSyD1Y-YDMpsd2MQiBIdAUb5oZwrPJRLzqgQ' );
        $registrationIds = array('d_x2FYw-EhY:APA91bGKCP0yDS7wwfs_h5lFCBsjK6erjJfKYc7ignQXe3eSTQRqcKr9R_GC-FAcmFNs_3yU2ynILLUQbZR7izBO1SeoFkqioBE0OS9DE2pAMUG1s4RJDXVGl2oky0m6am5lYQ-7-6FZ');
        // prep the bundle
        $msg = array
        (
            'message'   => 'here is a message. message',
            'title'     => 'This is a title. title',
            'subtitle'  => 'This is a subtitle. subtitle',
            'tickerText'    => 'Ticker text here...Ticker text here...Ticker text here',
            'vibrate'   => 1,
            'sound'     => 1
        );
        $fields = array
        (
            'registration_ids'  => $registrationIds,
            'data'          => $msg
        );
         
        $headers = array
        (
            'Authorization: key=' . API_ACCESS_KEY,
            'Content-Type: application/json'
        );
         
        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        curl_close( $ch );
        echo $result;
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


    public function moduleSetup(DashboardModuleRequest $request){
        $module_id   = $request->module_id;
        $user_id = Auth::user()->id;

        $data = array('user_id' => $user_id,
            'module_id' => $module_id,
        );
        if($module_id != null && $user_id != null ) {
            $response =$this->dashboard_module->create($data);
            return response()->json(array('success' => 'true'));
        }
    }

    public function getAllJobTasks(){
        $job_id = $_GET['jobId'];
        $roster_id= $_GET['rosterId'];
        $count=$this->rosterTaskResult->getTaskResultCount($job_id);
        if($count>0){
            $data=$this->rosterTaskResult->getTaskResults($job_id);

            $data=array_map(function($task){
                return ['name'=>$task['name'],'rosters_task_id'=>$task['rosters_task_id'],'job_id'=>$task['job_id'],'status'=>"{$task['status']}"];
            },$data->toArray());

        }else{
            $data=$this->rosterAssignees->getNewTasks($job_id,$roster_id);

        }
        return response()->json(array('success'=>'true','data'=>$data),200);
    }
    public function saveAllJobTasks(Request $request){

        $taskResults=$request->taskResults;
        $taskType=$request->type;
        $jobId=$request->jobId;

        if(!empty($taskResults)){
            DB::beginTransaction();
            try{

                foreach($taskResults as $taskResult){
                    $comment=($taskResult['status']==1)?"Completed":"Incomplete";
                    $this->rosterTaskResult->updateTaskResults(array('task_id' => $taskResult['rosterTaskId'],'job_id'=>$taskResult['jobId']),$taskResult['status'],$comment);
                }
                    if(isset($taskType)){
                        $updateJob=$this->rosterJobs->jobComplete($jobId);
                        if($updateJob){
                            DB::commit();
                            return response()->json(array('success'=>'true','message'=>'Tasks Completed Successfully'));

                        }else{
                            DB::rollBack();
                            return response()->json(array('success'=>'false','message'=>'Error in Saving' ),200);
                        }
                    }
                    DB::commit();
                    return response()->json(array('success'=>'true','message'=>'Tasks Saved Successfully'));


            }catch(\Exception $e){
                DB::rollBack();
                return response()->json(array('success'=>'false','message'=>'Error in Saving' ),200);
            }

        }

    }

    public function saveTaskResults($taskResults){
        foreach($taskResults as $taskResult){
            $comment=($taskResult['status']==1)?"Completed":"Incomplete";
            $this->rosterTaskResult->updateTaskResults(array('task_id' => $taskResult['rosterTaskId'],'job_id'=>$taskResult['jobId']),$taskResult['status'],$comment);
        }
    }

}
