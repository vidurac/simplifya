<?php

namespace App\Http\Controllers\Web;

use App\Models\MasterEntityType;
use App\Repositories\CompanyRepository;
use App\Repositories\UserGroupesRepository;
use App\Repositories\ConfirmationCodeRepository;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;

use Aws\Laravel\AwsFacade as AWS;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\UsersRepository;
use App\Repositories\CompanyUserRepository;
use App\Http\Requests\InviteEmployeeRequest;
use App\Http\Requests\SetUserPasswordRequest;
use App\Http\Requests\EditInviteEmployeeRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Lib\sendMail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Redirect;
use App\Repositories\UploadRepository;


class UserController extends Controller
{
    private $user;
    private $company_user;
    private $company;
    private $group;
    private $entityType;
    private $upload;
    private $confirmation;

    public function __construct(UsersRepository $user,
                CompanyUserRepository $company_user,
                CompanyRepository $company,
                UserGroupesRepository $group,
                MasterEntityType $entityType,
                UploadRepository $upload,
                ConfirmationCodeRepository $confirmation
    ) {
        $this->user       = $user;
        $this->company_user = $company_user;
        $this->company = $company;
        $this->group = $group;
        $this->entityType = $entityType;
        $this->upload = $upload;
        $this->confirmation = $confirmation;
    }

    /*
     * Load user view
     * return page title
     */
    public function getUsersByCompanyId()
    {
        $groupId = Auth::user()->master_user_group_id;

        if($groupId == Config::get('simplifya.MasterAdmin')){
            return view('users.user_details')->with(array('page_title' => 'SIMPLIFYA Admin Manager','show_location' => false));
        }
        else{
            return view('users.user_details')->with(array('page_title' => 'Admin Manager','show_location' => true));
        }


    }

    /**
     * Store User details
     * @param InviteEmployeeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function inviteToEmployees(InviteEmployeeRequest $request)
    {
        $id = Auth::user()->id;
        $sent_name = Auth::User()->name;
        $name = $request->name;
        $email = $request->email_address;
        $locations = $request->locations;
        $permission_level = $request->permission;
        $company_id = $request->company_id;
        $entity_type = $request->entity_type;
        $user_locations = array();
        $send_email = '';
        $se_entity_type = session('entity_type');
        $company_status = session('company_status');
        if($company_status == 0) {
            $send_email = 0;
        } else {
            $send_email = 1;
        }
        $isExist = $this->user->isuserExist($email);
        if(!isset($isExist[0])) {
            $user_data = array('name' => $name, 'email' => $email, 'company_id' => $company_id, 'is_invite' => 1, 'master_user_group_id' => $permission_level, 'status' => 1, 'is_send_mail' => $send_email, 'created_by' => $id);
            $user_response = $this->user->create($user_data);
            if($user_response) {
                $user_id = $user_response->id;
                $confirmation_code =  sha1(uniqid().$user_id); //Hash::make(time());
                $data = array('user_id' => $user_response->id, 'confirmation_code' => $confirmation_code, 'is_confirm' => '0');
                $response = $this->confirmation->create($data);
                if(is_array($locations)) {
                    for($i=0; $i<count($locations); $i++) {
                        $data =  array('user_id'=> $user_id, 'location_id'=> $locations[$i], 'created_by' => $id);
                        $user_locations[] = $data;
                    }
                    $response = $this->company_user->insertMultipleLocations($user_locations);
                }
                elseif($locations != '') {
                    $data =  array('user_id'=> $user_id, 'location_id'=> $locations, 'created_by' => $id);
                    $user_locations[] = $data;
                    $response = $this->company_user->insertMultipleLocations($user_locations);
                }

                $layout ='emails.emp_invitation';
                $subject ='You’ve been invited to join Simplifya by '.$sent_name;
                $base_url = 'register/'.$confirmation_code;
                $url = URL::to($base_url);
                $company_details = $this->company->find($company_id);

                if(($se_entity_type == 2) && ($company_status == 2)) {
                    $data = array('from' => 'noreply@simplifya.com','system' => 'Simplifya', 'url' => $url, 'company' => 'Simplifya', 'sent_name' => $sent_name, 'company_name' => $company_details->name);
                    $this->sendWelcomeMail($email, $name, $layout, $subject,$data);
                    $message = Config::get('messages.INVITE_TO_EMPLOYEE_SUCCESS');
                    return response()->json(array('success' => 'true', 'message'=> $message));
                } elseif(($se_entity_type == 2) && ($company_status == 0)) {
                    $message = Config::get('messages.INVITE_TO_EMPLOYEE_SUCCESS');
                    return response()->json(array('success' => 'true', 'message'=> $message));
                } elseif(($se_entity_type == 3 || $se_entity_type == 4) && ($company_status == 2)) {
                    $data = array('from' => 'noreply@simplifya.com','system' => 'Simplifya','url' => $url, 'company' => 'Simplifya', 'sent_name' => $sent_name, 'company_name' => $company_details->name);
                    $this->sendWelcomeMail($email, $name, $layout, $subject,$data);
                    $message = Config::get('messages.INVITE_TO_EMPLOYEE_SUCCESS');
                    return response()->json(array('success' => 'true', 'message'=> $message));
                } elseif($se_entity_type == 1) {
                    $data = array('from' => 'noreply@simplifya.com','system' => 'Simplifya','url' => $url, 'company' => 'Simplifya', 'sent_name' => $sent_name, 'company_name' => $company_details->name);
                    $this->sendWelcomeMail($email, $name, $layout, $subject,$data);
                    $message = Config::get('messages.INVITE_TO_EMPLOYEE_SUCCESS');
                    return response()->json(array('success' => 'true', 'message'=> $message));
                }

            } else {
                $message = Config::get('messages.INVITE_TO_EMPLOYEE_FAILED');
                return response()->json(array('success' => 'false', 'message'=> $message));
            }
        } else {
            if($isExist[0]['status'] == 0) {
                $message = Config::get('messages.USER_DELETED_MSG');
                return response()->json(array('success' => 'false', 'message'=> $message));
            }else {
                $message = Config::get('messages.USER_ALREADY_EXISTS');
                return response()->json(array('success' => 'false', 'message'=> $message));
            }
        }

    }

    /**
     * Get User details by company id
     * @param $company_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEmployeesByCompanyId($company_id)
    {
        $users = $this->user->getUserByCompanyId($company_id); 
        $locations = "";
        $users_data = array();
        foreach($users as $user) {
            foreach($user->companyUser as $location) {
              $locations .=   $location->CompanyLocation->name. '<br/>';
            }

            $users_data[] = array(
                    $user->name,
                    $user->email,
                    ($locations !='')?$locations: 'All',
                    $user->masterUserGroup->name,
                    "<a class='btn btn-info btn-circle' data-toggle='modal' data-target=''  data-loc_id='$user->id' onclick='changeUserDetails({$user->id})'><i class='fa fa-paste'></i></a>
                     <a class='btn btn-danger btn-circle' data-toggle='modal' data-target=''  data-loc_id='$user->id' onclick='changeUserStatus({$user->id}, 0)'><i class='fa fa-trash-o'></i></a>
                    "
            );
            $locations = "";
        }
        return response()->json(array('data' => $users_data), 200);
    }

    /**
     * User detail display on table
     * @return \Illuminate\Http\JsonResponse
     */
    public function allUsersByCompanyId()
    {
        $company_id = Auth::user()->company_id;
        $data = array();
        $row = array();
        $company_users = $this->user->getAllUsers($company_id);

        foreach($company_users as $company_user) {
            $locations = $this->company_user->getUserLocation($company_user['id']);
            //\Log::info("=============category....===============".print_r(json_encode($locations),true));

            $location_names = "";
            if(count($locations) > 0)
            {
                foreach($locations as $location)
                {
                    $location_names .= $location['name'].",";
                }
            }
            if($location_names != "")
            {
                $location_names = substr($location_names, 0, -1);
            }

            if($company_user['status'] == 1) {
                $row[] ="<a class='btn btn-info btn-circle' data-toggle='tooltip' data-target='#locationInfo' title='Edit' data-user_role='".$company_user->master_user_group_id."' data-user_id='".$company_user['id']."' onclick='changeUserDetails({$company_user['id']})'><i class='fa fa-paste'></i></a>
                        <a class='btn btn-success btn-circle' data-toggle='tooltip' data-target='#locationDelete' title='Active'  data-user_role='".$company_user->master_user_group_id."' data-user_id='".$company_user['id']."'onclick='changeUserStatus({$company_user['id']}, 2)'><i class='fa fa-thumbs-o-up'></i></a>
                        <a class='btn btn-danger btn-circle' data-toggle='tooltip' data-target='#locationDelete' title='Delete' data-user_role='".$company_user->master_user_group_id."' data-user_id='".$company_user['id']."'onclick='changeUserStatus({$company_user['id']}, 0)'><i class='fa fa-trash-o'></i></a>
                    ";
            } elseif($company_user['status'] == 2) {
                $row[] ="
                    <a class='btn btn-info btn-circle' data-toggle='tooltip' data-target='#locationInfo' title='Edit' data-user_role='".$company_user->master_user_group_id."' data-user_id='".$company_user['id']."' onclick='changeUserDetails({$company_user['id']})'><i class='fa fa-paste'></i></a>
                    <a class='btn btn-warning btn-circle' data-toggle='tooltip' data-target='#locationDelete' data-user_role='".$company_user->master_user_group_id."' title='Inactive' data-user_id='".$company_user['id']."'onclick='changeUserStatus({$company_user['id']}, 1)'><i class='fa fa-thumbs-o-down'></i></a>
                    <a class='btn btn-danger btn-circle' data-toggle='tooltip' data-target='#locationDelete' data-user_role='".$company_user->master_user_group_id."' title='Delete' data-user_id='".$company_user['id']."'onclick='changeUserStatus({$company_user['id']}, 0)'><i class='fa fa-trash-o'></i></a>
                    ";
            } else {
                $row[] = "<a class='btn btn-primary btn-circle' data-toggle='tooltip' data-target='#locationDelete' data-user_role='".$company_user->master_user_group_id."' title='Restore' data-user_id='".$company_user['id']."'onclick='reCreateUser({$company_user['id']}, 1)'><i class='fa fa-recycle'></i></a>";
            }
            $data[] = array(
                $company_user['name'],
                $company_user['email'],
                $company_user->masterUserGroup['name'],
                $row,
                $location_names
                );
            $row = array();
        }
        return response()->json(["data" => $data]);
    }

    /**
     * User filter by user state and name
     * @return \Illuminate\Http\JsonResponse
     */
    public function userSearchByLevelsAndStatus()
    {
        $company_id = Auth::User()->company_id;
        $user_group_id = Input::get('permission_id');
        $name = Input::get('name');
        $status = Input::get('status');

        $data = array();
        $row = array();
        $users = $this->user->searchUsers($user_group_id, $name, $status, $company_id);

        if($users) {
            foreach($users as $user) {
                if($user['status'] == 1) {
                    $row[] ="<a class='btn btn-info btn-circle' data-toggle='tooltip' data-target='#locationInfo' title='Edit' data-user_id='".$user['id']."' onclick='changeUserDetails({$user['id']})'><i class='fa fa-paste'></i></a>
                        <a class='btn btn-success btn-circle' data-toggle='tooltip' data-target='#locationDelete' title='Active'  data-user_id='".$user['id']."'onclick='changeUserStatus({$user['id']}, 2)'><i class='fa fa-thumbs-o-up'></i></a>
                        <a class='btn btn-danger btn-circle' data-toggle='tooltip' data-target='#locationDelete' title='Delete' data-user_id='".$user['id']."'onclick='changeUserStatus({$user['id']}, 0)'><i class='fa fa-trash-o'></i></a>
                    ";
                } elseif($user['status'] == 2) {
                    $row[] ="
                    <a class='btn btn-info btn-circle' data-toggle='tooltip' data-target='#locationInfo' title='Edit' data-user_id='".$user['id']."' onclick='changeUserDetails({$user['id']})'><i class='fa fa-paste'></i></a>
                    <a class='btn btn-warning btn-circle' data-toggle='tooltip' data-target='#locationDelete' title='Inactive' data-user_id='".$user['id']."'onclick='changeUserStatus({$user['id']}, 1)'><i class='fa fa-thumbs-o-down'></i></a>
                    <a class='btn btn-danger btn-circle' data-toggle='tooltip' data-target='#locationDelete' title='Delete' data-user_id='".$user['id']."'onclick='changeUserStatus({$user['id']}, 0)'><i class='fa fa-trash-o'></i></a>
                    ";
                } else {
                    $row[] = "<a class='btn btn-primary btn-circle' data-toggle='tooltip' data-target='#locationDelete'  title='Restore' data-user_id='".$user['id']."'onclick='reCreateUser({$user['id']}, 1)'><i class='fa fa-recycle'></i></a>";
                }

                $data[] = array(
                    $user['name'],
                    $user['email'],
                    $user->masterUserGroup['name'],
                    $row
                );
                $row = array();
            }
        }
        return response()->json(["data" => $data]);
    }

    public function reStoreUser(Request $request)
    {
        $user_id = $request->user_id;
        $status = $request->status;
        $company_id = Auth::User()->company_id;
        $se_entity_type = session('entity_type');
        $company_status = session('company_status');
        $user_details = $this->user->find($user_id);
        $sent_name = Auth::User()->name;

        $response = $this->user->changeUserStatus($user_id, $status);
        if($response) {
            $confirmation_code =  sha1(uniqid().$user_id);
            $layout ='emails.emp_invitation';
            $subject ='You’ve been invited to join Simplifya by '.$sent_name;
            $base_url = 'register/'.$confirmation_code;
            $url = URL::to($base_url);
            $company_details = $this->company->find($company_id);
            $code = $this->confirmation->findWhere(array('user_id' => $user_id));
            
            if(count($code)>0) {
                $this->confirmation->updateUserConfirmationCode($user_id, $confirmation_code);
            }
            if(($se_entity_type == 2) && ($company_status == 2)) {
                $data = array('from' => 'noreply@simplifya.com','system' => 'Simplifya', 'url' => $url, 'company' => 'Simplifya', 'sent_name' => $sent_name, 'company_name' => $company_details->name);
                $this->sendWelcomeMail($user_details->email, $user_details->name, $layout, $subject,$data);
                $message = Config::get('messages.RESTORED_TO_EMPLOYEE_SUCCESS');
                return response()->json(array('success' => 'true', 'message'=> $message));
            } elseif(($se_entity_type == 2) && ($company_status == 0)) {
                $message = Config::get('messages.RESTORED_TO_EMPLOYEE_SUCCESS');
                return response()->json(array('success' => 'true', 'message'=> $message));
            } elseif(($se_entity_type == 3 || $se_entity_type == 4) && ($company_status == 2)) {
                $data = array('from' => 'noreply@simplifya.com','system' => 'Simplifya','url' => $url, 'company' => 'Simplifya', 'sent_name' => $sent_name, 'company_name' => $company_details->name);
                $this->sendWelcomeMail($user_details->email, $user_details->name, $layout, $subject,$data);
                $message = Config::get('messages.RESTORED_TO_EMPLOYEE_SUCCESS');
                return response()->json(array('success' => 'true', 'message'=> $message));
            } elseif($se_entity_type == 1) {
                $data = array('from' => 'noreply@simplifya.com','system' => 'Simplifya','url' => $url, 'company' => 'Simplifya', 'sent_name' => $sent_name, 'company_name' => $company_details->name);
                $this->sendWelcomeMail($user_details->email, $user_details->name, $layout, $subject,$data);
                $message = Config::get('messages.RESTORED_TO_EMPLOYEE_SUCCESS');
                return response()->json(array('success' => 'true', 'message'=> $message));
            }
        }
    }

    /**
     * Get User Permission by company id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserPermissionsByCompany()
    {
        $company_id = Auth::User()->company_id;
        $permissions = $this->company->getUserPermissionByCompanyId($company_id);
        return response()->json(["data" => $permissions]);
    }

    /**
     * @return CompanyUserRepository
     */
    public function getInviteEmployees($user_id)
    {
        $user = $this->user->getUserByUserId($user_id);
        return response()->json(array('data' => $user), 200);
    }


    public function getAllEmployees($user_id)
    {
        $user = $this->user->getAllUserByUserId($user_id);
        return response()->json(array('data' => $user), 200);
    }

    /**
     * delete user by id
     * @param $user_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteUserById($user_id)
    {
        $response = $this->user->changeUserStatus($user_id, 0);
        if($response) {
            $message = Config::get('messages.DELETE_USERS');
            return response()->json(array('success' => 'true', 'message'=> $message));
        } else {
            $message = Config::get('messages.DELETE_USER_FAILED');
            return response()->json(array('success' => 'false', 'message'=> $message));
        }
    }

    /**
     * Update User details
     * @param EditInviteEmployeeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeInviteEmployees(EditInviteEmployeeRequest $request)
    {
        $result = '';
        $id = Auth::user()->id;
        $name = $request->name;
        $email = $request->email_address;
        $locations = $request->locations;
        $permission_level = $request->permission;
        $company_id = $request->company_id;
        $user_locations = array();
        $isExist = $this->user->isuserExist($email);

        if(isset($isExist[0])) {
            $userLocations = $this->company_user->findWhere(array('user_id' => $isExist[0]['id']));
            if(count($userLocations)>0) {
                foreach($userLocations as $userLocation) {
                    $user_locations[] = $userLocation->location_id;
                }
            } else {
                $data = array();
                if(isset($locations[0])) {
                    for($i=0; $i<count($locations); $i++) {
                        $data[] = array('user_id' => $isExist[0]['id'], 'location_id' => $locations[$i], 'created_by' => $id);
                    }
                    $this->company_user->insertMultipleLocations($data);
                }
            }
            if(!empty($user_locations)) {
                $this->company_user->deleteUserLocations($user_locations, $isExist[0]['id']);
                $data = array();
                for($i=0; $i<count($locations); $i++) {
                    $data[] = array('user_id' => $isExist[0]['id'], 'location_id' => $locations[$i], 'created_by' => $id);
                }
                $this->company_user->insertMultipleLocations($data);
            }

            $user_data = array('name' => $name, 'company_id' => $company_id, 'is_invite' => 1, 'master_user_group_id' => $permission_level);
            $response = $this->user->userUpdate($isExist[0]['id'], $user_data);
            if($response) {
                $message = Config::get('messages.USER_UPDATE_SUCCESS');
                return response()->json(array('success' => 'true', 'message'=> $message));
            } else {
                $message = Config::get('messages.USER_UPDATE_FAILED');
                return response()->json(array('success' => 'false', 'message'=> $message));
            }
        } else {
            $message = Config::get('messages.USER_NOT_IN_THE_SYSTEM');
            return response()->json(array('success' => 'false', 'message'=> $message));
        }
    }

    /**
     * User activate and Inactivate
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeUserStatus()
    {
        $user_id = Input::get('user_id');
        $status = Input::get('status');
        $message ='';
        $login_user = Auth::User()->id;
        if($login_user == $user_id) {
            if($status == 0) {
                $message = Config::get('messages.USER_SELF_DELETED');
                return response()->json(array('success' => 'false', 'message'=> $message));
            } else {
                $message = Config::get('messages.USER_SELF_INACTIVATE');
                return response()->json(array('success' => 'false', 'message'=> $message));
            }
        } else {
            $response = $this->user->changeUserStatus($user_id, $status);
            if($response) {
                if($status == 0){
                    $message = Config::get('messages.DELETE_USERS');
                } elseif($status == 1) {
                    $message = Config::get('messages.ACTIVE_USERS');
                } elseif($status == 2) {
                    $message = Config::get('messages.INACTIVE_USERS');
                }

                return response()->json(array('success' => 'true', 'message'=> $message));
            } else {
                $message = Config::get('messages.DELETE_USER_FAILED');
                return response()->json(array('success' => 'false', 'message'=> $message));
            }
        }

    }

    public function checkUserLevel($user_id)
    {
        $id = Auth::User()->id;
        $role = Auth::User()->master_user_group_id;
        $user = $this->user->find($user_id);
        if($id == $user_id) {

        } else {
            if($role == Config::get('simplifya.MasterAdmin')) {

            } elseif($role == Config::get('simplifya.MjbMasterAdmin')) {

            } elseif($role == Config::get('simplifya.MjbManager')) {

            }
        }
    }

    /**
     * add user password function
     * @param $id
     */
    public function userRegister($id)
    {
        $user = $this->confirmation->getUserDetails(array('confirmation_code' => $id));
        if(count($user)>0) {
            if($user[0]->status == 0) {
                return Redirect::to('/')->with('message', 'User dose not exist in the system!');
            } else {
                if($user[0]->is_confirm == 0) {
                    return view('auth.register')->with(array('email' => $user[0]->email, 'id' => $user[0]->id));
                } else {
                    return Redirect::to('/')->with('message', 'You have already registered!');
                }
            }
        } else {
            return Redirect::to('/')->with('message', 'Confirmation code dose not exist in system !');
        }
    }

    /**
     * set user password
     * @param SetUserPasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setPassword(SetUserPasswordRequest $request)
    {
        $email =  $request->username;
        $user_id = $request->user_id;
        $password = $request->password;
        $data = array('password' => Hash::make($password));
        $user = $this->user->isuserExist($email);

        if($user) {
            $response = $this->user->userUpdate($user_id, $data);
            if($response) {
                $is_confirm = 1;
                $this->confirmation->updateUserConfirmation($user_id, $is_confirm);
                Auth::loginUsingId($user_id);
                if (Auth::check())
                {
                    $result = $this->getCompanyInfo();
                    Session::put('entity_type', $result['entity_type']);
                    Session::put('company_status', $result['company_status']);
                    return redirect()->intended('company/info');
                }
            }
        } else {
            $message = Config::get('messages.USER_DOSE_NOT_EXIST');
            return response()->json(array('success' => 'false', 'message'=> $message));
        }
    }

    /**
     * Get Company info
     * @return array
     */
    public function getCompanyInfo()
    {
        $company = $this->company->find(Auth::User()->company_id);
        $company_detail = array('entity_type' => $company->entity_type, 'company_status' => $company->status);
        return $company_detail;
    }

    public function sendWelcomeMail($email, $name, $layout, $subject,$data)
    {
        $send_mail = new sendMail();
        $send_mail->mailSender($layout, $email, $name, $subject, $data);
    }


    /**
     * Get Entitiy type of user.
     *
     * @param  int  $userId
     * @return $entityType
     */
    public function getUserEntitiyType($userId){
        $user = $this->user->find($userId);
        $group = $this->group->find($user->master_user_group_id);
        $entityType = $this->entityType->find($group->entity_type_id);
        return $entityType;
    }

    /**
     * User profile view
     *
     * @return view
     */
    public function userProfile(){

        $userGroup = $this->group->find(Auth::user()->master_user_group_id);

        $image = $this->upload->findWhere(array("entity_tag" => "profile", "entity_id" => Auth::user()->id, "type" => "profile"))->last();

        if(empty($image)){

            $imageUrl = $this->upload->getImageUrl(Config::get('simplifya.BUCKET_IMAGE_PATH'), Config::get('aws.PROFILE_IMG_DIR'), Config::get('aws.PROFILE_DEFAULT_IMAGE'));
        }
        else{
            $imageUrl = $this->upload->getImageUrl(Config::get('simplifya.BUCKET_IMAGE_PATH'), Config::get('aws.PROFILE_IMG_DIR'), $image->name);
        }

        return view('users.profile')->with(array('page_title' => 'User Profile', 'user' => Auth::user(), 'userGroup' => $userGroup, 'imageUrl' => $imageUrl));
    }


    /**
     * Update user profile
     *
     * @return view
     */
    public function updateProfile(){

        $user_id = Auth::user()->id;
        $name = $_POST['name'];

        $response = $this->user->update(array('name' => $name), Auth::user()->id);

        if(!empty($_POST['newPassword'])){
            $this->user->update(array('password' => Hash::make($_POST['newPassword'])), Auth::user()->id);
        }

        if($response) {
            if (!empty($file)) {
                try {
                    if (isset($result['ObjectURL'])) {
                        return Redirect::to("/dashboard")->with('success', "Successfully Updated Your Profile");
                    }

                } catch (Exception $ex) {
                    $messages = Config::get("messages.FILE_UPLOAD_ERROR");
                    return Redirect::to("/dashboard")->with('error', $messages);
                }

            } else {
                return Redirect::to("/dashboard")->with('success', "Successfully Updated Your Profile");
            }
        }
        else {
            $messages = Config::get("messages.FILE_UPLOAD_ERROR");
            return Redirect::to("/dashboard")->with('error', $messages);
        }
    }

}
