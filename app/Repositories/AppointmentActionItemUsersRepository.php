<?php
/**
 * Created by PhpStorm.
 * User: Harsha
 * Date: 5/6/2016
 * Time: 3:38 PM
 */

namespace App\Repositories;

use Bosnadev\Repositories\Eloquent\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class AppointmentActionItemUsersRepository extends Repository
{
    /**
     * @return string
     */
    public function model()
    {
        return 'App\Models\AppointmentActionItemUser';
    }

    /**
     * Store assied action item users
     * @param $users
     * @return mixed
     */
    public function insertUsers($users)
    {
        return DB::table('appointment_action_item_users')->insert($users);
    }

    /**
     * Get related users from notification
     * @param type $aId
     * @param type $qId
     * @return type
     */
    public function getNotifiedUsers ($aId, $qId)
    {
        // Init users array
    	$users = [];

        // Get All users
    	$empUsers = $this->model->join('appointments', 'appointments.id', '=', 'appointment_action_item_users.appointment_id')
                                    ->where('appointment_id', '=', $aId)
                                    ->where('question_action_item_id', '=', $qId)
                                    ->select('appointment_action_item_users.user_id')
                                    ->get();
      
        // Get admin users
  		$masterUsers = DB::table('appointments')->join('users', 'users.company_id', '=', 'appointments.to_company_id')
  												->where('appointments.id', '=', $aId)
  												->where('users.master_user_group_id', '=', 2)
                                    ->select('users.id')
  												->get();
      
      // Get manager users
      $managerUsers = DB::table('appointments')
                                    ->join('company_users', 'company_users.location_id', '=', 'appointments.company_location_id')
                                    ->join('users', 'users.id', '=', 'company_users.user_id')
  												->where('appointments.id', '=', $aId)
  												->where('users.master_user_group_id', '=', 3)
                                    ->select('users.id')
  												->get();
      
      // Push manager ids to users list
      foreach ($empUsers as $empUser){
         $users[] = $empUser->user_id;
      }
      
      // Push manager ids to users list
      foreach ($masterUsers as $masterUser){
         $users[] = $masterUser->id;
      }
      
      // Push manager ids to users list
      foreach ($managerUsers as $managerUser){
         $users[] = $managerUser->id;
      }

  		return array_unique($users);
    }

    /**
     * un-assign action item users
     * @param $data
     * @return $query
     */
    public function deleteActionItemUsers($data)
    {
        foreach ($data as $value){
            $query = DB::table('appointment_action_item_users');
            $query->where('appointment_id', '=', $value['appointment_id']);
            $query->where('question_action_item_id', '=', $value['question_action_item_id']);
            $query->where('user_id', '=', $value['user_id']);
            $query->delete();
        }
        return $query;
    }

    public function getActionItemsForUser($user_id)
    {
        $data = DB::table('appointment_action_item_users')
                    ->where('appointment_action_item_users.user_id', '=', $user_id)
                    ->select('appointment_action_item_users.appointment_id', 'appointment_action_item_users.question_action_item_id')
                    ->orderBy('appointment_action_item_users.created_at', 'asc')
                    ->groupBy('appointment_action_item_users.appointment_id')
                    ->get();
        return $data;
    }

    public function getActionItemsForAppointment($appointment_id, $user_id)
    {
        $data = DB::table('appointment_action_item_users')
                    ->join('question_action_items', 'question_action_items.id', '=', 'appointment_action_item_users.question_action_item_id')
                    ->leftJoin('appointments', 'appointment_action_item_users.appointment_id', '=', 'appointments.id')
                    ->where('appointment_action_item_users.user_id', '=', $user_id)
                    ->where('appointment_action_item_users.appointment_id', '=', $appointment_id)
                    ->select('question_action_items.name','appointments.inspection_number')
                    ->groupBy('question_action_items.name')
                    ->get();

        return $data;
    }
}