<?php
/**
 * Created by PhpStorm.
 * User: Nishan
 * Date: 5/9/2016
 * Time: 9:48 AM
 */

namespace App\Repositories;

use Bosnadev\Repositories\Eloquent\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;


class AppointmentQuestionRepository extends Repository
{
    public function model()
    {
        return 'App\Models\AppointmentQuestion';
    }

    /**
     * Get all appointment answered questions list
     * @param $appointment_id
     */
    public function getAllAnsweredQuestions($appointment_id)
    {
        $result = $this->model->where('appointment_id', $appointment_id)->whereNotNull('master_answer_id')->get();
        return $result;
    }

    /**
     * Get all non compliance action items
     * @param $appointment_id
     * @return mixed
     */
    public function getAllNonComplianceQuestions($appointment_id, $master_answer_arr, $answer_value_id, $user_id='', $user_role='',$category_id =0, $order_by='')
    {
        $query = DB::table('appointment_questions');
        $query->select('question_action_items.name as action_item_name','question_action_items.id as action_item_id', 'question_answers.answer_value_id', 'appointment_questions.master_answer_id as master_answer_id','question_action_items.question_id','questions.id','questions.question','question_classifications.option_value','master_classification_options.id','master_classification_options.name','appointment_questions.comment',DB::raw('GROUP_CONCAT(users.name SEPARATOR \',\') as assigned_users'));
        $query->join('question_answers', 'appointment_questions.question_id', '=', 'question_answers.question_id');
        $query->join('question_action_items', 'appointment_questions.question_id', '=', 'question_action_items.question_id');
        $query->join('questions', 'questions.id', '=', 'question_action_items.question_id');
        $query->join('question_classifications', 'questions.id', '=', 'question_classifications.question_id');
        $query->join('master_classification_options', 'master_classification_options.id', '=', 'question_classifications.option_value');
        if($user_role != 4) {
            $query->leftJoin('appointment_action_item_users', function ($join) {
                $join->on('appointment_action_item_users.appointment_id', '=', 'appointment_questions.appointment_id');
                $join->on('question_action_items.id', '=', 'appointment_action_item_users.question_action_item_id');
            });
        }

        if($user_role == 4){
            $query->join('appointment_action_item_users', 'appointment_questions.appointment_id', '=', 'appointment_action_item_users.appointment_id');
            $query->whereRaw('appointment_action_item_users.question_action_item_id = question_action_items.id');
            $query->where('appointment_action_item_users.user_id', '=', $user_id);
        }
        $query->leftJoin('users', 'appointment_action_item_users.user_id', '=', 'users.id');

        if($master_answer_arr != '')
        {
            $query->whereIn('question_answers.id', $master_answer_arr);
        }
        if($answer_value_id != '')
        {
            $query->whereIn('question_answers.answer_value_id', $answer_value_id);
        }
        $query->where('appointment_questions.master_answer_id', '!=', 0);
        if($appointment_id != "")
        {
            $query->where('appointment_questions.appointment_id', '=', $appointment_id);
        }
        $query->where('question_classifications.entity_tag', '=', 1);
        if($category_id!=0){
        $query->where('master_classification_options.id', '=', $category_id);
        }
        if($order_by == "category")
        {
            $query->orderBy('master_classification_options.name', 'asc');
        }
        $query->groupBy('question_action_items.id');
        $result = $query->get();

        return $result;
    }

    /**
     * Get all action items assignee
     * @param $action_id, $appointment_id
     * @return mixed
     */
    public function getActionItemsAssignee($action_id, $appointmentId,$user_id='', $user_role='')
    {
        $query = DB::table('users');
        $query->select(DB::raw('GROUP_CONCAT(users.name SEPARATOR \',\') as assigned_users'));
        $query->join('appointment_action_item_users', 'users.id', '=', 'appointment_action_item_users.user_id');

        if($user_role == 4){
          $query->join('appointment_action_item_users', 'appointment_questions.appointment_id', '=', 'appointment_action_item_users.appointment_id');
          $query->whereRaw('appointment_action_item_users.question_action_item_id = question_action_items.id');
          $query->where('appointment_action_item_users.user_id', '=', $user_id);
        }


        $query->where('appointment_action_item_users.appointment_id', '=', $appointmentId);
        $query->where('appointment_action_item_users.question_action_item_id', '=', $action_id);

        $result = $query->get();

        return $result;
    }
    /**
     * Get all Navigation Categories
     * @param $appointment_id
     * @return mixed
     */
    public function getAllNavigationCategories($appointment_id, $master_answer_arr, $answer_value_id, $user_id='', $user_role='')
    {
        $query = DB::table('appointment_questions');
        $query->select('master_classification_options.id as id','master_classification_options.name as name');
        $query->join('question_answers', 'appointment_questions.question_id', '=', 'question_answers.question_id');
        $query->join('question_action_items', 'appointment_questions.question_id', '=', 'question_action_items.question_id');
        $query->join('questions', 'questions.id', '=', 'question_action_items.question_id');
        $query->join('question_classifications', 'questions.id', '=', 'question_classifications.question_id');
        $query->join('master_classification_options', 'master_classification_options.id', '=', 'question_classifications.option_value');

        if($user_role == 4){
          $query->join('appointment_action_item_users', 'appointment_questions.appointment_id', '=', 'appointment_action_item_users.appointment_id');
          $query->whereRaw('appointment_action_item_users.question_action_item_id = question_action_items.id');
          $query->where('appointment_action_item_users.user_id', '=', $user_id);
        }

        $query->whereIn('question_answers.id', $master_answer_arr);
        $query->whereIn('question_answers.answer_value_id', $answer_value_id);
        $query->where('appointment_questions.master_answer_id', '!=', 0);
        $query->where('appointment_questions.appointment_id', '=', $appointment_id);
        $query->where('question_classifications.entity_tag', '=', 1);
        $query->groupBy('master_classification_options.name');
        $result = $query->get();

        return $result;
    }
     
    /**
     * Find answer appointment questions
     * @param type $appointment_id
     * @return type
     */
    public function findAnsweredAppointmentQuestions($appointment_id, $user_id='', $user_role='')
    {
       
      if($user_role == 2 and $user_role == 3){
         $query = DB::table('appointment_questions');
         $query->join('appointment_action_item_users', 'appointment_questions.appointment_id', '=', 'appointment_action_item_users.appointment_id');
         $query->where('appointment_action_item_users.user_id', '=', $user_id); 
         $query->whereIn('appointment_questions.appointment_id', array($appointment_id));
         $query->where('master_answer_id', '!=', 0);
         return $results = $query->get();
      }else{
         return $this->model
                    ->whereIn('appointment_id', array($appointment_id))
                    ->where('master_answer_id', '!=', 0)
                    ->get();
      }
      
    }

    /**
     * Get unread status records
     * @param type $action_item_arr
     * @param type $user_id
     * @return type
     */
    public function getUnreadRecords($appointment_id, $action_item_arr, $user_id){
      $query = DB::table('appointment_action_item_comments');
      $query->join('appointment_comments_notify_users', function ($q) use ($appointment_id){
          $q->on('appointment_comments_notify_users.appointment_action_item_comments_id', '=', 'appointment_action_item_comments.id')
              ->where('appointment_action_item_comments.appointment_id', '=', $appointment_id);
      });
      $query->where('appointment_comments_notify_users.user_id', '=', $user_id);
      $query->whereIn('appointment_action_item_comments.question_action_item_id', $action_item_arr);
      $query->where('appointment_comments_notify_users.status', '=', 0);
      $query->where('appointment_comments_notify_users.type', '=', 1);
      $query->select('appointment_action_item_comments.question_action_item_id', 'appointment_comments_notify_users.appointment_action_item_comments_id');
      $results = $query->get();
      return $results;
    }
    
    /**
     * Check appointment questions
     * @param type $appointment_id
     * @param type $question_id
     * @return type
     */
    public function checkAppointmentQuestion($appointment_id, $question_id)
    {
        return $this->model
                    ->where('appointment_id', $appointment_id)
                    ->where('question_id', $question_id)
                    ->get()
                    ->toArray();
    }

    public function storeQuestionAnswers($appointment_id, $question_id, $answer_id, $q_comment, $user_id)
    {
        $response =  $this->model
            ->where('appointment_id', $appointment_id)
            ->where('question_id', $question_id)
            ->update(['comment' => $q_comment, 'master_answer_id' => $answer_id, 'updated_by' => $user_id]);
        return $response;
    }
    
    /**
     * Get data for appointment reports list
     * @param type $appointment_id
     * @return type
     */
    public function getAppointmentReportList($appointment_id,$order_by=''){
      $query = DB::table('question_answers');
      $query->join('appointment_questions', function ($q) use($appointment_id){
         $q->on('appointment_questions.master_answer_id', '=', 'question_answers.id')
         ->where('appointment_questions.appointment_id', '=', $appointment_id);
      });

      $query->join('appointments', 'appointments.id', '=', 'appointment_questions.appointment_id');

      $query->join('appointment_classifications', function ($q){
         $q->on('appointment_questions.appointment_id', '=', 'appointment_classifications.appointment_id')
         ->where('appointment_classifications.entity_type', '=', 'AUDIT_TYPE');
      });

      $query->join('master_answer_values', 'master_answer_values.id', '=', 'question_answers.answer_value_id');
      $query->join('questions', 'questions.id', '=', 'question_answers.question_id');

      $query->join('question_classifications', function ($q){
         $q->on('question_classifications.question_id', '=', 'questions.supper_parent_question_id')
         ->where('question_classifications.entity_tag', '=', 1);
      });

      $query->join('master_classification_options', 'master_classification_options.id', '=', 'question_classifications.option_value');

      $query->leftJoin('question_action_items', 'question_action_items.question_id', '=', 'questions.id');
      $query->leftJoin('appointment_action_item_users', function($q) use($appointment_id){
         $q->on('appointment_action_item_users.question_action_item_id', '=', 'question_action_items.id')
         ->where('appointment_action_item_users.appointment_id', '=', $appointment_id);
      });
      $query->leftJoin('users', 'users.id', '=', 'appointment_action_item_users.user_id');
      $query->leftJoin('images', function($q){
         $q->on('images.entity_id', '=', 'appointment_questions.id')
         ->where('images.entity_tag', '=', 'question_photo');
      });
      if($order_by == "category")
      {
            $query->orderBy('master_classification_options.name', 'asc');
      }
      else
      {
          $query->orderBy('appointment_questions.question_id', 'asc');
      }
      $query->select(
                  'appointment_questions.question_id',
                  'appointment_questions.appointment_id',
                  'questions.parent_question_id',
                  'master_answer_values.name as answer_value_name',
                  'question_answers.id as answer_id',
                  'question_answers.answer_value_id as answer_value_id',
                  'questions.parent_question_id',
                  'questions.question',
                  'questions.explanation',
                  'question_action_items.id as action_item_id',
                  'question_action_items.name as action_item_name',
                  'question_action_items.status as action_item_status',
                  'appointment_action_item_users.user_id as action_item_user_id',
                  'master_classification_options.id as category_id',
                  'master_classification_options.name as category_name',
                  'master_classification_options.option_value as category_option_value',
                  'images.name as image_name',
                  'users.name as user_name',
                  'appointment_questions.comment',
                  'appointment_classifications.option_value',
                  'appointments.report_status'
              );
       $result = $query->get();
        return $result;
    }
}