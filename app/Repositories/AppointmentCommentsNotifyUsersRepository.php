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

class AppointmentCommentsNotifyUsersRepository extends Repository
{
    public function model()
    {
        return 'App\Models\AppointmentCommentsNotifyUser';
    }

    /**
     * assing action item users
     * @param $users
     * @return mixed
     */
    public function insertUsers($users)
    {
        return DB::table('appointment_action_item_users')->insert($users);
    }

    /**
     * get user notifications
     * @param $user_id
     * @return mixed
     */
    public function getUserNotifications ($user_id)
    {
    	$notifications = $this->model->join('appointment_action_item_comments', 'appointment_action_item_comments.id', '=', 'appointment_comments_notify_users.appointment_action_item_comments_id')
    								 ->join('users', 'appointment_action_item_comments.user_id', '=', 'users.id')
                                     ->join('question_action_items', 'question_action_items.id', '=', 'appointment_action_item_comments.question_action_item_id')
                                     ->leftJoin('images', function ($q){
                                         $q->on('entity_id', '=', 'users.id')
                                             ->where('entity_tag', '=', 'profile');
                                     })
                                     ->leftJoin('appointments', 'appointment_action_item_comments.appointment_id', '=', 'appointments.id')
                                     ->where('appointment_comments_notify_users.status', '=', 0)
    								 ->where('appointment_comments_notify_users.type', '=', 1)
    								 ->where('appointment_comments_notify_users.user_id', '=', $user_id)
    								 ->select('appointment_comments_notify_users.id', 'users.name', 'appointment_action_item_comments.content', 'appointment_action_item_comments.question_action_item_id', 'appointment_action_item_comments.appointment_id','appointment_action_item_comments.created_at', 'images.name as profile_pic', 'question_action_items.name as actionItemName','appointments.inspection_number')
    								 ->get(); 								 
    	return $notifications;								 
    }

    /**
     * update notification read item statuses
     * @param $id
     */
    public function updateReadStatus ($id)
    {
    	$notification = $this->model->find($id);
    	$notification->status = 1;
    	$notification->save();
    }

    /**
     * get User notification count
     * @param $user_id
     * @return mixed
     */
    public function getUserNotificationsCount ($user_id)
    {
    	$notifications = $this->model->join('appointment_action_item_comments', 'appointment_action_item_comments.id', '=', 'appointment_comments_notify_users.appointment_action_item_comments_id')
    								 ->join('users', 'appointment_action_item_comments.user_id', '=', 'users.id')
                                     ->where('appointment_comments_notify_users.status', '=', 0)
    								 ->where('appointment_comments_notify_users.type', '=', 1)
    								 ->where('appointment_comments_notify_users.user_id', '=', $user_id)
    								 ->select(DB::raw('COUNT(appointment_comments_notify_users.id) as total'))
    								 ->first(); 								 
    	return $notifications;								 
    }

    /**
     * get report notifications
     * @param $user_id
     * @return mixed
     */
    public function getReportNotifications ($user_id)
    {
        $notifications = $this->model->join('appointments', 'appointments.id', '=', 'appointment_comments_notify_users.appointment_action_item_comments_id')
                                     ->join('users', 'appointment_comments_notify_users.user_id', '=', 'users.id')
                                     ->join('company_locations', 'company_locations.id', '=', 'appointments.company_location_id')
                                     ->where('appointment_comments_notify_users.status', '=', 0)
                                     ->where('appointment_comments_notify_users.type', '=', 2)
                                     ->where('appointment_comments_notify_users.user_id', '=', $user_id)
                                     ->select('appointment_comments_notify_users.id', 'users.name', 'appointments.inspection_number', 'company_locations.name as location', 'appointment_comments_notify_users.appointment_action_item_comments_id')
                                     ->get();                                
        return $notifications;                               
    }
}