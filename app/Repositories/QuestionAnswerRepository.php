<?php namespace App\Repositories;

use Bosnadev\Repositories\Eloquent\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class QuestionAnswerRepository extends Repository{
    /**
     * @return string
     */
    public function model()
    {
        return 'App\Models\QuestionAnswer';
    }

    public function findQuestionAnsswerArray($questionIds){
        return $this->model
            ->join('master_answers', 'master_answers.id', '=', 'question_answers.answer_id')
            ->join('master_answer_values', 'master_answer_values.id', '=', 'question_answers.answer_value_id')
            ->whereIn('supper_parent_question_id', $questionIds)
           ->select('question_answers.id', 'question_answers.is_deleted', 'question_answers.question_id', 'master_answers.id as answer_id', 'master_answers.name as answer_name', 'master_answer_values.id as answer_value_id', 'master_answer_values.name as answer_value_name')
            ->get();
    }

    public function findQuestionOtherAnswersArray($questionIds, $answer_id, $answer_value_id){
        if($answer_id==null) {
            $result = $this->model
                ->join('master_answers', 'master_answers.id', '=', 'question_answers.answer_id')
                ->join('master_answer_values', 'master_answer_values.id', '=', 'question_answers.answer_value_id')
                ->whereIn('supper_parent_question_id', $questionIds)
                ->where('question_answers.answer_value_id', $answer_value_id)
                ->select('question_answers.id', 'question_answers.question_id', 'master_answers.id as answer_id',
                    'master_answers.name as answer_name', 'master_answer_values.id as answer_value_id',
                    'master_answer_values.name as answer_value_name')
                ->get();
            return $result;
        }else{
            if($answer_value_id!=""){
                //echo json_encode($answer_value_id);die;
                $returnData = $this->model
                    ->join('master_answers', 'master_answers.id', '=', 'question_answers.answer_id')
                    ->join('master_answer_values', 'master_answer_values.id', '=', 'question_answers.answer_value_id')
                    ->whereIn('question_answers.question_id', $questionIds)
                    ->whereIn('question_answers.id', $answer_id)
                    ->select('question_answers.id', 'question_answers.question_id', 'master_answers.id as answer_id',
                        'master_answers.name as answer_name', 'master_answer_values.id as answer_value_id',
                        'master_answer_values.name as answer_value_name')
                    ->get();
            }else {
                $returnData = $this->model
                    ->join('master_answers', 'master_answers.id', '=', 'question_answers.answer_id')
                    ->join('master_answer_values', 'master_answer_values.id', '=', 'question_answers.answer_value_id')
                    ->whereIn('question_answers.question_id', $questionIds)
                    ->whereIn('question_answers.id', $answer_id)
                    ->select('question_answers.id', 'question_answers.question_id', 'master_answers.id as answer_id',
                        'master_answers.name as answer_name', 'master_answer_values.id as answer_value_id',
                        'master_answer_values.name as answer_value_name')
                    ->get();
            }
            return $returnData;

        }
    }
}