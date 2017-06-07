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

class AppointmentActionItemCommentsRepository extends Repository
{
    /**
     * @return string
     */
    public function model()
    {
        return 'App\Models\AppointmentActionItemComment';
    }

    /**
     * Get all action item comments
     * @param $comment_arr
     * @return mixed
     */
    public function getAllActionItemComments($comment_arr)
    {

        $result =  $this->model->with('user')
            ->where('appointment_id', $comment_arr['appointment_id'])
            ->whereIn('appointment_action_item_comments.question_action_item_id', $comment_arr['question_action_item_id'])
            ->where('appointment_action_item_comments.status', '=', $comment_arr['status'])
            ->with([
                'image' => function ($query) {
                    $query->where('images.entity_tag', '=', 'comment_photo');
                }
            ])
            ->get();
        
        return $result;
    }

    /**
     * Get action comments
     * @param $appointment_id
     * @param $action_id
     * @param $user_id
     * @return mixed
     */
    public function getActionComments ($appointment_id, $action_id, $user_id)
    {
        $comments = $this->model->join('users', 'users.id', '=', 'appointment_action_item_comments.user_id')
                                ->leftJoin('appointment_comments_notify_users', function ($q) use ($user_id){
                                   $q->on('appointment_comments_notify_users.appointment_action_item_comments_id', '=', 'appointment_action_item_comments.id')
                                   ->where('appointment_comments_notify_users.type', '=', 1)
                                   ->where('appointment_comments_notify_users.user_id', '=', $user_id);
                                })
                                ->leftJoin('images', function ($q){
                                   $q->on('appointment_action_item_comments.id', '=', 'images.entity_id')
                                   ->where('images.entity_tag', '=', 'comment_photo');
                                })
                                ->where('appointment_action_item_comments.appointment_id', '=', $appointment_id)
                                ->where('appointment_action_item_comments.question_action_item_id', '=', $action_id)
                                ->select('images.name as image_name', 'appointment_comments_notify_users.status', 'users.name', 'appointment_action_item_comments.id', 'appointment_action_item_comments.created_at', 'appointment_action_item_comments.content', 'appointment_action_item_comments.location')
                                ->orderBy('appointment_action_item_comments.id', 'asc')
                                ->get();
        return $comments;                        
    }
    
    /**
     * Mark comments as read
     * @param $appointment_id
     * @param $action_id
     * @param $user_id
     * @return $status
     */
    public function readComments($appointment_id, $action_id, $user_id){
       // Prepare dataset
       $dataset = [
                  'status' => 1
               ];
       
       // Get action item comments
       $questions_list = DB::table('appointment_action_item_comments')
               ->where('appointment_id', '=', $appointment_id)
               ->where('question_action_item_id', '=', $action_id)
               ->lists('id');

       // mark comments as read
       $status = DB::table('appointment_comments_notify_users')
               ->where('type', '=', 1)
               ->where('user_id', '=', $user_id)
               ->whereIn('appointment_action_item_comments_id', array_values($questions_list))
               ->update($dataset);
       
       return $status;
       
    }


    public function getUnreadCommentCount($appointment_id, $action_id, $user_id){

    }

    public function createActionItemComment($dataset)
    {
        return $this->model->create($dataset);
    }

}