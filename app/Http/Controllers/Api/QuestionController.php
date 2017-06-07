<?php

namespace App\Http\Controllers\Api;

use App\Models\MasterClassificationOption;
use App\Repositories\AppointmentActionItemUsersRepository;
use App\Repositories\AppointmentClassificationRepository;
use App\Repositories\AppointmentQuestionRepository;
use App\Repositories\MasterClasificationRepository;
use App\Repositories\QuestionActionItemRepository;
use App\Repositories\QuestionAnswerRepository;
use App\Repositories\QuestionClassificationRepository;
use App\Repositories\QuestionRepository;
use App\Repositories\UsersRepository;
use Illuminate\Http\Request;
use App\Repositories\ImagesRepository;
use App\Repositories\QuestionCitationsRepository;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use PhpParser\Node\Expr\Array_;

class QuestionController extends Controller
{
    private $question;
    private $questionAnswer;
    private $appointmentQuestion;
    private $appointmentClassification;
    private $actionItem;
    private $actionItemUsers;
    private $masterClassification;
    private $questionClassification;
    private $masterClassiOption;
    private $images;
    private $user;
    private $citation;


    /**
     * Constructor.
     *
     */
    public function __construct(QuestionRepository $question,
                                QuestionAnswerRepository $questionAnswer,
                                AppointmentQuestionRepository $appointmentQuestion,
                                QuestionActionItemRepository $actionItem,
                                MasterClasificationRepository $masterClassification,
                                QuestionClassificationRepository $questionClassification,
                                MasterClassificationOption $masterClassiOption,
                                ImagesRepository $images,
                                AppointmentActionItemUsersRepository $actionItemUsers,
                                UsersRepository $user,
                                AppointmentClassificationRepository $appointmentClassification,
                                QuestionCitationsRepository $citation
    ){
        $this->question = $question;
        $this->questionAnswer = $questionAnswer;
        $this->appointmentQuestion = $appointmentQuestion;
        $this->appointmentClassification = $appointmentClassification;
        $this->actionItem = $actionItem;
        $this->actionItemUsers = $actionItemUsers;
        $this->masterClassification = $masterClassification;
        $this->questionClassification = $questionClassification;
        $this->masterClassiOption = $masterClassiOption;
        $this->images = $images;
        $this->user = $user;
        $this->citation = $citation;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * Display Question Answer Tree Based on Appointment Id .
     *
     * @param  int  $id
     * @return json
     */
    public function show($id)
    {
        $questionIds = array();
        $data = array();


        $appointments = $this->appointmentQuestion->findWhere(array('appointment_id' => $id, 'parent_question_id' => 0));
        \Log::info('=================');
        \Log::info($appointments);
        \Log::info('=================');
        foreach($appointments as $appointment){
            $questionIds[] = $appointment->supper_parent_question_id;

        }

//        $questions = $this->question->findQuestionIpadArray($questionIds);
        $questions= $this->filterSubQuestionsByClassifications($questionIds, $id);

        \Log::debug("==== question count " . count($questions));
        \Log::debug("==== question ids " . print_r($questionIds, true));
        foreach ($questions as $fq) {
            \Log::debug("==== filtered question list " . $fq->id);
        }

        $answers = $this->questionAnswer->findQuestionAnsswerArray($questionIds);


        if(count($questionIds) > 0){

            foreach($questionIds as $questionId){
                if(count($questions) > 0){
                    $x = 0;
                    foreach($questions as $key => $question){
                        if($question->supper_parent_question_id == $questionId && $question->parent_question_id == 0 && $question->status = 1){
                            $actionItems = $this->findQuestionActionItems($question->id);
                            $mainCategory = $this->findQuestionCategory($question->id);
                            $citations = $this->citation->getCitations($question->id);

                            //$questions[$x]->citations = array();
                            //echo json_encode($questions[0]); die;
                            //$questions[$key]->citations = $citations;

                            $returnArray = array('question_id' =>$question->id, 'question' => $question->question, 'question_answer_id' => $question->question_answer_id, 'explanation' => $question->explanation,'action_items' => $actionItems, 'category' => $mainCategory,'citations' => $citations);
                           //$returnArray = array('question_id' =>$question->id, 'question' => $question->question, 'question_answer_id' => $question->question_answer_id, 'action_items' => $actionItems, 'category' => $mainCategory,'citations' => array('hit'));
                            if(!empty($answers)){
                                $answerArray = $this->addQuestionsAnswerToArray($question->id, $answers, $questions, $returnArray);
                                array_push($data,$answerArray);
                            }
                        }
                        $x++;
                    }
                }
            }

            $ret_array = array();
            $cat_array = array();
            $temp = array();
            if(count($data) > 0)
            {
                foreach($data as $main)
                {
                    array_push($cat_array,array('question_id' => $main['question_id'] , 'category_name' => $main['category']->name));
                }

                foreach ($cat_array as $key => $value)
                {
                    $temp[$key] = $value['category_name'];
                }

                array_multisort($temp, SORT_ASC, $cat_array);

                foreach($cat_array as $sorted_cat)
                {
                    foreach($data as $main)
                    {
                        if($main['question_id'] == $sorted_cat['question_id'])
                        {
                            array_push($ret_array, $main);
                        }
                    }
                }

            }

            return response()->json(array('success'=>'true', 'message'=> array('questions'), 'questions'=> $ret_array));
        }
        else{
            return response()->json(array('success'=>'false', 'message'=> array('No questions')));
        }


    }


    /**
     * Add Question's Answers to Array.
     *
     * @param  int  $questionId
     * @param  array  $answers
     * @param  array  $questions
     * @param  array  $returnArray
     * @return array
     */
    private function addQuestionsAnswerToArray($questionId, $answers, $questions, &$returnArray){
        $key = 0;
        if(!empty($answers)){
            foreach($answers as $answer){
                if($answer->question_id == $questionId && $answer->is_deleted == 0){

                    $answerData = array(
                        'id' =>$answer->id,
                        'answer_id' => $answer->answer_id,
                        'answer_name' => $answer->answer_name,
                        'question_id'=> $answer->question_id,
                        'answer_value_id' => $answer->answer_value_id,
                        'answer_value_name' => $answer->answer_value_name
                    );
                    $citations = $this->citation->getCitations($answer->question_id);


                    $answerData = $this->addAnswerQuestionToArray($answer->id, $answers, $questions, $answerData);
                    $returnArray['answers'][$key] = $answerData;
                    $returnArray['citations'] = $citations;
                    $key++;
                }
            }
        }
        return $returnArray;
    }


    /**
     * Add Answer's Questions to Array.
     *
     * @param  int  $answerId
     * @param  array  $answers
     * @param  array  $questions
     * @param  array  $answerData
     * @return array
     */
    private function addAnswerQuestionToArray($answerId, $answers, $questions, &$answerData){
        $key = 0;
        if(!empty($questions)){
            foreach($questions as $question){
                if($question->question_answer_id == $answerId && $question->status == 1){
                    $actionItems = $this->findQuestionActionItems($question->id);

                    $citations = $this->citation->getCitations($question->id);
                    $questionData = array(
                        'question_id' =>$question->id,
                        'question' => $question->question,
                        'question_answer_id' => $question->question_answer_id,
                        'explanation' => $question->explanation,
                        'action_items' => $actionItems,
                        'citations' => $citations
                    );


                    $mainCategory = $this->findQuestionCategory($question->id);
                    // check category options, do not create category object if it returns empty element within category object
                    if (isset($mainCategory) && !empty($mainCategory)) {
                        if (!is_array($mainCategory) && empty($mainCategory[0])) {
                            $questionData['category_id'] = $mainCategory->id;
                        }
                    }

                    $questionData = $this->addQuestionsAnswerToArray($question->id, $answers, $questions, $questionData);
                    $answerData['questions'][$key] = $questionData;
                    $key++;
                }
            }
        }
        return $answerData;
    }

    /**
     * Add Question's Answers to Array.
     *
     * @param  int  $questionId
     * @param  array  $answers
     * @param  array  $questions
     * @param  array  $returnArray
     * @return array
     */
    private function addReportQuestionsAnswerToArray($questionId, $answers, $questions, $appointment_id, &$returnArray){
        $key = 0;
      //  echo \GuzzleHttp\json_encode($category_id);die;
        if(!empty($answers)){
            foreach($answers as $answer){
                if($answer->question_id == $questionId && $answer->is_deleted == 0){

                    $answerData = array(
                        'id' =>$answer->id,
                        'answer_id' => $answer->answer_id,
                        'answer_name' => $answer->answer_name,
                        'question_id'=> $answer->question_id,
                        'answer_value_id' => $answer->answer_value_id,
                        'answer_value_name' => $answer->answer_value_name,
                        'questions'     => array()
                    );

                    $answerData = $this->addReportAnswerQuestionToArray($answer->id, $answers, $questions, $appointment_id, $answerData);
                    $returnArray['answers'][$key] = $answerData;
                    $key++;
                }
            }
        }
        return $returnArray;
    }


    /**
     * Add Answer's Questions to Array.
     *
     * @param  int  $answerId
     * @param  array  $answers
     * @param  array  $questions
     * @param  array  $answerData
     * @return array
     */
    private function addReportAnswerQuestionToArray($answerId, $answers, $questions, $appointment_id, &$answerData){
        $key = 0;
        $parent_question_id = "";
        $actionItems = array();
        $images = "";

        if(!empty($questions)){
            foreach($questions as $question){

                $appointment_question_increment = $this->appointmentQuestion->findWhere(array('question_id' => $question->id, 'appointment_id' => $appointment_id));
                if(isset($appointment_question_increment[$key])) {
                    //get all images array on question
                    $images = $this->findQuestionAnswerImages($appointment_question_increment[$key]);
                }

                if($question->question_answer_id == $answerId && $question->status == 1){

                    $question_answer_value = $this->appointmentQuestion->findWhere(array('question_id' => $question->id, 'appointment_id' => $appointment_id));
                    $nonCompliantValue = $this->questionAnswer->findWhere(array('id' => $question_answer_value[0]->master_answer_id));


                    //get all appointments associated with parent question id
                    $appointment_entity_id = $this->appointmentQuestion->findWhere(array('appointment_id' => $appointment_id));

                    //get all action items assigned to questions
                    if(isset($nonCompliantValue[0])) {
                       if($nonCompliantValue[0]->answer_value_id==2 || $nonCompliantValue[0]->answer_value_id==3) {
                           $actionItems = $this->findQuestionActionItemsWithUsers($question->id, $appointment_id);
                       }else{
                           $actionItems = [
                               'action_items'  => [],
                               'assigned_users'=> []
                           ];
                       }
                    }else{
                        $actionItems = [
                                'action_items'  => [],
                                'assigned_users'=> []
                        ];
                    }

                    //choose between all categories and specific category selection
                    $mainCategory = $this->findQuestionCategory($question->id);

                    if(isset($question_answer_value[0])){
                        $parent_question_id = $question_answer_value[0]->parent_question_id;
                    }

                    $questionData = array(
                        'question_id' =>$question->id,
                        'question' => $question->question,
                        'parent_question_id' => $parent_question_id,
                        'images'    => $images,
                        'question_answer_id' => $question->question_answer_id,
                        'appointment_comment' => $question_answer_value[$key]->comment,
                        'explanation' => $question->explanation,
                        'action_items' => (array) $actionItems,
                        'category'      => $mainCategory
                    );

                    $questionData = $this->addReportQuestionsAnswerToArray($question->id, $answers, $questions, $appointment_id, $questionData);
                    $answerData['questions'][$key] = $questionData;
                    $key++;
                }
            }
        }
        return $answerData;
    }

    /**
     * Find Question Action Items.
     *
     * @param  int  $questionId
     * @return array
     */
    private function findQuestionActionItems($questionId){
        return $this->actionItem->findWhere(array('question_id' => $questionId, 'status' => 1));
    }

    /**
     * Find Question Action Items with assigned users.
     *
     * @param  int  $questionId
     * @return array
     */
    private function findQuestionActionItemsWithUsers($questionId, $appointment_id){
        $action_items_with_users = array();
        $action_items = array();
        $users = array();
        $users_array = array();
        $action_item = array();

        //get all action items
        $action_items  = $this->actionItem->findWhere(array('question_id' => $questionId, 'status' => 1));

        foreach ($action_items as $key => $action_item) {
            //get all action item assigned users
            $assigned_users[] = $this->actionItemUsers->findWhere(array('question_action_item_id' => $action_item->id, 'appointment_id' => $appointment_id));

            //collect all action item ids
            $action_ids[] = array('action_item_id' => $action_item->id);
        }

        if(count($assigned_users) > 0) {
            foreach ($assigned_users[0] as $pointer => $assigned_user) {
                $users = $this->user->findWhere(array('id' => $assigned_user->user_id));
                $users_array[] = json_decode(json_encode($users[0]), True);

                $users_array[$pointer]['action_item_ids'][] = $assigned_user;
            }

        }else{ $users = array(''); }


        $action_items_with_users = array(
            'action_items'  => $action_items,
            'assigned_users'=> $users_array,
        );

        return $action_items_with_users;
    }

    /**
     * Find Main Question Category
     *
     * @param  int  $questionId
     * @return array
     */
    private function findQuestionCategory($questionId){
        $mainCategory = $this->masterClassification->findBy('is_main', 1);
        $questionClassification = $this->questionClassification->findWhere(array('entity_tag' => $mainCategory->id, 'question_id' => $questionId));
        if(isset($questionClassification[0])) {
            $option = $this->masterClassiOption->find($questionClassification[0]->option_value);
        }else{
            $option = array('');
        }
        return $option;
    }

    /**
     * Find Question Categories
     *
     * @param  int  $categoryId
     * @return array
     */
    private function findCategoriesById($questionId, $categoryId){
        $mainCategory = $this->masterClassification->findBy('is_main', 1);
        $questionClassification = $this->questionClassification->findWhere(array('entity_tag' => $mainCategory->id, 'option_value' => $categoryId));
        if(isset($questionClassification[0])) {
            $option = $this->masterClassiOption->find($questionClassification[0]->option_value);
        }else{
            $option = array('');
        }
        return $option;
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
     * @param $id
     * @param $answer_value_id
     * @param $category_id
     * @param $question_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function otherAppointmentQuestions($id, $answer_value_id, $category_id="", $question_id)
    {
        //declaration of variables
        $questionIds = array();
        $data = array();
        $appointment_comments = array();
        $appointment_entity_id = array();
        $answer_id = array();
        $category_list = "";
        $category_id_arr = array();
        $category_name_arr = array();
        $parent_question_id = "";
        $images = array();

        //get all answered questions
        $appointments = $this->appointmentQuestion->findAnsweredAppointmentQuestions($id);

        foreach($appointments as $appointment){
            //store multiple ids to array array items
            $questionIds[] = $appointment->question_id;
            $answer_id[] = $appointment->master_answer_id;
            $parent_question_id[] = $appointment->parent_question_id;
            $appointment_comments[] = $appointment->comment;
            $appointment_entity_id[] = $appointment->id;
        }

        if($question_id!=null){
            $questions = $this->question->findAllQuestionArray($questionIds);
            $answers = $this->questionAnswer->findQuestionOtherAnswersArray($questionIds, $answer_id, $answer_value_id);
        }else{
            $questions = $this->question->findAllQuestionArray($questionIds);
            $answers = $this->questionAnswer->findQuestionOtherAnswersArray($questionIds, $answer_id, $answer_value_id);
        }

        foreach($questionIds as $qkey => $questionId){
            foreach($questions as $key => $question){

                $appointment_question_increment = $this->appointmentQuestion->findWhere(array('question_id' => $questionId, 'appointment_id' => $id));
                if(isset($appointment_question_increment[$key])) {
                    //get all images array on question
                    $images = $this->findQuestionAnswerImages($appointment_question_increment[$key]);
                }

                if($question->supper_parent_question_id == $questionId && $question->parent_question_id == 0 && $question->status = 1){

                    $question_answer_value = $this->appointmentQuestion->findWhere(array('question_id' => $question->id, 'appointment_id' => $id));
                    $nonCompliantValue = $this->questionAnswer->findWhere(array('id' => $question_answer_value[0]->master_answer_id));

                    //get all action items assigned to questions
                    if($nonCompliantValue[0]->answer_value_id==2 || $nonCompliantValue[0]->answer_value_id==3) {
                        $actionItems = $this->findQuestionActionItemsWithUsers($question->id, $id);
                    }else{
                        $actionItems = [
                            'action_items'  => [],
                            'assigned_users'=> []
                        ];
                    }

                    //choose between all categories and specific category selection
                    if($category_id!=""){
//                        echo \GuzzleHttp\json_encode($question_id);die;
                        $mainCategory = $this->findCategoriesById($question_id, $category_id);
//                        $mainCategory = $this->findQuestionCategory($question->id);
                    }else{
                        $mainCategory = $this->findQuestionCategory($question->id);
                    }

                    if(isset($mainCategory->id)) {
                        //store category name and ids in separated arrays
                        $category_id_arr[] = $mainCategory->id;
                        $category_name_arr[] = $mainCategory->name;

                        //remove duplicates and store same category ids and names to two arrays
                        $cat_id_arr = array_unique($category_id_arr);
                        $cat_name_arr = array_unique($category_name_arr);

                        //combine category id's and name's into one array
                        if(is_array($cat_id_arr) && is_array($cat_name_arr)) {
                            $category_list = array_combine($cat_id_arr, $cat_name_arr);
                        }
                    }else{
                        $category_id_arr[] = [];
                        $category_name_arr[] = [];

                        $cat_id_arr[] = array('');
                        $cat_name_arr[] = array('');
                        $category_list = array('');
                    }



                    //echo json_encode($actionItems);die;
                    if(!empty($answers)){

                        //send all data to get question answers list
                        $returnArray = array(
                            'question_id' =>$question->id,
                            'question' => $question->question,
                            'parent_question_id' => $parent_question_id[$qkey],
                            'explanation' => $question->explanation,
                            'images' => $images,
                            'appointment_comment' => $appointment_comments[$qkey],
                            'action_items' => $actionItems,
                            'category' => $mainCategory
                        );

                        //echo json_encode($answers[0]->answer_value_id);die;
                        $answerArray = $this->addReportQuestionsAnswerToArray($question->id, $answers, $questions, $id, $returnArray);
                        array_push($data,$answerArray);
                    }
                }
            }
        }

        //send all data to front-end
        return response()->json(array('success'=>'true', 'message'=>array('report_questions'), 'questions'=> $data, 'category_list' => $category_list));
    }

    /**
     * Find all images in question comments
     * @param $appointment_entity_id
     * @return array
     */
    public function findQuestionAnswerImages($appointment_entity_id)
    {
        //declaration of variables
        $image_path = array();
        if(isset($appointment_entity_id->id)) {
            $question_answer_image = $this->images->findImageByEntity(array(
                'entity_id' => $appointment_entity_id->id,
                'is_deleted' => 0,
                'type' => Config::get('simplifya.UPD_TYPE_QUESTION_COMMENT_PIC')
            ));
        }else{
            $question_answer_image = array(
                'entity_id' => '',
                'is_deleted' => '',
                'type' => ''
            );
        }

        foreach ($question_answer_image as $pointer => $item) {
            if(isset($item->name)) {
                $image_path[] = Config::get('simplifya.BUCKET_IMAGE_PATH') . Config::get('aws.ACTION_COMMENT_IMG_DIR') . $item->name;
            }
        }

        return $image_path;
    }

//    public function findQuestionListAnswerImages($question_id, $appointment_id)
//    {
//        $appointment_question_id = $this->appointmentQuestion->findWhere(array('appointment_id' => $appointment_id, 'question_id' => $question_id));
//        echo json_encode($appointment_question_id);die;
//        //declaration of variables
//        $image_path = array();
//
//        $question_answer_image = $this->images->findImageByEntity(array('entity_id' => $appointment_entity_id, 'is_deleted' => 0, 'type' => Config::get('simplifya.UPD_TYPE_QUESTION_COMMENT_PIC')));
//
//        foreach ($question_answer_image as $item) {
//            $image_path[] = Config::get('simplifya.BUCKET_IMAGE_PATH').Config::get('aws.ACTION_COMMENT_IMG_DIR').$item->name;
//        }
//        return $image_path;
//    }

    /**
     * @param $id
     * @param $answer_value_id
     * @param $category_id
     * @param $question_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function AppointmentQuestions($id)
    {
        //declaration of variables
        $questionIds = array();
        $data = array();
        $appointment_comments = array();
        $appointment_entity_id = array();
        $answer_id = array();
        $category_list = array();
        $category_id_arr = array();
        $category_name_arr = array();
        $category_list_arr = [];

        //get all answered questions
        $appointments = $this->appointmentQuestion->findAnsweredAppointmentQuestions($id);

        foreach($appointments as $appointment){
            //initialize multiple ids into arrays
            $questionIds[] = $appointment->question_id;
            $answer_id[] = $appointment->master_answer_id;
            $parent_question_id[] = $appointment->parent_question_id;
            $appointment_comments[] = $appointment->comment;
            $appointment_entity_id[] = $appointment->id;
        }

        //get all questions and answers according to the appointment id
        $questions = $this->question->findQuestionIpadArray($questionIds);
        $answers = $this->questionAnswer->findQuestionOtherAnswersArray($questionIds, $answer_id, "");

        foreach($questionIds as $qkey => $questionId){
            foreach($questions as $key => $question){

                $appointment_question_increment = $this->appointmentQuestion->findWhere(array('question_id' => $questionId, 'appointment_id' => $id));
                if(isset($appointment_question_increment[$key])) {
                    //get all images array on question
                    $images = $this->findQuestionAnswerImages($appointment_question_increment[$key]);
                }

                if($question->supper_parent_question_id == $questionId && $question->parent_question_id == 0 && $question->status = 1){

                    $question_answer_value = $this->appointmentQuestion->findWhere(array('question_id' => $question->id, 'appointment_id' => $id));
                    $nonCompliantValue = $this->questionAnswer->findWhere(array('id' => $question_answer_value[0]->master_answer_id));

                    //get all action items assigned to questions
                    if($nonCompliantValue[0]->answer_value_id==2 || $nonCompliantValue[0]->answer_value_id==3) {
                        $actionItems = $this->findQuestionActionItemsWithUsers($question->id, $id);
                    }else{
                        $actionItems = [
                            'action_items'  => [],
                            'assigned_users'=> []
                        ];
                    }

                    //get main categories from parent questions
                    $mainCategory = $this->findQuestionCategory($question->id);

                    //store category name and ids in separated arrays
                    $category_id_arr[] = $mainCategory->id;
                    $category_name_arr[] = $mainCategory->name;

                    //remove duplicates and store same category ids and names to two arrays
                    $cat_id_arr = array_unique($category_id_arr);
                    $cat_name_arr = array_unique($category_name_arr);


//                    if(is_array($cat_id_arr) && is_array($cat_name_arr)) {
//
//                        foreach ($category_id_arr as $key => $category_id) {
//
//                            if((!isset($category_id[$key])) && (count($category_id_arr)==1)){
//                                $category_list[] = array(
//                                    'id' => $category_id_arr,
//                                    'name' => $category_name_arr,
//                                );
//                            } elseif((isset($category_id[$key])) && (count($category_id_arr)>1)) {
//                                $category_list[] = array(
//                                    'id' => $cat_id_arr[$key],
//                                    'name' => $cat_name_arr[$key],
//                                );
//                            }
//                        }
//                        //remove duplication categories from looping
//                        $category_list_arr = array_map("unserialize", array_unique(array_map("serialize", $category_list)));
//                    }


                    //combine category id's and name's into one array
                    if(is_array($cat_id_arr) && is_array($cat_name_arr)) {
                        foreach ($cat_id_arr as $pointer => $catId) {
                            $category_list[] = array(
                                'id' => $catId,
                                'name' => $cat_name_arr[$pointer]
                            );

                            //remove duplication categories from looping
                            $category_list_arr = array_map("unserialize", array_unique(array_map("serialize", $category_list)));

                        }
                    }

                    if(!empty($answers)){

                        //send all data to get question answers list
                        $returnArray = array(
                            'question_id' =>$question->id,
                            'question' => $question->question,
                            'parent_question_id' => $parent_question_id[$qkey],
                            'explanation' => $question->explanation,
                            'images' => $images,
                            'appointment_comment' => $appointment_comments[$qkey],
                            'action_items' => $actionItems,
                            'category' => $mainCategory
                        );

                        $answerArray = $this->addReportQuestionsAnswerToArray($question->id, $answers, $questions, $id, $returnArray);
                        array_push($data,$answerArray);
                    }
                }
            }
        }
        //send all data to mobile API
        return response()->json(array('success'=>'true', 'message'=>array('reports'), 'questions'=> $data, 'category_list' => array($category_list_arr)));
    }

    /**
     * Sub questions to have all the functionality of parent questions
     *
     * We have questions that apply to 4 different license types,
     * but the subquestion only applies to 2 of those 4 licenses.
     *
     * @param $id Appointment id
     * @param $questionIds Question ids
     *
     * @return array
     */
    private function filterSubQuestionsByClassifications($questionIds, $id){
        $filteredQuestions=array();
        $valueDatasets=$this->appointmentClassification->findWhere(array('appointment_id' => $id));
        //\Log::info('-----------value'.print_r($value_dataset->toArray(),true));

        $questions = $this->question->findQuestionIpadArray($questionIds);
        $allQuestionIds = array();
        foreach ($questions as $q) {
            $allQuestionIds[] = $q->id;
        }

        \Log::debug('======== question ids : ' . print_r($questionIds, true));
        \Log::debug('======== all question ids : ' . print_r($allQuestionIds, true));

        //\Log::info('========START QUESTIONS=========');
        $dataset=array();
        $licenseType=array();
        foreach ($valueDatasets as $valueDataset){
            if($valueDataset->entity_type == 'AUDIT_TYPE'){
                $dataset['AUDIT_TYPE']=$valueDataset->option_value;
            }elseif ($valueDataset->entity_type == 'COUNTRY'){
                $dataset['COUNTRY']=$valueDataset->option_value;

            }elseif ($valueDataset->entity_type == 'STATE'){
                $dataset['STATE']=$valueDataset->option_value;
            }elseif ($valueDataset->entity_type == 'CITY'){
                $dataset['CITY']=$valueDataset->option_value;

            }elseif ($valueDataset->entity_type == 'LICENCE'){

                $licenseTypes=explode(',',$valueDataset->option_value);
                foreach ($licenseTypes as $x){
                    if(!in_array($x,$licenseType)){
                        array_push($licenseType,$x);
                    }
                    //\Log::info('------------License'.print_r($x,true));
                }

            }elseif (is_numeric($valueDataset->entity_type)){
                $dataset[$valueDataset->entity_type] = $valueDataset->option_value;
            }
        }
        \Log::debug('--------------dataset: '.print_r($dataset,true));

        sort($licenseType);
        \Log::debug('------------License'.print_r($licenseType,true));

        $getList = $this->questionClassification->getAllQuestionsList($dataset, $licenseType, false, $allQuestionIds); // third parameter is to check child questions
        \Log::info("==== getList : " . print_r($getList, true));

        foreach ($questions as $temp){
            \Log::debug("===== check id " . $temp->id);
            if($temp->parent_question_id!=0){

                if(in_array($temp->id,$getList)){
                    \Log::debug("===== Add " . $temp->id . ' into final list');
                    //\Log::info('------------'.$temp);
                    array_push($filteredQuestions,$temp);

                }

            }else{
                \Log::debug("===== Add " . $temp->id . ' into final list on else part');
                array_push($filteredQuestions,$temp);
            }
            //\Log::info('------------'.print_r($temp, true));
        }
        return $filteredQuestions;
    }
}