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


class UsersRepository extends Repository
{
    public function model() {
        return 'App\Models\User';
    }

    /**
     * Check user exist by email
     * @param type $email
     * @return type
     */
    public function isuserExist($email){
        return $result = $this->model
                ->with('company')
                ->where('email', '=',$email)
                ->get()
                ->toArray();
    }

    /**
     * get user details by id
     * @param $user_id
     * @return mixed
     */
    public function getUserById($user_id)
    {
        return $this->model
                    ->where('id',$user_id)
                    ->get()
                    ->toArray();

    }

    public function getUserByCompanyId($company_id) {
        return $this->model
                ->with('companyUser','masterUserGroup','companyUser.CompanyLocation')
                ->where('company_id', '=' ,$company_id)
                ->where('is_invite', '=' , 1)
                ->where('status', '!=' , 0)
                ->get();
    }

    public function getUserDetailByCompanyId($company_id) {
        return $this->model
            ->where('company_id', '=' ,$company_id)
            ->where('is_invite', '=' , 0)
            ->where('status', '!=' , 0)
            ->get();
    }

    public function getAllUsers($company_id)
    {
        return $this->model
                    ->with('masterUserGroup')
                    ->where('company_id', '=' ,$company_id)
                    ->where('status', '=' , 1)
                    ->get();
    }

    public function getUserByUserId($user_id)
    {
        return $this->model
                    ->with('masterUserGroup', 'companyUser.CompanyLocation')
                    ->where('id', '=', $user_id)
                    ->where('is_invite', '=', 1)
                    ->get();
    }

    public function getAllUserByUserId($user_id)
    {
        return $this->model
            ->with(array('masterUserGroup', 'companyUser.CompanyLocation'))
            ->where('id', '=', $user_id)
            ->get();
    }

    public function getCompanyDetails($user_id)
    {
        return $this->model
                ->with('company', 'company.masterEntityType')
                ->where('id', '=', $user_id)
                ->get();

    }

    public function changeUserStatus($user_id, $status)
    {
        return $this->model
            ->where('id', '=', $user_id)
            ->update(['status' => $status]);
    }

    public function userUpdate($user_id, $data)
    {
        return $this->model
            ->where('id', '=', $user_id)
            ->update($data);
    }
    public static function getActions($sql)
    {
        $result = DB::select($sql);
        return $result;
    }

    public static function getTotalNumber($company_id)
    {
        $result = DB::select("SELECT COUNT(`id`) as count FROM `users` WHERE `company_id` =".$company_id);
        return $result;
    }

    public static function currentRow()
    {
        $result = DB::select('SELECT FOUND_ROWS() as FilteredTotal');
        return $result;
    }

    public function changeUserStatusByUserId($user_id, $status)
    {
        return $this->model;
    }

    public function searchUsers($user_group_id, $name, $status ,$company_id)
    {
        $query =  $this->model->where('company_id', $company_id);
        if($user_group_id != '') {
            $query->where('master_user_group_id', $user_group_id);
        }
        if($status != '') {
            $query->where('status', $status);
        } else {
            //$query->where('status', '!=',0);
        }
        if($name != '') {
            $query->where('name', 'LIKE', '%'.$name.'%');
        }
        $query->with('masterUserGroup');
       $result = $query->get();
       return $result;
    }

    /**
     * check if user already assigned to roster item
     *
     * @param $dataset
     * @param $company_id
     * @param $appointment_id
     * @param $action_item_id
     * @return mixed
     */
    public function checkUserAssigned($dataset, $appointment_id, $action_item_id){

         $result = DB::table('appointment_action_item_users')
            ->where('appointment_id', '=', $appointment_id)
            ->where('question_action_item_id', '=', $action_item_id)
            ->whereIn('user_id', $dataset)
            ->get();

        return $result;
    }

    /**
     * Get location based company user list with user profile pictures
     *
     * @param $dataset
     */
    public function getLocationUsersWithAvatar($dataset, $company_id){
      $query = DB::table('users');
      $query->leftJoin('images', function ($q){
         $q->on('images.entity_id', '=', 'users.id')
            ->where('images.type', '=', 'profile');
      });
      $query->where('users.company_id', '=', $company_id);
      $query->where('users.status', '=', 1);
      $query->whereIn('users.id', $dataset);
      $query->orWhere(function ($q) {
               $q->where('users.master_user_group_id', '=', 3)
               ->where('users.master_user_group_id', '=', 4);
            });
      $query->select(
                  'users.id',
                  'users.name', 
                  'users.id',
                  'images.name as image_name'
               );
      return $result = $query->get();
    }
    
    /**
     * Get location based company user list 
     *
     * @param $dataset
     */
    public function getLocationUsers($dataset, $company_id){
        return $this->model
            ->where('company_id', '=', $company_id)
            ->where('status', '=', 1)
            ->whereIn('id', $dataset)
            ->orWhere(function ($query) {
                $query->where('master_user_group_id', '=', 3)
                      ->where('master_user_group_id', '=', 4);
            })
            ->get();
    }

    public function removeAssignedUser($appointment_id, $action_id){
        $result = DB::table('appointment_action_item_users')
            ->where('appointment_id', '=', $appointment_id)
            ->where('question_action_item_id', '=', $action_id)
            ->delete();
        return $result;
    }
    
    /**
    * Clear access token & sessions
    * @param type $id
    * @param type $token
    * @return boolean
    */
   public function RevokeAccessToken($token){
		$temp = array();
		$response = false;

		$_token = DB::table('oauth_access_tokens')
               ->where('id', '=', $token)
               ->get();
      
      if(!empty($_token)) {
         
			$result =  DB::table('oauth_sessions')
						->where('id', '=', $_token[0]->session_id)
						->delete();
		
			if($result) {
				$response = true;
			}
      }
      
      return $response;
      
   }


    /**
     * Find users for MailChimp
     * @param type $companyList
     * @return array
     */
    public function findMailChimpUsers($companyList)
    {
        $qry = $this->model->with(array('masterUserGroup'))->with(array('company'))->where('id', '!=', "");

        if($companyList != ""){
            $qry->where('company_id', $companyList);
        }

        $response = $qry->orderBy('created_at', 'desc')->get();

        return $response;
    }

    public function getUserEmailById($email_user)
    {
        return $this->model
                ->select('name', 'email')
                ->whereIn('id',$email_user)
                ->get();
    }
    
   /**
   * Get device tokens by user id
   * @param type $user_id
   * @return type
   */
   public function getDeviceTokens($user_id){
      return $result = DB::table('device_tokens')->whereIn('user_id', $user_id)->get();
   }

    /**
     * Unassign users from device tokens
     * @param $device_type
     * @param $device_token
     */
   public function UnAssignDeviceToken($device_type, $device_token){
       $dataset = ['user_id' => 0];
       $status = DB::table('device_tokens')
           ->where('device_type', '=', $device_type)
           ->where('device_token', '=', $device_token)
           ->update($dataset);
       return $status;
   }

    public function findCompanyUsers($entityType, $companyList, $user_name)
    {
        $qry = $this->model->with(array('masterUserGroup'))->with(array('company'))->where('id', '!=', "");

        if($companyList != ""){
            $qry->where('company_id', $companyList);
        }
        if($user_name != ""){
            $qry->where('name', 'like', '%' . $user_name . '%');
        }

        $response = $qry->orderBy('created_at', 'desc')->get();

        return $response;
    }

    /**
     * Get all active master admin users by company id
     * @param $company_id
     * @param $group_ids
     * @return mixed
     */
    public function getAllMasterAdminUsersBy($company_id, $group_ids) {
        return $this->model
            ->where('company_id', '=' ,$company_id)
            ->where('status', '=' , 1)
            ->whereIn('master_user_group_id', $group_ids)
            ->get();
    }

    public function getRosterUsers($company_id){
        return $this->model
            ->where('company_id',$company_id)
            ->whereIn('master_user_group_id',array(3,4))
            ->select('name','id as value')
            ->get();
    }
}