<?php namespace App\Repositories;

use App\Models\MasterClassification;
use App\Models\MasterClassificationOption;
use Bosnadev\Repositories\Eloquent\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class QuestionRepository extends Repository{
    public function model()
    {
        return 'App\Models\question';
    }

    public function categoryOptionInsert($option_set, $category_id)
    {
        $dataset = array();
        $m_classification_option = array();
        //remove all clasification ids
        //$remove_category = DB::table('master_classification_options')->where('classification_id', $category_id)->delete();

//        foreach ($option_set as $key=>$value)
//        {
//            $dataset = array(
//                'name'          => $key,
//                'option_value'  => $value,
//                'classification_id'   => $category_id,
//                'status'        => 1
//            );
//
//            $m_classification_option = MasterClassificationOption::create($dataset);
//        }

        foreach ($option_set as $item) {
            if(isset($item['name'])){
                $dataset = array(
                    'name' => $item['name'],
                    'classification_id' => $category_id,
                    'status' => 1
                );
            }else{
                $dataset = array(
                    'name' => $item,
                    'classification_id' => $category_id,
                    'status' => 1
                );
            }

            $m_classification_option = MasterClassificationOption::create($dataset);
        }

        if($m_classification_option) {
            return $m_classification_option->id;
        }else{
            return true;
        }
    }

    public function categoryOptionUpdate($option_withid, $option_whithoutid, $category_id)
    {
        $dataset = array();
        $m_classification_option = array();
        $option_ids = "";
        if($option_withid!="" || $option_withid!=null){
            foreach ($option_withid as $item){
                $mclassification_option = MasterClassificationOption::where('id', '=', $item['id'])->update(array('name' => $item['name'],'parent_id'=>$item['parent_id']));
                $option_ids .= $item['id']. ',';
            }
            $option_ids = rtrim($option_ids, ',');
            $delete = MasterClassificationOption::where('classification_id', '=', $category_id)->whereRaw("`id` NOT IN (".$option_ids.")")->update(array('status' => 3));
        }

        if($option_whithoutid!="" || $option_whithoutid!=null) {

            foreach ($option_whithoutid as $item){
                $dataset = array(
                    'name' => $item['name'],
                    'classification_id' => $category_id,
                    'status' => 1,
                    'parent_id'=>$item['parent_id']
                );

                $m_classification_option = MasterClassificationOption::create($dataset);
            }
        }

        if($m_classification_option) {
            return $m_classification_option->id;
        }else{
            return true;
        }
    }

    public function questionCategoryInsert($dataset)
    {
        $m_classification = MasterClassification::create($dataset);
        return $m_classification->id;
    }
    
    public function visibilityInsert($visible_on, $classification_id)
    {
        $dataset = array();

        if(is_array($visible_on)) {
            foreach ($visible_on as $item) {
                $dataset[] = array(
                    'entity_type_id' => $item,
                    'classification_id' => $classification_id,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id
                );
            }
        }else{
            $dataset[] = array(
                'entity_type_id' => $visible_on,
                'classification_id' => $classification_id,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id
            );
        }

        return DB::table('master_classification_entity_allocations')->insert($dataset);
    }

    public function searchQuestions($questionName, $status, $display,$sort='desc',$sortType='created_at'){
//        $qry = $this->model->where('parent_question_id', 0)->where('is_archive', 0)->where('is_deleted', 0);
        $qry = $this->model->where('is_archive', 0)->where('is_deleted', 0);
        if($questionName != ""){
            $qry->where('question', 'LIKE', '%'.$questionName.'%');
        }
        if($status != ""){
            $qry->where('status', $status);
        }
        if($display != ""){
            $qry->where('is_draft', $display);
        }

        return $qry->orderBy($sortType, $sort)->get();

    }

    public function getQuestion($ids,$sort='desc',$sortType='created_at'){
        return $this->model
            ->leftJoin('users as u1','u1.id','=','questions.created_by')
            ->leftJoin('users as u2','u2.id','=','questions.updated_by')
            ->select('questions.*','u1.name as createdUser','u2.name as updatedUser')
            ->whereIn('questions.id', $ids)->orderBy('questions.'.$sortType, $sort)->get();
    }

    /**
     * Find all questions for rexport
     * @return array
     */
    public function getQuestionsList($is_draft=0,$is_archive=0,$status=1){

        $sql = "SELECT
                        questions.id,
                        questions.question,
                        questions.explanation,
                        questions.question_answer_id,
                        questions.parent_question_id,
                        questions.master_question_id,
                        questions.previous_question_id,
                        questions.supper_parent_question_id,
                        `question_action_items`.`id` AS `action_item_id`,
                        `question_action_items`.`name` AS `action_item_name`,
                        `question_action_items`.`status` AS `action_item_status`,
                        `master_classification_options`.`id` AS `category_id`,
	                    `master_classification_options`.`name` AS `category_name`,
	                    `master_classification_options`.`option_value` AS `category_option_value`,
	                    questions.is_archive,
                        questions.is_draft,
                        questions.`status`
                        FROM
                        questions
                        INNER JOIN question_action_items ON questions.id = question_action_items.question_id
                        INNER JOIN question_classifications ON question_classifications.question_id = questions.id and (question_classifications.entity_tag = '1' or question_classifications.entity_tag='SUB_CATEGORY')
                        INNER JOIN master_classification_options ON question_classifications.option_value = master_classification_options.id
                        WHERE                        
                         questions.`is_deleted` = 0    AND
                        question_classifications.entity_tag = 1";
        if($is_draft == '0')
        {
            $sql .= " AND questions.`is_draft` = 0 ";
        }
        if($is_archive == '0')
        {
            $sql .= " AND questions.`is_archive` = 0 ";
        }
        if($status == '1')
        {
            $sql .= " and questions.`status`= 1 ";
        }
        //\Log::info('================ START PARENT================'.print_r($sql,true)); die;
        $query=DB::select($sql);
        return $query;
    }

    public function findQuestionArray($questions){
        return $this->model->whereIn('supper_parent_question_id', $questions)->where('is_draft', 0)->where('is_archive', 0)->where('is_deleted', 0)->where('status', 1)->get();
    }

    public function findAllQuestionArray($questions){
        return $this->model->whereIn('id', $questions)->get();
    }

    public function findChildQuestions($preQuestion){
        return $this->model->where('supper_parent_question_id', '=', $preQuestion->supper_parent_question_id)->where('parent_question_id', '=', $preQuestion->id)->get();
    }

    public function findQuestionVersions($masterQuestionId, $questionId){
        return $this->model->with(array('user'))->where('master_question_id', '=', $masterQuestionId)->where('id', '!=', $questionId)->orderBy('version_no', 'desc')->get();
    }

    public function findQuestionIpadArray($questions){
        return $this->model->whereIn('supper_parent_question_id', $questions)->where('status', 1)->where('is_deleted', 0)->get();
    }

    /**
     * Find questions for checklist
     * @param $questions
     * @return array
     */
    public function findQuestionChecklistArra($questions,$city_only=false,$city=''){

        if($city_only == "true")
        {
            $questions_updated = array();
                $sql = "SELECT
                        questions.id,
                        count(question_classifications.entity_tag) as city_count
                        FROM
                        questions
                        INNER JOIN question_classifications ON questions.id = question_classifications.question_id
                        where question_classifications.entity_tag = 'CITY'
                        AND questions.supper_parent_question_id IN (". implode(",", $questions) .")
                        AND questions.parent_question_id='0'
                        GROUP BY questions.id";

                $results = DB::select($sql);

                foreach($results as $result)
                {
                    if($result->city_count == 1)
                    {
                        $sql2 = "select qc.option_value
                                  from question_classifications as qc
                                  where qc.question_id='".$result->id."'
                                  AND qc.entity_tag = 'CITY'";
                        $city_id = DB::select($sql2);
                        if($city_id[0]->option_value == $city)
                        {
                            array_push($questions_updated,$result->id);
                        }
                    }

                }


            $questions = $questions_updated;
        }

        //die;

        //\Log::info("=============category....===============".print_r($questions,true));

        return $this->model->whereIn('supper_parent_question_id', $questions)
            ->where('is_draft', 0)->where('is_archive', 0)
            ->where('status', 1)
            ->where('parent_question_id', 0)
            ->where('is_deleted', 0)
            ->get();
    }
    
    
    public function getAnswersCount($appointment_id, $category_id){
      $query = DB::table('question_classifications');
      $query->join('appointment_questions', function($join){
                     $join->on('question_classifications.question_id', '=', 'appointment_questions.id')
                     ->where('question_classifications.entity_tag', '=', '1');
                  }
               );
      $query->leftJoin('question_answers', 'appointment_questions.master_answer_id', '=', 'question_answers.id');
      $query->where('appointment_questions.appointment_id', '=', $appointment_id);
      // Filter by category
      if($category_id != '' and $category_id != null and $category_id != 0){
         $query->where('question_classifications.id', '=', $category_id);
      }
      $query->select(
                  'question_classifications.id as category_id',
                  'question_answers.answer_value_id',
                  'appointment_questions.master_answer_id'
              );
      $results = $query->get();
      return $results;
    }

    /**
     * Find all sub question which are belongs to parent question
     * @param $parentQuestion
     * @return mixed
     * @internal param $preQuestion
     */
    public function findOnlySubQuestions($parentQuestion) {
        return $this->model->where('supper_parent_question_id', '=', $parentQuestion->supper_parent_question_id)->where('parent_question_id', '!=', 0)->get();
    }

    public function findAllInQuestion($parentQuestion) {
        return $this->model->where('supper_parent_question_id', '=', $parentQuestion->supper_parent_question_id)->select('id')->get();
    }

    public function getSubQuestionForQuestionList($parentId){
        return $this->model->leftJoin('users as u1','u1.id','=','questions.created_by')
            ->leftJoin('users as u2','u2.id','=','questions.updated_by')
            ->select('questions.*','u1.name as createdUser','u2.name as updatedUser')
            ->where(['questions.parent_question_id' => $parentId, 'questions.is_archive' => 0])->get();
    }

    public function getQuestionList(){
        $appointment_id = "";
        $query = DB::table('question_answers');
        $query->join('appointment_questions', function ($q) use($appointment_id){
            $q->on('appointment_questions.master_answer_id', '=', 'question_answers.id');
                //->where('appointment_questions.appointment_id', '=', $appointment_id);
        });

        $query->join('appointments', 'appointments.id', '=', 'appointment_questions.appointment_id');

        $query->join('appointment_classifications', function ($q){
            $q->on('appointment_questions.appointment_id', '=', 'appointment_classifications.appointment_id');
                //->where('appointment_classifications.entity_type', '=', 'AUDIT_TYPE');
        });

        $query->join('master_answer_values', 'master_answer_values.id', '=', 'question_answers.answer_value_id');
        $query->join('questions', 'questions.id', '=', 'question_answers.question_id');

        $query->join('question_classifications', function ($q){
            $q->on('question_classifications.question_id', '=', 'questions.supper_parent_question_id');
                //->where('question_classifications.entity_tag', '=', 1);
        });

        $query->join('master_classification_options', 'master_classification_options.id', '=', 'question_classifications.option_value');

        $query->leftJoin('question_action_items', 'question_action_items.question_id', '=', 'questions.id');
        $query->leftJoin('appointment_action_item_users', function($q) use($appointment_id){
            $q->on('appointment_action_item_users.question_action_item_id', '=', 'question_action_items.id');
                //->where('appointment_action_item_users.appointment_id', '=', $appointment_id);
        });
        $query->leftJoin('users', 'users.id', '=', 'appointment_action_item_users.user_id');
        $query->leftJoin('images', function($q){
            $q->on('images.entity_id', '=', 'appointment_questions.id');
                //->where('images.entity_tag', '=', 'question_photo');
        });

        $query->orderBy('appointment_questions.question_id', 'asc');

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
        \Log::info("=============query....===============");
        return $result;
    }

    public function getParentLaw($parentId){
        return $this->model
            ->select('questions.law')
            ->where('questions.id',$parentId)
            ->first();
    }

    /**
     * Returns all active questions, based on answer id
     * @param $id
     * @return mixed
     */
    public function getQuestionsBasedOnAnswer($id) {
        return $this->model->where('question_answer_id', $id)
            ->select('id','question', 'question_answer_id', 'parent_question_id', 'master_question_id', 'supper_parent_question_id', 'status', 'is_draft')
            ->where('is_deleted', 0)
            ->get();
    }

    /**
     * Returns all active questions, based on answer id
     * @param $id
     * @param bool $checkStatus
     * @return mixed
     */
    public function getChildQuestionCountByParent($id, $checkStatus = false) {
        if ($checkStatus) {
            return \DB::table('questions')->where('parent_question_id', $id)
                ->where('is_deleted', 0)
                ->where('status', 1)
                ->count();
        }else {
            return \DB::table('questions')->where('parent_question_id', $id)
                ->where('is_deleted', 0)
                ->count();
        }

    }

    public function getUnPublishedQuestions($publish_date)
    {
        return $this->model
                    ->where('is_draft', 1)
                    ->where('status', 1)
                    ->where('is_deleted', 0)
                    ->whereDate('published_at','=', $publish_date)
                    ->get();
    }

    public function updateUnPublishedQuestions($data)
    {
        return $this->model
            ->whereIn('id', $data)
            ->update(['is_draft' => 0]);
    }
}