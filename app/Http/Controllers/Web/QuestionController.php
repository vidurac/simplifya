<?php

namespace App\Http\Controllers\Web;

use App\Lib\CsvGenerator;
use App\Models\MasterCountry;
use App\Models\MasterKeyword;
use App\Repositories\MasterAnswerRepository;
use App\Repositories\MasterAnswerValueRepository;
use App\Repositories\MasterAuditTypeRepository;
use App\Repositories\MasterCityRepository;
use App\Repositories\MasterClasificationRepository;
use App\Repositories\MasterKeywordRepository;
use App\Repositories\MasterLicenseRepository;
use App\Repositories\MasterStateRepository;
use App\Repositories\QuestionActionItemRepository;
use App\Repositories\QuestionAnswerRepository;
use App\Repositories\QuestionClassificationRepository;
use App\Repositories\QuestionKeywordRepository;
use App\Repositories\QuestionRepository;
use App\Repositories\UsersRepository;
use App\Repositories\UserSettingsRepository;
use Illuminate\Http\Request;
use Auth;
use App\Http\Requests;
use App\Http\Requests\AddQuestionRequest;
use App\Http\Requests\AddQuestionUserSettings;
use App\Http\Requests\AddChildQuestionRequest;
use App\Http\Requests\UpdateChildQuestionRequest;
use App\Http\Requests\UpdateParentQuestionRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;
use DB;
use Illuminate\Support\Facades\Redirect;
use Mockery\CountValidator\Exception;
use Psy\Util\Json;
use App\Lib\PdfGenerator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Repositories\QuestionCitationsRepository;
use App\Repositories\MasterUserRepository;


class QuestionController extends Controller
{

    private $question, $classification, $auditType, $country, $city, $state, $masterLicenses, $masterAnswer, $answerValue, $masterKeyword, $keyword, $actionItem, $questionClassification, $questionAnswer, $user,$userSettings, $csv;
    private $citation; private $master_data;

    /**
     * QuestionController constructor.
     * @param QuestionRepository $question
     * @param MasterClasificationRepository $classification
     * @param MasterAuditTypeRepository $auditType
     * @param MasterCountry $country
     * @param MasterCityRepository $city
     * @param MasterStateRepository $state
     * @param MasterLicenseRepository $masterLicenses
     * @param MasterAnswerRepository $masterAnswer
     * @param MasterAnswerValueRepository $answerValue
     * @param QuestionKeywordRepository $keyword
     * @param MasterKeywordRepository $masterKeyword
     * @param QuestionActionItemRepository $actionItem
     * @param QuestionClassificationRepository $questionClassification
     * @param QuestionAnswerRepository $questionAnswer
     * @param UsersRepository $user
     */
    public function __construct(QuestionRepository $question,
                                MasterClasificationRepository $classification,
                                MasterAuditTypeRepository $auditType,
                                MasterCountry $country,
                                MasterCityRepository $city,
                                MasterStateRepository $state,
                                MasterLicenseRepository $masterLicenses,
                                MasterAnswerRepository $masterAnswer,
                                MasterAnswerValueRepository $answerValue,
                                QuestionKeywordRepository $keyword,
                                MasterKeywordRepository $masterKeyword,
                                QuestionActionItemRepository $actionItem,
                                QuestionClassificationRepository $questionClassification,
                                QuestionAnswerRepository $questionAnswer,
                                UsersRepository $user,
                                UserSettingsRepository $userSettings,
                                CsvGenerator $csv,
                                QuestionCitationsRepository $citation,
                                MasterUserRepository $master_data
                                )
    {
        $this->question = $question;
        $this->classification = $classification;
        $this->auditType = $auditType;
        $this->country = $country;
        $this->city = $city;
        $this->state = $state;
        $this->masterLicenses = $masterLicenses;
        $this->masterAnswer = $masterAnswer;
        $this->answerValue = $answerValue;
        $this->keyword = $keyword;
        $this->masterKeyword = $masterKeyword;
        $this->actionItem = $actionItem;
        $this->questionClassification = $questionClassification;
        $this->questionAnswer = $questionAnswer;
        $this->user = $user;
        $this->userSettings = $userSettings;
        $this->csv = $csv;
        $this->citation = $citation;
        $this->master_data  = $master_data;
    }

    /**
     * Display a listing of the questions.
     *
     * @return index
     */
    public function index()
    {

        if(Auth::user()->master_user_group_id == Config::get('simplifya.MasterAdmin')) {
            $masterKeywords = $this->masterKeyword->all(array('*'));
//            return view('question.index')->with(array('masterKeywords' => $masterKeywords, 'page_title' => 'Question Manager'));
            return response()->view('question.index',array('masterKeywords' => $masterKeywords, 'page_title' => 'Question Manager'),200)
                ->header('Cache-Control','no-store, no-cache, must-revalidate');
        }
        else{
            $message =  Config::get('messages.ACCESS_DENIED');
            return Redirect::to("/dashboard")->with('error', $message);
        }
    }

    /**
     * Show the form for create question view
     *
     * @return create
     */
    public function create()
    {
        $enable = false;
        $not_req_enable = false;

        if(Auth::user()->master_user_group_id == Config::get('simplifya.MasterAdmin')) {
            $auditTypes = $this->auditType->all(array('*'));
            $countries = $this->country->all(array('*'));
            //$masterKeywords = $this->masterKeyword->all(array('*'));
            $masterKeywords = $this->masterKeyword->getAllKeywords();
            $mainCategoryOptions = $this->classification->findClassifications(0);
            $classifications = $this->classification->findClassifications(1);
            $classificationsNotReq = $this->classification->findClassifications(2);
            $masterAnswers = $this->masterAnswer->findWhere(array('status' => '1'))->all();
            $masterAnswerValue = $this->answerValue->findWhere(array('status' => '1'))->all();
            $laws=Config::get('simplifya.LAW_TYPES');
            //$citations = $this->citation->getAllCitations();

            foreach ($classifications as $classification) {
                if($classification->status == 1) {
                    $enable = true;
                }
            }
            foreach ($classificationsNotReq as $classificationNotReq) {
                if($classificationNotReq->status == 1) {
                    $not_req_enable = true;
                }
            }

            $formated_categories = $this->getFormatCategories();
            $mainCategoryOptions[0]->masterClassificationOptions = $formated_categories;

            return view('question.create')->with(array('mainCategoryOptions' => $mainCategoryOptions, 'auditTypes' => $auditTypes, 'laws'=>$laws,'countries' => $countries, 'classifications' => $classifications, 'classificationsNotReq' => $classificationsNotReq, 'masterAnswers' => $masterAnswers, 'masterAnswerValue' => $masterAnswerValue, 'masterKeywords' => $masterKeywords, 'question_answer_id' => 0, 'count' => 0, 'page_title' => 'Create Question', 'enable' => $enable, 'not_req_enable' => $not_req_enable));
        }
        else{
            $message =  Config::get('messages.ACCESS_DENIED');
            return Redirect::to("/dashboard")->with('error', $message);
        }
    }



    /**
     * Store a newly created question.
     *
     * @param $request
     * @return Json
     */
    public function store(AddQuestionRequest $request)
    {
        $publishDate = '0000-00-00 00:00:00';
        $userId = Auth::user()->id;
        $visibility = $request->visibility;
        $mandatory = $request->mandatory;
        $question = $request->question;
        $explanation = $request->explanation;
        $keywords = $request->keywords;
        $mainCategory = $request->mainCategory;
        $classificationId = $request->classificationId;
        $classificationParentID = $request->classificationParentID;
        $is_child = $request->is_child;

        $auditTypes = $request->auditTypes;
        $country = $request->country;
        $state = is_array($request->state)? array_unique($request->state) : $request->state;
        $cities = $request->cities;
        $actionItems = $request->actionItems;
        $license = $request->license;
        $reqClassifictions = $request->reqClassifictions;
        $nonReqClassifications = $request->nonReqClassifications;
        $answers = $request->answers;
        $isDraft = $request->isDraft;
        $law = $request->law;
        $actionItems = json_decode($actionItems);
        $citations = $request->citations;
        if($request->publishDate != '') {
            $publishDate = date_format(date_create($request->publishDate),"Y-m-d H:i:s");
        }


        //\Log::info('================ START PARENT================'.print_r($citations,true)); die;


        // create question record
        $question_data = array('version_no' => 1,
            'question' => $question,
            'explanation' => $explanation,
            'is_mandatory' => $mandatory,
            'is_draft' => $isDraft,
            'is_archive' => 0,
            'comment' => '',
            'question_answer_id' => 0,
            'parent_question_id' => 0,
            'master_question_id' => 0,
            'previous_question_id' => 0,
            'created_by' => $userId,
            'updated_by' => $userId,
            'status' => $visibility,
            'law' => $law,
            'published_at' => $publishDate
        );

        // Start DB transaction
        DB::beginTransaction();

        //\Log::info('================ START PARENT================');

        try{
            $createQuestion = $this->question->create($question_data);
            if($createQuestion){

                $oid = 1;
                foreach($citations as $citation)
                {
                    if (is_array($citation) && count($citation)) {
                        $citation_data = array(
                            'question_id' => $createQuestion->id,
                            'citation' => isset($citation['citation'])? $citation['citation']: '',
                            'description' => isset($citation['description'])? $citation['description'] : '',
                            'link' => isset($citation['link'])? $citation['link'] : '',
                            'order_id' => $oid
                        );
                        //\Log::info('================ START PARENT================'.print_r($citation_data,true)); die;
                        //update citations
                        $this->citation->create($citation_data);
                        $oid++;
                    }
                }

                // update supper parent question id
                $this->question->update(array('supper_parent_question_id' => $createQuestion->id, 'master_question_id' => $createQuestion->id, 'previous_question_id' => $createQuestion->id), $createQuestion->id);

                // create question keywords
                $this->createQuestionKeywords($keywords, $createQuestion, $userId);

                // create action items
                $this->createActionItems($actionItems, $createQuestion, $userId);

                // create classification audit types
                $this->createQuestionClassifications($auditTypes, $createQuestion, $userId, "AUDIT_TYPE", 0);

                // create classification country
                $this->createQuestionClassifications($country, $createQuestion, $userId, "COUNTRY", 1);

                // create classification State
                if($law==1 && is_array($state)){

                    $this->createQuestionClassifications($state, $createQuestion, $userId, "STATE", 0);
                }else{
                    $this->createQuestionClassifications($state, $createQuestion, $userId, "STATE", 1);
                }

                // create classification cities
                $this->createQuestionClassifications($cities, $createQuestion, $userId, "CITY", 0);

                // create classification license
                $this->createQuestionLicence($license, $createQuestion, $userId);


                if($is_child == "yes")
                {
                    // create classification main category
                    $this->createQuestionClassifications($mainCategory, $createQuestion, $userId, "SUB_CATEGORY", 1);

                    // create classification main category for sub category
                    $this->createQuestionClassifications($classificationParentID, $createQuestion, $userId, $classificationId, 1);
                    //\Log::info('|||||||......sub..........|||||||||'.$mainCategory." ".$classificationParentID);
                    //die;
                }
                else
                {
                    // create classification main category
                    $this->createQuestionClassifications($mainCategory, $createQuestion, $userId, $classificationId, 1);
                }

                // create classification custom mandetory
                $this->createSystemQuestionClassifications($reqClassifictions, $createQuestion, $userId);

                // create classification custom not required
                $this->createSystemQuestionClassifications($nonReqClassifications, $createQuestion, $userId);

                // Question Answer add
                $this->createQuestionAnswers($answers, $createQuestion, $userId);

                // commit transaction
                DB::commit();
                //\Log::info('================ END PARENT================');
                if($isDraft == 0) {
                    $message = Config::get('messages.QUESTION_ADDED_AND_PUBLISH_SUCCESS');
                } else {
                    $message = Config::get('messages.QUESTION_ADDED_SUCCESS');
                }

                return Response()->json(array('success' => 'true', 'data' => $createQuestion->id, 'message' => $message), 200);
            }
            else{
                DB::rollback();
            }
        }
        catch(exception $ex){
            DB::rollback();
        }
    }


    /**
     * Create keywords related to question
     *
     * @param  array  $keywords
     * @param  array  $createQuestion
     * @param  int  $userId
     * @return boolean
     */
    private function createQuestionKeywords($keywords, $createQuestion, $userId){
        if(!empty($keywords)){
            foreach($keywords as $keyword){
                $masterKeyword = $this->masterKeyword->find($keyword);
                if(!empty($masterKeyword)){
                    $keyword_data = array('question_id' => $createQuestion->id,
                        'keyword_id' => $keyword,
                        'created_by' => $userId,
                        'updated_by' => $userId
                    );
                    $this->keyword->create($keyword_data);
                }
                else{
                    $master_keyword_data = array('name' => $keyword,
                        'created_by' => $userId,
                        'updated_by' => $userId
                    );
                    $response = $this->masterKeyword->create($master_keyword_data);
                    if($response){
                        $keyword_data = array('question_id' => $createQuestion->id,
                            'keyword_id' => $response->id,
                            'created_by' => $userId,
                            'updated_by' => $userId
                        );
                        $this->keyword->create($keyword_data);
                    }
                }
            }
            return true;
        }
    }


    /**
     * Create Action Items related to question
     *
     * @param  array  $actionItems
     * @param  array  $createQuestion
     * @param  int  $userId
     * @return boolean
     */
    private function createActionItems($actionItems, $createQuestion, $userId){
        if(!empty($actionItems)) {
            foreach ($actionItems as $item) {
                $item_data = array('name' => $item,
                    'question_id' => $createQuestion->id,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                    'status' => 1
                );
                $this->actionItem->create($item_data);
            }
            return true;
        }
    }

    /**
     * Create classifications related to question
     *
     * @param  array || int  $optionValues
     * @param  array  $createQuestion
     * @param  int  $userId
     * @param  string  $entityTag
     * @param  int  $questionId
     * @return boolean
     */
    private function createQuestionClassifications($optionValues, $createQuestion, $userId, $entityTag, $questionId){
        if($questionId == 0){
            if(!empty($optionValues)) {
                if (is_array($optionValues )) {
                    foreach ($optionValues as $value) {
                        $item_data = array('entity_tag' => $entityTag,
                            'question_id' => $createQuestion->id,
                            'option_value' => $value,
                            'created_by' => $userId,
                            'updated_by' => $userId
                        );
                        $this->questionClassification->create($item_data);
                    }
                }else {
                    $item_data = array('entity_tag' => $entityTag,
                        'question_id' => $createQuestion->id,
                        'option_value' => $optionValues,
                        'created_by' => $userId,
                        'updated_by' => $userId
                    );
                    $this->questionClassification->create($item_data);
                }

                return true;
            }
        }
        else{
            $item_data = array('entity_tag' => $entityTag,
                'question_id' => $createQuestion->id,
                'option_value' => $optionValues,
                'created_by' => $userId,
                'updated_by' => $userId
            );
            $this->questionClassification->create($item_data);
            return true;
        }

    }


    /**
     * Create licence related to question
     *
     * @param  array  $license
     * @param  array  $createQuestion
     * @param  int  $userId
     * @return boolean
     */
    private function createQuestionLicence($license, $createQuestion, $userId){
        if(!empty($license)) {
            foreach ($license as $lic) {
                $item_data = array('entity_tag' => 'LICENCE',
                    'question_id' => $createQuestion->id,
                    'option_value' => is_array($lic)? implode(",", $lic) : $lic,
                    'created_by' => $userId,
                    'updated_by' => $userId
                );
                $this->questionClassification->create($item_data);
            }

            return true;
        }
    }


    /**
     * Create mandatory and non-mandatory classifications related to question
     *
     * @param  array  $classifications
     * @param  array  $createQuestion
     * @param  int  $userId
     * @return boolean
     */
    private function createSystemQuestionClassifications($classifications, $createQuestion, $userId){
        if(!empty($classifications)) {
            foreach ($classifications as $classifiction => $value) {
                if (is_array($value['value'])) {
                    foreach ($value['value'] as $val) {
                        $item_data = array('entity_tag' => $value['classificationId'],
                            'question_id' => $createQuestion->id,
                            'option_value' => $val,
                            'created_by' => $userId,
                            'updated_by' => $userId
                        );
                        $this->questionClassification->create($item_data);
                    }
                } else {
                    $item_data = array('entity_tag' => $value['classificationId'],
                        'question_id' => $createQuestion->id,
                        'option_value' => $value['value'],
                        'created_by' => $userId,
                        'updated_by' => $userId
                    );
                    $this->questionClassification->create($item_data);
                }
            }
            return true;
        }
    }


    /**
     * Create answers related to question
     *
     * @param  array  $answers
     * @param  array  $createQuestion
     * @param  int  $userId
     * @return boolean
     */
    private function createQuestionAnswers($answers, $createQuestion, $userId){
        if(!empty($answers)){
            foreach ($answers as $index => $answer) {
                $item_data = array('answer_value_id' => $answer['answerOptionId'],
                    'answer_id' => $answer['answerId'],
                    'question_id' => $createQuestion->id,
                    'supper_parent_question_id' => $createQuestion->id,
                    'created_by' => $userId,
                    'updated_by' => $userId
                );
                $this->questionAnswer->create($item_data);
            }
            return true;
        }
    }

    public function getFormatCategories($id='')
    {
        $mainCategoryOptions = $this->classification->getAllCategories($id);

        $parent_categories = array();
        $sub_categories = array();

        foreach($mainCategoryOptions as $data)
        {
            if($data->parent_id == 0)
            {
                array_push($parent_categories,$data);
            }
            if($data->parent_id != 0)
            {
                array_push($sub_categories,$data);
            }
        }
        $i = 0;
        foreach($parent_categories as $pcat)
        {
            $temp = array();
            foreach($sub_categories as $scat)
            {
                if($pcat->id == $scat->parent_id)
                {
                    array_push($temp,$scat);
                }
            }
            $parent_categories[$i]->childs = $temp;
            $i++;
        }

        return $parent_categories;
        //\Log::info("=============category....===============".print_r(json_encode($parent_categories),true));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return View
     */
    public function edit($id)
    {
        if(Auth::user()->master_user_group_id == Config::get('simplifya.MasterAdmin')) {
            $question = $this->question->findWhere(array('id' => $id, 'is_deleted' => false))->first();
            $questions = $this->question->findWhere(array('is_deleted' => false))->all();
            $masterAnswers = $this->masterAnswer->findWhere(array('status' => '1'))->all();
            $masterAnswerValue = $this->answerValue->findWhere(array('status' => '1'))->all();
            $questionAnswers = $this->questionAnswer->findWhere(array('question_id' => $id, 'is_deleted' => false))->all();
            $answer = $this->questionAnswer->find(0, array("*"));
            $actionItems = $this->actionItem->findWhere(array('question_id' => $id))->all();
            $keywordList = $this->keyword->findWhere(array('question_id' => $id))->all();
            $masterKeywords = $this->masterKeyword->all(array("*"));
            $mainCategoryOptions = $this->classification->findClassifications(0);
            $questionClassifications = $this->questionClassification->findWhere(array('question_id' => $id))->all();
            $auditTypes = $this->auditType->all(array('*'));
            $countries = $this->country->all(array('*'));
            $country = $this->questionClassification->findWhere(array('question_id' => $id, 'entity_tag' => 'COUNTRY'))->first();
            $states = $this->state->findWhere(array('country_id' => $country->option_value, 'status' => true))->all();
            $state = $this->questionClassification->findWhere(array('question_id' => $id, 'entity_tag' => 'STATE'))->first();
            $cities = $this->city->getCityByStatus($state->option_value, true);
            $citations = $this->citation->getCitations($id);
            //$cities = $this->city->findWhere(array('status_id' => $state->option_value, 'status' => true))->all();

            // LAW_TYPE - Federal
            if ($question->law == 1) {
                $state = $this->questionClassification->findWhere(array('question_id' => $id, 'entity_tag' => 'STATE'))->all();
                $masterLicenses = [];
                $cities = [];
            } else {
                $state = $this->questionClassification->findWhere(array('question_id' => $id, 'entity_tag' => 'STATE'))->first();
                $cities = $this->city->getCityByStatus($state->option_value, true);
                $masterLicenses = DB::table('master_licenses')
                    ->select('*')
                    ->where('master_states_id', '=', $state->option_value)
                    ->get();
            }

            $laws=Config::get('simplifya.LAW_TYPES');
            //$cities = $this->city->findWhere(array('status_id' => $state->option_value, 'status' => true))->all();



            $licences = $this->questionClassification->findWhere(array('question_id' => $id, 'entity_tag' => 'LICENCE'))->all();
            $federalLicenses = [];
            if ($question->law == 1) {
                foreach ($licences as $licence) {
                    if ($licence->option_value != 'ALL') {
                        $lIds = explode(',', (string)$licence->option_value);
                        $lId = $lIds[0];
                        //find state by $id
                        //find all licenses to that specific state
                        $l = $this->masterLicenses->find($lId);
                        $l = DB::table('master_licenses')->select(array('master_licenses.id', 'master_licenses.master_states_id', 'master_licenses.name', 'master_states.name as state_name'))->join('master_states', 'master_states.id', '=', 'master_licenses.master_states_id')->where('master_licenses.id', $lId)->first();
                        $masterLicenses = DB::table('master_licenses')
                            ->select(array('master_licenses.id', 'master_licenses.name'))
                            ->where('master_states_id', '=', $l->master_states_id)
                            ->get();
                        $temp = new \stdClass();
                        $temp->state_id = $l->master_states_id;
                        $temp->state_name = $l->state_name;
                        $temp->licenses = (object)$licence->toArray();
                        $temp->masterLicenses = $masterLicenses;

                        $federalLicenses[] = $temp;
                    }
                }
            }



            $classifications = $this->classification->findClassifications(1);
            $classificationsNotReq = $this->classification->findClassifications(2);

            $formated_categories = $this->getFormatCategories($id);
            $mainCategoryOptions[0]->masterClassificationOptions = $formated_categories;
            //\Log::info("=============category....===============".print_r(json_encode($country->option_value),true));
            //\Log::info("=============category....===============".print_r(json_encode($mainCategoryOptions[0]->masterClassificationOptions),true));

            $licencesH = array();
            if(count($licences) > 0)
            {
                foreach($licences as $licence)
                {
                    if($licence->entity_tag == "LICENCE")
                    {
                        $ids = explode(',', (string)$licence->option_value);
                        foreach($ids as $id)
                        {
                            array_push($licencesH, $id);
                        }
                    }
                }
            }

            $citiesH = array();
            if(count($questionClassifications) > 0)
            {
                foreach($questionClassifications as $citiy)
                {
                    if($citiy->entity_tag == "CITY")
                    {
                        array_push($citiesH, $citiy->option_value);
                    }
                }
            }
            return response()->view('question.edit',array(
                'question' => $question,
                'questions' => $questions,
                'masterAnswers' => $masterAnswers,
                'masterAnswerValue' => $masterAnswerValue,
                'questionAnswers' => $questionAnswers,
                'answer' => $answer,
                'actionItems' => $actionItems,
                'masterKeywords' => $masterKeywords,
                'keywordList' => $keywordList,
                'mainCategoryOptions' => $mainCategoryOptions,
                'questionClassifications' => $questionClassifications,
                'laws'=>$laws,
                'auditTypes' => $auditTypes,
                'countries' => $countries,
                'states' => $states,
                'cities' => $cities,
                'licences' => $licences,
                'masterLicenses' => $masterLicenses,
                'classifications' => $classifications,
                'classificationsNotReq' => $classificationsNotReq,
                'is_parent' => 1,
                'licencesH' => $licencesH,
                'citiesH' => $citiesH,
                'federalLicenses' => $federalLicenses,
                'saved_citations' => $citations,
                'page_title' => 'Edit Question - ' . str_limit($question->question, $limit = 125, $end = '...')),200)->header('Cache-Control','no-store, no-cache, must-revalidate');
        }
        else{
            $message =  Config::get('messages.ACCESS_DENIED');
            return Redirect::to("/dashboard")->with('error', $message);
        }
    }

    /**
     * View the specified resource.
     *
     * @param  int  $id
     * @return View
     */
    public function view($id)
    {


        if(Auth::user()->master_user_group_id == Config::get('simplifya.MasterAdmin')) {
            $question = $this->question->findWhere(array('id' => $id, 'is_deleted' => false))->first();
            $questions = $this->question->findWhere(array('is_deleted' => false))->all();
            $masterAnswers = $this->masterAnswer->findWhere(array('status' => '1'))->all();
            $masterAnswerValue = $this->answerValue->findWhere(array('status' => '1'))->all();
            $questionAnswers = $this->questionAnswer->findWhere(array('question_id' => $id, 'is_deleted' => false))->all();
            $answer = $this->questionAnswer->find(0, array("*"));
            $actionItems = $this->actionItem->findWhere(array('question_id' => $id))->all();
            $keywordList = $this->keyword->findWhere(array('question_id' => $id))->all();
            $masterKeywords = $this->masterKeyword->all(array("*"));
            $mainCategoryOptions = $this->classification->findClassifications(0);
            $questionClassifications = $this->questionClassification->findWhere(array('question_id' => $id))->all();
            $auditTypes = $this->auditType->all(array('*'));
            $countries = $this->country->all(array('*'));
            $country = $this->questionClassification->findWhere(array('question_id' => $id, 'entity_tag' => 'COUNTRY'))->first();
            $states = $this->state->findWhere(array('country_id' => $country->option_value, 'status' => true))->all();
            $state = $this->questionClassification->findWhere(array('question_id' => $id, 'entity_tag' => 'STATE'))->first();
            $cities = $this->city->findWhere(array('status_id' => $state->option_value, 'status' => true))->all();
            $masterLicenses = $this->masterLicenses->findWhere(array('master_states_id' => $state->option_value))->all();
            $licences = $this->questionClassification->findWhere(array('question_id' => $id, 'entity_tag' => 'LICENCE'))->all();
            $classifications = $this->classification->findClassifications(1);
            $classificationsNotReq = $this->classification->findClassifications(2);
            $laws=Config::get('simplifya.LAW_TYPES');
            $citations = $this->citation->getCitations($id);

            $federalLicenses = [];
            if ($question->law == 1) {
                foreach ($licences as $licence) {
                    if ($licence->option_value != 'ALL') {
                        $lIds = explode(',', (string)$licence->option_value);
                        $lId = $lIds[0];
                        //find state by $id
                        //find all licenses to that specific state
                        $l = $this->masterLicenses->find($lId);
                        $l = DB::table('master_licenses')->select(array('master_licenses.id', 'master_licenses.master_states_id', 'master_licenses.name', 'master_states.name as state_name'))->join('master_states', 'master_states.id', '=', 'master_licenses.master_states_id')->where('master_licenses.id', $lId)->first();
                        $masterLicenses = DB::table('master_licenses')
                            ->select(array('master_licenses.id', 'master_licenses.name'))
                            ->where('master_states_id', '=', $l->master_states_id)
                            ->get();
                        $temp = new \stdClass();
                        $temp->state_id = $l->master_states_id;
                        $temp->state_name = $l->state_name;
                        $temp->licenses = (object)$licence->toArray();
                        $temp->masterLicenses = $masterLicenses;

                        $federalLicenses[] = $temp;
                    }
                }
            }

            return response()->view('question.view',array(
                'question' => $question,
                'questions' => $questions,
                'masterAnswers' => $masterAnswers,
                'masterAnswerValue' => $masterAnswerValue,
                'questionAnswers' => $questionAnswers,
                'answer' => $answer,
                'actionItems' => $actionItems,
                'masterKeywords' => $masterKeywords,
                'keywordList' => $keywordList,
                'mainCategoryOptions' => $mainCategoryOptions,
                'questionClassifications' => $questionClassifications,
                'auditTypes' => $auditTypes,
                'countries' => $countries,
                'states' => $states,
                'cities' => $cities,
                'licences' => $licences,
                'masterLicenses' => $masterLicenses,
                'federalLicenses' => $federalLicenses,
                'classifications' => $classifications,
                'laws'=>$laws,
                'classificationsNotReq' => $classificationsNotReq,
                'saved_citations' => $citations,
                'view_only_citations' => 'yes',
                'page_title' => 'View Question - ' . str_limit($question->question, $limit = 180, $end = '...')),200)->header('Cache-Control','no-store, no-cache, must-revalidate');
        }
        else{
            $message =  Config::get('messages.ACCESS_DENIED');
            return Redirect::to("/dashboard")->with('error', $message);
        }
    }

    /**
     *  Get states based on country.
     *
     * @return json
     */
    public function getStatus(){
        $country_id = $_GET['countryId'];
        //$states = $this->country->with(array('masterStates'))->find($country_id, array('*'));
        $states = $this->country
            ->with([
                'masterStates' => function ($query){
                    $query->orderBy('name', 'ASC');
                }
         ])->find($country_id, array('*'));

        return Response()->json(array('success' => 'true', 'data' => $states), 200);
    }

    /**
     *  Get cities based on states.
     *
     * @return json
     */
    public function getCities(){
        $state_id = $_GET['stateId'];
        $states = $this->state
            ->with([
                'masterCity' => function ($query){
                    $query->orderBy('name', 'ASC');
                    $query->where('status', '=', 1);
                    $query->orderBy('name', 'ASC');
                }
            ])
        ->find($state_id, array('*'));

        return Response()->json(array('success' => 'true', 'data' => $states), 200);
    }

    /**
     *  Get licences based on states.
     *
     * @return json
     */
    public function getLicences(){
        $state_id = $_GET['stateId'];

        if($state_id != "FEDERAL")
        {
            $states = $this->state->with(['masterLicense' => function ($query) {
                $query->where('status', '=', 1);
            }])->find($state_id, array('*'));
        }

        if($state_id == "FEDERAL")
        {
            //\Log::info("=============QC-S===============".print_r(json_encode($state_id),true));
            $counry_id = (isset($_GET['counry_id'])) ? $_GET['counry_id'] : '';

            $state_list = DB::table('master_states')
                            ->select('id')
                            ->where('country_id', '=', $counry_id)
                            ->get();

            $state_ids = array();
            foreach($state_list as $state_id)
            {
                array_push($state_ids, $state_id->id);
            }

            $states = DB::table('master_licenses')
                        ->select('*')
                        ->where('status', '=', 1)
                        ->where('type', '=', "FEDERAL")
                        ->whereIn('master_states_id', $state_ids)
                        ->get();

            $master_license = array();
            foreach($states as $state)
            {
                array_push($master_license,$state);
            }
            $data = array(
                'master_license' => $master_license
            );
            $states = $data;
        }
        //\Log::info("=============QC-S===============".print_r(json_encode($states),true));

        return Response()->json(array('success' => 'true', 'data' => $states), 200);
    }

    /**
     *  Get licences based on Country.
     *
     * @return json
     */
    public function getLicenseFromCountry(){
        $country_id = $_GET['countryId'];


        $countries = DB::table('master_licenses')
            ->select('master_licenses.id','master_licenses.name')
            ->join('master_states','master_licenses.master_states_id','=','master_states.id')
            ->where('master_licenses.status', '=', 1)
            ->where('master_states.country_id', '=', $country_id)
            ->get();

        $master_license = array();
        foreach($countries as $country)
        {
            array_push($master_license,$country);
        }
        $data = array(
            'master_license' => $master_license
        );
        $countries = $data;

        \Log::info("=============QC-S===============".print_r(json_encode($countries),true));

        return Response()->json(array('success' => 'true', 'data' => $countries), 200);
    }


    /**
     *  Get child question view.
     *
     * @return json
     */
    public function getChildQuestion(){
        $id = $_GET['questionId'];
        $supperParentId = $_GET['supperParentId'];
        $parentQuestionId = $_GET['parentQuestionId'];
        $answerId = $_GET['answerId'];

        $question = $this->question->findWhere(array('id' => $id, 'is_deleted' => false))->first();
        $questions = $this->question->all(array('*'));
        $masterAnswers = $this->masterAnswer->findWhere(array('status' => '1'))->all();
        $masterAnswerValue = $this->answerValue->findWhere(array('status' => '1'))->all();
        $questionAnswers = $this->questionAnswer->findWhere(array('question_id' => $id, 'is_deleted' => false))->all();
        $answer = $this->questionAnswer->findWhere(array('id' => $answerId, 'is_deleted' => false))->first();
        $actionItems = $this->actionItem->findWhere(array('question_id' => $id))->all();
        $supperParentQuestion = $this->question->find($supperParentId);
        $citations_saved = $this->citation->getCitations($id);

        $classifications = $this->classification->findClassifications(1);
        $classificationsNotReq = $this->classification->findClassifications(2);
        //print_r(json_encode($classificationsNotReq));
        $laws=Config::get('simplifya.LAW_TYPES');

        $parent_audit_types = $this->questionClassification->findWhere(array('question_id' => $parentQuestionId != '' ? $parentQuestionId : $id, 'entity_tag' => 'AUDIT_TYPE'))->all();
        //\Log::info("=============QC-S===============".print_r(json_encode($parent_audit_types),true));
        $auditTypes = $this->auditType->all(array('*'));
        //if(isset($_GET['auditType_data']) && $_GET['auditType_data'] != '')
        {
            $temp = array();
            /*$audit_type_ids = explode(',', (string)$_GET['auditType_data']);
            foreach($auditTypes as $tmp)
            {
                if(in_array($tmp->id,$audit_type_ids))
                {
                    array_push($temp,$tmp);
                }
            }*/

            foreach($parent_audit_types as $parent_audit_type)
            {
                foreach($auditTypes as $audit_type)
                {
                    if($audit_type->id == $parent_audit_type['option_value'])
                    {
                        array_push($temp,$audit_type);
                    }
                }
            }
            $auditTypes = $temp;
        }
        //$auditTypes = $this->auditType->findWhere(array('question_id' => $id, 'is_deleted' => false))->all();


        $countries = $this->country->all(array('*'));
        //if(isset($_GET['country_data']) && $_GET['country_data'] != '')
        {
            $country_ids = explode(',', (string)$_GET['country_data']);
            $temp = array();
            foreach($countries as $tmp)
            {
                if(in_array($tmp->id,$country_ids))
                {
                    array_push($temp,$tmp);
                }
            }
            $countries = $temp;
        }
        //$questionClassifications = $this->questionClassification->findWhere(array('question_id' => ($parentQuestionId==0?$supperParentId:$parentQuestionId)))->all();
        //$questionClassifications = $this->questionClassification->findWhere(array('question_id' => ($id==0?$supperParentId:$id)))->all();
       // \Log::info("=============QC-E===============".$id);

        $questionClassifications = $this->questionClassification->findWhere(array('question_id' => ($id==0?$supperParentId:$id)))->all();

        //$country = $this->questionClassification->findWhere(array('question_id' => ($parentQuestionId==0?$supperParentId:$parentQuestionId), 'entity_tag' => 'COUNTRY'))->first();
        $country = $this->questionClassification->findWhere(array('question_id' => ($id==0?$supperParentId:$id), 'entity_tag' => 'COUNTRY'))->first();
        $states = $this->state->findWhere(array('country_id' => $country->option_value, 'status' => true))->all();
        //$state = $this->questionClassification->findWhere(array('question_id' => ($parentQuestionId==0?$supperParentId:$parentQuestionId), 'entity_tag' => 'STATE'))->first();
        //if(isset($_GET['state_data']) && $_GET['state_data'] != '')
        {
            $temp = array();
            $state_ids = explode(',', (string)$_GET['state_data']);
            foreach($states as $tmp)
            {
                if(in_array($tmp->id,$state_ids))
                {
                    array_push($temp,$tmp);
                }
            }
            $states = $temp;
        }

        $state = $this->questionClassification->findWhere(array('question_id' => ($id==0?$supperParentId:$id), 'entity_tag' => 'STATE'))->first();

        /*if($state->option_value == "FEDERAL")
        {
            $cities = $this->questionClassification->findWhere(array('question_id' => $parentQuestionId != '' ? $parentQuestionId : $id, 'entity_tag' => 'CITY'))->all();
        }
        else
        {
        }*/
        \Log::debug("=== parent cities query");
        $cities = $this->city->findWhere(array('status_id' => $state->option_value, 'status' => true))->all();

        $parent_cities = $this->questionClassification->findWhere(array('question_id' => $parentQuestionId != '' ? $parentQuestionId : $id, 'entity_tag' => 'CITY'))->all();
        $parent_law = $this->question->find($parentQuestionId != '' ? $parentQuestionId : $id)->law;

        \Log::debug("=== parent cities end");
        //if(isset($_GET['city_data']) && $_GET['city_data'] != '')
        {
            $temp = array();
            foreach($parent_cities as $parent_city)
            {
                if ($parent_city != 'ALL') {
                    foreach($cities as $ct)
                    {
                        if($ct->id == $parent_city['option_value'])
                        {
                            array_push($temp,$ct);
                        }
                    }
                }else {

                }
            }
            \Log::debug("==== selected cities ". print_r($temp, true));
            if ($parent_law != 2) {
                $cities = $temp;
            }
        }

        //$licences = $this->questionClassification->findWhere(array('question_id' => ($parentQuestionId==0?$supperParentId:$parentQuestionId), 'entity_tag' => 'LICENCE'))->all();
        //$licences = $this->questionClassification->findWhere(array('question_id' => ($id==0?$supperParentId:$id), 'entity_tag' => 'LICENCE'))->all();
        $licences = $this->questionClassification->findWhere(array('question_id' => ($id==0?$parentQuestionId:$id), 'entity_tag' => 'LICENCE'))->all();
        //\Log::info('licences//////////id='.$id.' $parentQuestionId='.$parentQuestionId." res ".($id==0?$parentQuestionId:$id));

        $masterLicenses = $this->masterLicenses->findWhere(array('master_states_id' => $state->option_value))->all();
        if($state->option_value == "FEDERAL")
        {
            $state_list = DB::table('master_states')
                ->select('id')
                ->where('country_id', '=', $country->option_value)
                ->get();

            $state_ids = array();
            foreach($state_list as $state_id)
            {
                array_push($state_ids, $state_id->id);
            }

            /*$masterLicenses = DB::table('master_licenses')
                                ->select('*')
                                ->where('status', '=', 1)
                                ->where('type', '=', "FEDERAL")
                                ->whereIn('master_states_id', $state_ids)
                                ->get();*/
            $msid = "";
            $a = 0;
            foreach($state_ids as $state_id)
            {
                if($a == 0)
                {
                    $msid .= $state_id." or ";
                }
                else
                {
                    $msid .= " master_states_id = ".$state_id." or ";
                }
                $a++;
            }
            if($msid != "")
            {
                $msid .= " 1=2 ";
            }

            $masterLicenses = $this->masterLicenses->findWhere(array(
                array('status', '=', 1),
                array('type', '=', "FEDERAL"),
                array('master_states_id',"=", $msid)
            ))->all();

            //$masterLicenses = json_decode(json_encode($masterLicenses), true);

            \Log::info("=============masterLicenses===============".print_r(json_encode($masterLicenses),true));
        }

        $parent_licences = $this->questionClassification->findWhere(array('question_id' => $parentQuestionId != '' ? $parentQuestionId : $id, 'entity_tag' => 'LICENCE'))->all();


        //\Log::info('licences//////////'.print_r(json_encode($masterLicenses),true));

        //if(isset($_GET['license_data']) && $_GET['license_data'] != '')
        {
            $temp = array();
            foreach($parent_licences as $licence)
            {
                $licen_ids = explode(',', (string)$licence['option_value']);
                foreach($masterLicenses as $ml)
                {
                    if(in_array($ml->id,$licen_ids))
                    {
                        array_push($temp,$ml);
                    }
                }
                if($licence['option_value'] == "GENERAL")
                {
                    array_push($temp,$licence);
                }
            }
            //$temp = collect($temp)->map(function($x){ return (array) $x; })->toArray();
            $temp = $this->unique_me($temp);
            $masterLicenses = $temp;


        }

        $superParentlicences = $this->questionClassification->findWhere(array('question_id' => $supperParentId, 'entity_tag' => 'LICENCE'))->all();
        $federalLicenses = [];
        if ($supperParentQuestion->law == 1) {
            foreach ($superParentlicences as $superParentlicence) {
                if ($superParentlicence->option_value != 'ALL') {
                    $lIds = explode(',', (string)$superParentlicence->option_value);
                    $lId = $lIds[0];
                    //find state by $id
                    //find all licenses to that specific state
                    $l = $this->masterLicenses->find($lId);
                    $l = DB::table('master_licenses')->select(array('master_licenses.id', 'master_licenses.master_states_id', 'master_licenses.name', 'master_states.name as state_name'))->join('master_states', 'master_states.id', '=', 'master_licenses.master_states_id')->where('master_licenses.id', $lId)->first();
                    $masterLicenses = DB::table('master_licenses')
                        ->select(array('master_licenses.id', 'master_licenses.name'))
                        ->where('master_states_id', '=', $l->master_states_id)
                        ->get();
                    $temp = new \stdClass();
                    $temp->state_id = $l->master_states_id;
                    $temp->state_name = $l->state_name;
                    $temp->licenses = (object)$superParentlicence->toArray();
                    $temp->masterLicenses = $masterLicenses;

                    $federalLicenses[] = $temp;
                }
            }
        }


        //\Log::info('licences//////////'.print_r(json_encode($questionClassifications),true));

        $html = View::make('question.ChildQuestion', array('saved_citations' => $citations_saved, 'question_id' => $id, 'auditTypes'=>$auditTypes,'countries'=>$countries,'questionClassifications'=>$questionClassifications,'country'=>$country,'states'=>$states,'state'=>$state,'cities'=>$cities,'masterLicenses'=>$masterLicenses,'licences'=>$licences,'question' => $question, 'questions' => $questions, 'masterAnswers' => $masterAnswers, 'masterAnswerValue' => $masterAnswerValue, 'questionAnswers' => $questionAnswers, 'answer' => $answer, 'actionItems' => $actionItems, 'supperParentQuestion' => $supperParentQuestion,'is_parent' => 0, "classifications" => $classifications, "classificationsNotReq" => $classificationsNotReq,'laws'=>$laws,'federalLicenses'=>$federalLicenses, 'parentLaw' => $parent_law));

        return Response()->json(array('success' => 'true', 'data' => $html->render()),200);

    }

    public function unique_me($main_array)
    {
        $temp_arr = array();
        $ret_array = array();
        foreach($main_array as $line)
        {
            if(!in_array($line['id'],$temp_arr))
            {
                array_push($temp_arr,$line['id']);
            }
        }
        foreach($temp_arr as $unique_id)
        {
            foreach($main_array as $main)
            {
                if($main['id'] == $unique_id)
                {
                    array_push($ret_array, $main);
                    break;
                }
            }
        }
        //\Log::info("=============QC-E===============".print_r($temp_arr,true));
        return $ret_array;
    }


    /**
     *  Get child question view only for view
     *
     * @return json
     */
    public function getChildQuestionForView(){
        $id = $_GET['questionId'];
        $supperParentId = $_GET['supperParentId'];
        $answerId = $_GET['answerId'];
        $question = $this->question->findWhere(array('id' => $id, 'is_deleted' => false))->first();
        $questions = $this->question->all(array('*'));
        $masterAnswers = $this->masterAnswer->findWhere(array('status' => '1'))->all();
        $masterAnswerValue = $this->answerValue->findWhere(array('status' => '1'))->all();
        $questionAnswers = $this->questionAnswer->findWhere(array('question_id' => $id, 'is_deleted' => false))->all();
        $answer = $this->questionAnswer->findWhere(array('id' => $answerId, 'is_deleted' => false))->first();
        $actionItems = $this->actionItem->findWhere(array('question_id' => $id))->all();
        $supperParentQuestion = $this->question->find($supperParentId);


        $html = View::make('question.ChildQuestionViewOnly', array('question' => $question, 'questions' => $questions, 'masterAnswers' => $masterAnswers, 'masterAnswerValue' => $masterAnswerValue, 'questionAnswers' => $questionAnswers, 'answer' => $answer, 'actionItems' => $actionItems, 'supperParentQuestion' => $supperParentQuestion));

        return Response()->json(array('success' => 'true', 'data' => $html->render()),200);

    }

    /**
     *  Get Answer Question.
     *
     * @return json
     */
    public function getAnswerQuestion(){
        $answerId = $_GET['questionAnswerId'];
        $questions = $this->question->findWhere(array('question_answer_id' => $answerId, 'is_deleted' => false))->all();
        $questionAnswer = $this->questionAnswer->findWhere(array('id' => $answerId))->first();

        return Response()->json(array('success' => 'true', 'question' => $questions, 'questionAnswer' => $questionAnswer),200);
    }


    /**
     *  Save child question.
     *
     * @return json
     */
    public function saveChildQuestion(AddChildQuestionRequest $request){

        $user_id = Auth::user()->id;
        $supperQuestionId = $request->superParentQuestionId;
        $visibility = $request->visibility;
        $mandatory = $request->mandatory;
        $question = $request->question;
        $explanation = $request->explanation;
        $answerId = $request->answerId;
        $parentQuestionId = $request->parentQuestionId;
        $actionItems = $request->actionItems;
        $answers = $request->answers;
        $reqClassifictions = $request->req_classification;
        $nonReqClassifications = $request->not_req_classification;

        $classificationId = $request->classificationId;
        $mainCategory = $request->mainCategory;
        $citations = $request->citations_child;
        $law = $request->law;

        $license_type = array();
        \Log::debug("l type " . print_r($request->license_type, true));

        if (is_array($request->license_type )) {
            foreach($request->license_type as $lt)
            {
                //\Log::info($lt['val']);
                if (is_array($lt)) {
                    array_push($license_type, $lt['val']);
                }else {
                    array_push($license_type, $lt);
                }
            }
        }else {
            $license_type = $request->license_type;
        }

        /*print_r($request->license_type);
        die;*/
        // create child question record
        $question_data = array('version_no' => 1,
            'question' => $question,
            'explanation' => $explanation,
            'is_mandatory' => $mandatory,
            'is_draft' => 1,
            'is_archive' => 0,
            'comment' => '',
            'question_answer_id' => $answerId,
            'parent_question_id' => $parentQuestionId,
            'master_question_id' => 0,
            'previous_question_id' => 0,
            'supper_parent_question_id' => $supperQuestionId,
            'created_by' => $user_id,
            'updated_by' => $user_id,
            'status' => $visibility,
            'law' => $law,
        );

        DB::beginTransaction();

        try{

//            $actionItems = json_decode($actionItems);

            $createQuestion = $this->question->create($question_data);
            $this->question->update(array('master_question_id' => $createQuestion->id, 'previous_question_id' => $createQuestion->id), $createQuestion->id);


            // create action items
            if(!empty($actionItems)) {
                foreach ($actionItems as $item) {
                    $item_data = array('name' => $item,
                        'question_id' => $createQuestion->id,
                        'created_by' => $user_id,
                        'updated_by' => $user_id,
                        'status' => 1
                    );
                    $this->actionItem->create($item_data);
                }
            }

            // Question Answer add
            if(!empty($answers)){
                foreach ($answers as $index => $answer) {
                    $item_data = array('answer_value_id' => $answer['answerOptionId'],
                        'answer_id' => $answer['answerId'],
                        'question_id' => $createQuestion->id,
                        'supper_parent_question_id' => $supperQuestionId,
                        'created_by' => $user_id,
                        'updated_by' => $user_id
                    );
                    $this->questionAnswer->create($item_data);
                }
            }

            //update citations
            $oid = 1;
            $citation_data = array();
            foreach($citations as $citation)
            {
                if (is_array($citation) && count($citation)) {
                    $tmp = array(
                        'question_id' => $createQuestion->id,
                        'citation' => isset($citation['citation'])? $citation['citation']: '',
                        'description' => isset($citation['description'])? $citation['description'] : '',
                        'link' => isset($citation['link'])? $citation['link'] : '',
                        'order_id' => $oid,
                        'id' => isset($citation['id']) ? $citation['id'] : '',
                    );
                    array_push($citation_data, $tmp);
                    $oid++;
                }
            }
            //\Log::info("=============error===============".print_r($citation_data,true)); die;
            if(count($citation_data) > 0)
            {
                $this->citation->updateCitation($citation_data);
            }

            if ($law != 1) {
                // update audit type
                $this->updateQuestionClassifications($createQuestion->id, $request->audit_types, 'AUDIT_TYPE');

                // update country
                $this->updateQuestionClassification($createQuestion->id, $request->country, 'COUNTRY');

                // update state
                $this->updateQuestionClassification($createQuestion->id, $request->state, 'STATE');

                // update cities
                $this->updateQuestionClassifications($createQuestion->id, $request->cities, 'CITY');

                // update licence
                //$this->updateLicences($createQuestion->id, array($request->license_type));
                $this->updateLicences($createQuestion->id, $license_type);

                //update classification custom required
                $this->updateCustomClassifications($createQuestion->id, $reqClassifictions, 'required');

                // update classification not required
                $this->updateCustomClassifications($createQuestion->id, $nonReqClassifications, 'non_required');

                // classification main category
                $this->updateQuestionClassification($createQuestion->id, $mainCategory, $classificationId);
            }else {
                \Log::debug("making new classification for federal question!");
                $this->makeNewClassicationDataWhenLawTypeFederal($supperQuestionId, $createQuestion->id);
            }

            DB::commit();

            // find question answer
            $questionAnswer = $this->questionAnswer->findWhere(array('question_id' => $createQuestion->id))->all();

            return Response()->json(array('success' => 'true', 'data' => $questionAnswer, 'question_answer_id' => $createQuestion->question_answer_id, 'question_id' => $createQuestion->id), 200);



        }catch(Exception $ex){
            // Someting went wrong
            \Log::info("=============error===============");
            DB::rollback();

            return Response()->json(array('success' => 'false'), 200);

        }
    }

    /**
     *  get sub childs for a given question id
     *
     * @return array
     */
    public function subChildFounder($questionId,$sub_question_ids=array())
    {
        $chils = $this->question->findWhere(array('parent_question_id' => $questionId))->all();

        if(count($chils) > 0)
        {
            foreach($chils as $child)
            {
                array_push($sub_question_ids,$child->id);
                $sub_question_ids = $this->subChildFounder($child->id,$sub_question_ids);
                //\Log::info("=============sub===============".print_r(json_encode($sub_question_ids),true));
            }
        }
        return $sub_question_ids;
    }

    /**
     *  check redundant data exist on classifications table.
     *
     * @return array
     */
    public function checkRedundantOnChildSave($request,$questionId)
    {
        $sub_question_ids = $this->subChildFounder($questionId);

        $classifications_old = $this->questionClassification->findWhere(array('question_id' => $questionId));
        $old_audit_types = array();
        $old_country = "";
        $old_state = "";
        $old_cities = array();
        //$old_licence = "";
        $old_licences = array();

        foreach($classifications_old as $classification_old)
        {
            //$old_audit_type = $classification_old->entity_tag == "AUDIT_TYPE" ? $classification_old->option_value : $old_audit_type;
            if($classification_old->entity_tag == "AUDIT_TYPE")
            {
                array_push($old_audit_types, $classification_old->option_value);
            }
            $old_country = $classification_old->entity_tag == "COUNTRY" ? $classification_old->option_value : $old_country;
            $old_state = $classification_old->entity_tag == "STATE" ? $classification_old->option_value : $old_state;
            if($classification_old->entity_tag == "CITY")
            {
                array_push($old_cities, $classification_old->option_value);
            }
            //$old_licence = $classification_old->entity_tag == "LICENCE" ? $classification_old->option_value : $old_licence;
            if($classification_old->entity_tag == "LICENCE")
            {
                $temp = explode(',', (string)$classification_old->option_value);
                foreach($temp as $tmp)
                {
                    array_push($old_licences, $tmp);
                }

            }
        }

        //$old_licences = explode(',', (string)$old_licence);
        \Log::debug("cities  " . print_r($request->cities, true));
        \Log::debug("cities  law " . $request->law);
        $new_cities =  is_array($request->cities)? $request->cities : array($request->cities);;
        $new_audit_types = array();
        if(!empty($request->audit_types) && count($request->audit_types) > 0)
        {
            $new_audit_types = $request->audit_types;
        }

        $new_licences = array();
        /*if(!empty($request->license) && count($request->license) > 0)
        {
            foreach($request->license as $new_licence)
            {
                foreach($new_licence as $temp)
                {
                    array_push($new_licences, $temp);
                }
            }
        }*/
        if(isset($request->license_type))
        {
            foreach($request->license_type as $lt)
            {
                //\Log::info($lt['val']);
                if (isset($lt['val'] )) {
                    foreach($lt['val'] as $val)
                    {
                        array_push($new_licences, $val);
                    }
                }
            }

        }


        $dif_audit_types = array_diff($old_audit_types,$new_audit_types);
        if ($request->law == 2 && is_string($request->cities) && $request->cities == 'ALL') {
            $dif_cities = array();
        }else {
            $dif_cities = array_diff($old_cities,$new_cities);
        }
        $dif_licences = array_diff($old_licences,$new_licences);
        //\Log::info("=============old licences================".print_r(json_encode($dif_licences),true));
        if(count($sub_question_ids) > 0)
        {
            foreach($sub_question_ids as $question_id)//foreach 1
            {
                $classifications = $this->questionClassification->findWhere(array('question_id' => $question_id));
                $current_audit_types = array();
                $current_country = "";
                $current_state = "";
                $current_cities = array();
                //$old_licence = "";
                $current_licences = array();

                foreach($classifications as $classification)
                {
                    if($classification->entity_tag == "AUDIT_TYPE")
                    {
                        array_push($current_audit_types, $classification->option_value);
                    }
                    $current_country = $classification->entity_tag == "COUNTRY" ? $classification->option_value : $current_country;
                    $current_state = $classification->entity_tag == "STATE" ? $classification->option_value : $current_state;
                    if($classification->entity_tag == "CITY")
                    {
                        array_push($current_cities, $classification->option_value);
                    }
                    if($classification->entity_tag == "LICENCE")
                    {
                        $temp = explode(',', (string)$classification->option_value);
                        foreach($temp as $tmp)
                        {
                            array_push($current_licences, $tmp);
                        }

                    }
                }


                foreach($dif_audit_types as $dif_audit_type)
                {
                    if(in_array($dif_audit_type ,$current_audit_types ) )
                    {
                        return array('success' => false, 'msg' => 'Invalid Audit Type exists. Please save child questions first.');
                    }
                }
                foreach($dif_cities as $dif_city)
                {
                    if(in_array($dif_city ,$current_cities ) )
                    {
                        return array('success' => false, 'msg' => 'Invalid City exists. Please save child questions first.');
                    }
                }

                if($request->law!=1){
                    foreach($dif_licences as $dif_licence)
                    {
                        if(in_array($dif_licence ,$current_licences ) )
                        {
                            return array('success' => false, 'msg' => 'Invalid Licence exists. Please save child questions first.');
                        }
                    }
                }

            }//end foreach 1
        }
        return array('success' => true, 'msg' => 'sucess');
    }

    /**
     *  update child question.
     *
     * @return json
     */
    public function updateChildQuestion(UpdateChildQuestionRequest $request){
        $user_id = Auth::user()->id;
        $supperQuestionId = $request->superParentQuestionId;
        $questionId = $request->questionId;
        $visibility = $request->visibility;
        $mandatory = $request->mandatory;
        $question = $request->question;
        $explanation = $request->explanation;
        $answerId = $request->answerId;
        $parentQuestionId = $request->parentQuestionId;
        $actionItems = $request->actionItems;
        $answers = $request->answers;
        $reqClassifictions = $request->req_classification;
        $nonReqClassifications = $request->not_req_classification;

        $classificationId = $request->classificationId;
        $mainCategory = $request->mainCategory;
        $citations = $request->citations_child;
        $law = $request->law;

        $license_type = array();
        if(isset($request->license_type))
        {
            foreach($request->license_type as $lt)
            {
                //\Log::info($lt['val']);
                if (is_array($lt)) {
                    array_push($license_type, $lt['val']);
                }else {
                    array_push($license_type, $lt);
                }
            }
        }
//        $actionItems = json_decode($actionItems);


        $question_data = array(
            'question' => $question,
            'explanation' => $explanation,
            'is_mandatory' => $mandatory,
            'is_draft' => 1,
            'is_archive' => 0,
            'comment' => '',
            'question_answer_id' => $answerId,
            'parent_question_id' => $parentQuestionId,
            'supper_parent_question_id' => $supperQuestionId,
            'updated_by' => $user_id,
            'status' => $visibility,
            'law' => $law
        );
        $extra_records_exist = $this->checkRedundantOnChildSave($request,$questionId);
        if(!$extra_records_exist['success'])
        {
            return Response()->json(array('success' => 'false','msg' => $extra_records_exist['msg']), 200);
        }

        DB::beginTransaction();

        try{


            $updateQuestion = $this->question->update($question_data, $questionId);



            if($updateQuestion){
                //update citations
                $oid = 1;
                $citation_data = array();
                foreach($citations as $citation)
                {

                    if (is_array($citation) && count($citation)) {
                        $tmp = array(
                            'question_id' => $questionId,
                            'citation' => isset($citation['citation'])? $citation['citation']: '',
                            'description' => isset($citation['description'])? $citation['description'] : '',
                            'link' => isset($citation['link'])? $citation['link'] : '',
                            'order_id' => $oid,
                            'id' => isset($citation['id']) ? $citation['id'] : '',
                        );
                        array_push($citation_data, $tmp);
                        $oid++;
                    }
                }
                if(count($citation_data) > 0)
                {
                    $this->citation->updateCitation($citation_data);
                }

                // update action items
                $this->updateActionItems($questionId, $actionItems);

                // update add question answers
                $this->updateQuestionAnswer($questionId, $answers);

                // find question answer
                $questionAnswer = $this->questionAnswer->findWhere(array('question_id' => $questionId))->all();
                $answerCount = $this->question->findWhere(array('question_answer_id' => $answerId))->count();
                $questionAnswerId = $this->question->find($questionId);

                if ($law != 1) {
                    // update audit type
                    $this->updateQuestionClassifications($questionId, $request->audit_types, 'AUDIT_TYPE');

                    // update country
                    $this->updateQuestionClassification($questionId, $request->country, 'COUNTRY');

                    // update state
                    $this->updateQuestionClassification($questionId, $request->state, 'STATE');

                    // update cities
                    $this->updateQuestionClassifications($questionId, $request->cities, 'CITY');

                    //update classification custom required
                    $this->updateCustomClassifications($questionId, $reqClassifictions, 'required');

                    // update classification not required
                    $this->updateCustomClassifications($questionId, $nonReqClassifications, 'non_required');

                    // update licence
                    //$this->updateLicences($questionId, array($request->license_type));
                    $this->updateLicences($questionId, $license_type);
                }else {
                    \Log::debug("making new classification for federal question!");
                    $this->makeNewClassicationDataWhenLawTypeFederal($supperQuestionId, $questionId);
                }

                //\Log::info("=============classificationId...........................................".$mainCategory." ".$classificationId);
                // classification main category
                $this->updateQuestionClassification($questionId, $mainCategory, $classificationId);

                DB::commit();

                return Response()->json(array('success' => 'true', 'data' => $questionAnswer, 'question_answer_id' => $questionAnswerId->question_answer_id, 'answerCount' => $answerCount), 200);
            }

        }catch(Exception $ex){
            // Someting went wrong
            \Log::info("=============error===============");
            DB::rollback();

            return Response()->json(array('success' => 'false'), 200);

        }

    }


    /**
     *  Update Action Items.
     *
     * @return json
     */
    public function updateActionItems($questionId, $actionItems){

        \Log::debug("----aI------");
        \Log::debug(print_r($actionItems,true));
        \Log::debug("----aI------");

        $user_id = Auth::user()->id;

        // delete existing action items
        $items = $this->actionItem->findWhere(array('question_id' => $questionId))->all();
        foreach($items as $item){
            $this->actionItem->delete($item->id);
        }

        // add new action items
        if(!empty($actionItems)) {
            foreach ($actionItems as $item) {
                $item_data = array('name' => $item,
                    'question_id' => $questionId,
                    'created_by' => $user_id,
                    'updated_by' => $user_id,
                    'status' => 1
                );
                $this->actionItem->create($item_data);
            }
        }
    }



    /**
     *  Update question answer.
     *
     * @return json
     */
    public function updateQuestionAnswer($questionId, $answers){
        $user_id = Auth::user()->id;
        // Question Answer add
        $existingAnswers = $this->questionAnswer->findWhere(array('question_id' => $questionId, 'is_deleted' => 0))->all();
        $supperParentId = $this->question->find($questionId);

        if(!empty($answers)){
            foreach ($answers as $index => $answer) {
                $isFind = false;
                foreach($existingAnswers as $existingAnswer){
                    // if exists update
                    if($existingAnswer->answer_id == $answer['answerId']){
                        $item_data = array('answer_value_id' => $answer['answerOptionId'],
                            'answer_id' => $answer['answerId'],
                            'question_id' => $questionId,
                            'supper_parent_question_id' => $supperParentId->supper_parent_question_id,
                            'created_by' => $user_id,
                            'updated_by' => $user_id
                        );

                        $this->questionAnswer->update($item_data, $existingAnswer->id);
                        $isFind = true;
                        break;
                    }
                }

                if(!$isFind){
                    // add new answer
                    $item_data = array('answer_value_id' => $answer['answerOptionId'],
                        'answer_id' => $answer['answerId'],
                        'question_id' => $questionId,
                        'supper_parent_question_id' => $supperParentId->supper_parent_question_id,
                        'created_by' => $user_id,
                        'updated_by' => $user_id
                    );
                    $this->questionAnswer->create($item_data);
                }
            }
        }
    }

    // Delete Answer
    public function deleteQuestionAnswer(){
        $answerId = $_GET['answerId'];
        $questions = $this->question->findWhere(array('question_answer_id' => $answerId))->all();
        $question_data = array('is_deleted' => true);
        foreach($questions as $question){

            $this->question->update($question_data, $question->id);
        }

        $this->questionAnswer->update($question_data, $answerId);

        return Response()->json(array('success' => 'true', 'answerId' => $answerId), 200);
    }

    // Update Keywords
    public function updateKeywords($questionId, $keywords){
        $user_id = Auth::user()->id;
        // delete existing action items
        $items = $this->keyword->findWhere(array('question_id' => $questionId))->all();
        foreach($items as $item){
            $this->keyword->delete($item->id);
        }

        // add new keywords
        if(!empty($keywords)){
            foreach($keywords as $keyword){
                $masterKeyword = $this->masterKeyword->find($keyword);
                if(!empty($masterKeyword)){
                    $keyword_data = array('question_id' => $questionId,
                        'keyword_id' => $keyword,
                        'created_by' => $user_id,
                        'updated_by' => $user_id
                    );
                    $this->keyword->create($keyword_data);
                }
                else{
                    $master_keyword_data = array('name' => $keyword,
                        'created_by' => $user_id,
                        'updated_by' => $user_id
                    );
                    $response = $this->masterKeyword->create($master_keyword_data);
                    if($response){
                        $keyword_data = array('question_id' => $questionId,
                            'keyword_id' => $response->id,
                            'created_by' => $user_id,
                            'updated_by' => $user_id
                        );
                        $this->keyword->create($keyword_data);
                    }
                }

                $keyword_data = array('question_id' => $questionId,
                    'keyword_id' => $keyword,
                    'created_by' => $user_id,
                    'updated_by' => $user_id
                );
                $this->keyword->create($keyword_data);
            }
        }
    }

    // Update Question Classifications
    public function updateQuestionClassifications($questionId, $data, $entityTag){
        $user_id = Auth::user()->id;

        $items = $this->questionClassification->findWhere(array('question_id' => $questionId, 'entity_tag' => $entityTag))->all();
        foreach($items as $item){
            $this->questionClassification->delete($item->id);
        }

        // add new keywords
        if(!empty($data)){
            if (is_array($data)) {
                foreach ($data as $dt) {
                    $item_data = array('entity_tag' => $entityTag,
                        'question_id' => $questionId,
                        'option_value' => $dt,
                        'created_by' => $user_id,
                        'updated_by' => $user_id
                    );
                    $this->questionClassification->create($item_data);
                }
            }else {
                $item_data = array('entity_tag' => $entityTag,
                    'question_id' => $questionId,
                    'option_value' => $data,
                    'created_by' => $user_id,
                    'updated_by' => $user_id
                );
                $this->questionClassification->create($item_data);
            }

        }
    }

    // Update Question Classification
    public function updateQuestionClassification($questionId, $value, $entityTag){
        $user_id = Auth::user()->id;

        $items = $this->questionClassification->findWhere(array('question_id' => $questionId, 'entity_tag' => $entityTag))->all();
        foreach($items as $item){
            $this->questionClassification->delete($item->id);
        }

        $items = $this->questionClassification->findWhere(array('question_id' => $questionId, 'entity_tag' => "SUB_CATEGORY"))->all();
        foreach($items as $item){
            $this->questionClassification->delete($item->id);
        }

        if (is_array($value)) {
            foreach ($value as $dt) {
                $item_data = array('entity_tag' => $entityTag,
                    'question_id' => $questionId,
                    'option_value' => $dt,
                    'created_by' => $user_id,
                    'updated_by' => $user_id
                );
                $this->questionClassification->create($item_data);
            }
        }else {
            $item_data = array('entity_tag' => $entityTag,
                'question_id' => $questionId,
                'option_value' => $value,
                'created_by' => $user_id,
                'updated_by' => $user_id
            );
            $this->questionClassification->create($item_data);
        }

    }

    // update licences
    public function updateLicences($questionId, $licences){
        $user_id = Auth::user()->id;

        /*\Log::info('=====licences============');
        \Log::info(print_r($licences,true));
        \Log::info('=====licences============');*/

        $items = $this->questionClassification->findWhere(array('question_id' => $questionId, 'entity_tag' => 'LICENCE'))->all();
        foreach($items as $item){
            $this->questionClassification->delete($item->id);
        }

        if(!empty($licences)) {
            foreach ($licences as $lic) {
                $item_data = array('entity_tag' => 'LICENCE',
                    'question_id' => $questionId,
                    'option_value' => is_array($lic)? implode(",", $lic) : $lic,
                    'created_by' => $user_id,
                    'updated_by' => $user_id
                );
                $this->questionClassification->create($item_data);
            }
        }
    }

    // update classifications
    public function updateCustomClassifications($questionId, $classifications, $type){
        $user_id = Auth::user()->id;

        $items = $this->questionClassification->findCustomClassifications($questionId, $type);
        foreach($items as $item){
            $this->questionClassification->delete($item->id);
        }

        if(!empty($classifications)) {
            foreach ($classifications as $classifiction => $value) {
                if (is_array($value['value'])) {
                    foreach ($value['value'] as $val) {
                        $item_data = array('entity_tag' => $value['classificationId'],
                            'question_id' => $questionId,
                            'option_value' => $val,
                            'created_by' => $user_id,
                            'updated_by' => $user_id
                        );
                        $this->questionClassification->create($item_data);
                    }
                } else {
                    $item_data = array('entity_tag' => $value['classificationId'],
                        'question_id' => $questionId,
                        'option_value' => $value['value'],
                        'created_by' => $user_id,
                        'updated_by' => $user_id
                    );
                    $this->questionClassification->create($item_data);
                }
            }
        }
    }

    // check parent question
    public function checkParentQuestion(UpdateParentQuestionRequest $request){

        $questionId = $request->questionId;
        $question = $request->question;
        $explanation = $request->explanation;
        $keywords = $request->keywords;
        $mainCategory = $request->mainCategory;
        $classificationId = $request->classificationId;
        $auditTypes = $request->auditTypes;
        $country = $request->country;
        $state = is_array($request->state)? array_unique($request->state) : $request->state;
        $cities = $request->cities;
        $actionItems = $request->actionItems;
        $publishDate = $request->publishDate;
        $license = $request->license;
        $isDraft = $request->isDraft;


        $savedQuestion = $this->questionClassification->findWhere(array('question_id' => $questionId,'entity_tag' => 'LICENCE'));

        //\Log::info('====== ===== ======');
        foreach($savedQuestion as $sq)
        {
            //\Log::info($sq->option_value);
            //\Log::info(print_r($license,true));

            if(!in_array($sq->option_value ,$license ) )
            {
                if($isDraft == 0) {
                    $message = Config::get('messages.QUESTION_PUBLISH_SUCCESS');
                } else {
                    $message = Config::get('messages.QUESTION_UPDATE_SUCCESS');
                }

                return Response()->json(array('success' => 'true', 'message' => $questionId, 'message' => $message), 200);
            }

        }
        //\Log::info('====== ===== ======');

        //return Response()->json(array('success' => 'true', 'data' => $questionId), 200);
    }


    // update parent question
    public function updateParentQuestion(UpdateParentQuestionRequest $request){
        $published_date = '0000-00-00 00:00:00';
        $user_id = Auth::user()->id;
        $questionId = $request->questionId;
        $visibility = $request->visibility;
        $mandatory = $request->mandatory;
        $question = $request->question;
        $explanation = $request->explanation;
        $keywords = $request->keywords;
        $mainCategory = $request->mainCategory;
        $classificationId = $request->classificationId;
        $classificationParentID = $request->classificationParentID;
        $is_child = $request->is_child;
        $auditTypes = $request->auditTypes;
        $country = $request->country;
        $state = is_array($request->state)? array_unique($request->state) : $request->state;
        $cities = $request->cities;
        $actionItems = $request->actionItems;
        $license = $request->license;
        $reqClassifictions = $request->reqClassifictions;
        $nonReqClassifications = $request->nonReqClassifications;
        $answers = $request->answers;
        $isDraft = $request->isDraft;
        $citations = $request->citations;
        $law = $request->law;
        if($request->publishDate != '') {
            $published_date = date_format(date_create($request->publishDate),"Y-m-d H:i:s");
        }


        $extra_records_exist = $this->checkRedundantFromClassification($request);

        if(count($extra_records_exist) > 0)
        {
            return Response()->json(array('success' => 'false', 'data' => $extra_records_exist), 200);
        }
        $question_data = array(
            'question' => $question,
            'explanation' => $explanation,
            'is_mandatory' => $mandatory,
            'is_draft' => $isDraft,
            'is_archive' => 0,
            'comment' => '',
            'question_answer_id' => 0,
            'parent_question_id' => 0,
            'supper_parent_question_id' => $questionId,
            'updated_by' => $user_id,
            'status' => $visibility,
            'law'=>$law,
            'published_at' => $published_date
        );

        $actionItems = json_decode($actionItems);
        DB::beginTransaction();
        try{
            $updateQuestion = $this->question->update($question_data, $questionId);

            if($updateQuestion){

                //update citations
                $oid = 1;
                $citation_data = array();
                foreach($citations as $citation)
                {
                    //if(!empty($citation['citation_name']))

                    if (is_array($citation) && count($citation)) {
                        $tmp = array(
                            'question_id' => $questionId,
                            'citation' => isset($citation['citation'])? $citation['citation']: '',
                            'description' => isset($citation['description'])? $citation['description'] : '',
                            'link' => isset($citation['link'])? $citation['link'] : '',
                            'order_id' => $oid,
                            'id' => isset($citation['id']) ? $citation['id'] : '',
                        );
                        array_push($citation_data, $tmp);
                        $oid++;
                    }
                }
                //if(count($citation_data) > 0)
                {
                    $this->citation->updateCitation($citation_data);
                }

                // update Keywords
                $this->updateKeywords($questionId, $keywords);

                // update audit type
                $this->updateQuestionClassifications($questionId, $auditTypes, 'AUDIT_TYPE');

                // update country
                $this->updateQuestionClassification($questionId, $country, 'COUNTRY');

                // update state
                $this->updateQuestionClassification($questionId, $state, 'STATE');

                \Log::debug("all cities " . print_r($cities, true));
                // update cities
                $this->updateQuestionClassifications($questionId, $cities, 'CITY');

                // update licence
                $this->updateLicences($questionId, $license);


                if($is_child == "yes")
                {
                    $this->updateQuestionClassification($questionId, $classificationParentID, $classificationId);
                    $this->updateQuestionClassification($questionId, $mainCategory, 'SUB_CATEGORY');

                    //\Log::info('|||||||......sub..........|||||||||'.$classificationId." ".$classificationParentID);
                }
                else
                {
                    // classification main category
                    $this->updateQuestionClassification($questionId, $mainCategory, $classificationId);
                    //\Log::info('|||||||......main..........|||||||||');
                }

                //update classification custom required
                $this->updateCustomClassifications($questionId, $reqClassifictions, 'required');

                // update classification not required
                $this->updateCustomClassifications($questionId, $nonReqClassifications, 'non_required');

                // update action items
                $this->updateActionItems($questionId, $actionItems);

                // update add question answers
                $this->updateQuestionAnswer($questionId, $answers);

                // publish child questions
                if($isDraft == 0){
                    $this->publishChildQuestions($questionId);
                }

                //If type is fedaral remove sub questions classifications and inserts
                if($law==1){
                    $this->makeNewClassicationDataForAllChildernWhenLawTypeFederal($questionId);
                }
                DB::commit();

                return Response()->json(array('success' => 'true', 'data' => $questionId), 200);
            }
        }catch (Exception $e){

                DB::rollback();
                return Response()->json(array('success' => 'false', 'data' => $questionId), 200);
        }

    }

    //check redundant records exist or not from question classification table
    public function checkRedundantFromClassification($request){

        $response = array();

        $classifications_old = $this->questionClassification->findWhere(array('question_id' => $request->questionId));
        $old_audit_types = array();
        $old_country = "";
        $old_state = "";
        $old_cities = array();
        $old_licences = array();
        foreach($classifications_old as $classification_old)
        {
            if($classification_old->entity_tag == "AUDIT_TYPE")
            {
                array_push($old_audit_types, $classification_old->option_value);
            }
            $old_country = $classification_old->entity_tag == "COUNTRY" ? $classification_old->option_value : $old_country;
            $old_state = $classification_old->entity_tag == "STATE" ? $classification_old->option_value : $old_state;
            if($classification_old->entity_tag == "CITY")
            {
                array_push($old_cities, $classification_old->option_value);
            }
            if($classification_old->entity_tag == "LICENCE")
            {
                $temp = explode(',', (string)$classification_old->option_value);
                foreach($temp as $tmp)
                {
                    array_push($old_licences, $tmp);
                }

            }
        }
        //$old_licences = explode(',', (string)$old_licence);

        $new_cities = is_array($request->cities)? $request->cities : array($request->cities);
        $new_audit_types = $request->auditTypes;
        \Log::debug("new license : " . print_r($request->license, true));
        $new_licences = array();
        foreach($request->license as $new_licence)
        {
            if (is_array($new_licence)) {
                foreach($new_licence as $temp)
                {
                    array_push($new_licences, $temp);
                }
            }
        }


        //$new_licences = $request->license[0];
        $dif_audit_types = array_diff($old_audit_types,$new_audit_types);
        $dif_cities = array_diff($old_cities,$new_cities);
        $dif_licence = array_diff($old_licences,$new_licences);



        $parent_question = $this->question->findWhere(array('id' => $request->questionId));

        foreach($parent_question as $question)
        {
            $sub_questions = $this->question->findWhere(array('supper_parent_question_id' => $question->supper_parent_question_id));
            foreach($sub_questions as $sub_question)
            {
                if($sub_question->id != $request->questionId)
                {
                    //getting classifications for the sub questions.
                    $classifications_current = $this->questionClassification->findWhere(array('question_id' => $sub_question->id));

                    $sub_audit_types = array(); $sub_country = ""; $sub_state = ""; $sub_cities = array(); $sub_licence = "";
                    $sub_licences = array();

                    foreach($classifications_current as $classifications)
                    {
                       // $sub_audit_type = $classifications->entity_tag == "AUDIT_TYPE" ? $classifications->option_value : $sub_audit_type;
                        if($classifications->entity_tag == "AUDIT_TYPE")
                        {
                            array_push($sub_audit_types,$classifications->option_value);
                        }
                        $sub_country = $classifications->entity_tag == "COUNTRY" ? $classifications->option_value : $sub_country;
                        $sub_state = $classifications->entity_tag == "STATE" ? $classifications->option_value : $sub_state;

                        if($classifications->entity_tag == "CITY")
                        {
                            array_push($sub_cities, $classifications->option_value);
                        }

                        if($classifications->entity_tag == "LICENCE")
                        {
                            $temp = explode(',', (string)$classifications->option_value);
                            foreach($temp as $tmp)
                            {
                                array_push($sub_licences, $tmp);
                            }

                        }
                    }
                    if($request->law!=1  && count($dif_audit_types) > 0 )
                    {
                        //$dif_sub_licence = array_diff($dif_licence,$sub_licences);
                        foreach($dif_audit_types as $removed_audit_type_id)
                        {
                            if(in_array($removed_audit_type_id,$sub_audit_types))
                            {
                                array_push($response, array('msg' => 'Invalid Audit Type', 'data' =>$sub_question));
                            }
                        }
                    }

                    if($request->law!=1 && count($dif_licence) > 0 )
                    {
                        //$dif_sub_licence = array_diff($dif_licence,$sub_licences);
                        foreach($dif_licence as $removed_licence_id)
                        {
                            if(in_array($removed_licence_id,$sub_licences))
                            {
                                array_push($response, array('msg' => 'Invalid Licence', 'data' =>$sub_question));
                            }
                        }
                    }

                    if(count($dif_cities) > 0 )
                    {
                        //$dif_sub_licence = array_diff($dif_licence,$sub_licences);
                        foreach($dif_cities as $removed_city_id)
                        {
                            if(in_array($removed_city_id,$sub_cities))
                            {
                                array_push($response, array('msg' => 'Invalid City', 'data' =>$sub_question));
                            }
                        }
                    }


//                    \Log::info('|||||||................|||||||||');
//                    \Log::info(print_r($sub_licences,true));
//                    \Log::info('|||||||............................|||||||||');
                }
            }

        }
        return $response;
    }

    // publish all the child questions of a supper parent
    public function publishChildQuestions($superParentId){
        $questions = $this->question->findWhere(array('supper_parent_question_id' => $superParentId));
        foreach($questions as $question){
            $this->question->update(array('is_draft' => 0), $question->id);
        }
    }


    // Load Parent Questions
    public function allParentQuestions()
    {
        $data = array();
        $questionName = $_GET['questionName'];
        $keyWords= $_GET['keywords'];
        $status= $_GET['status'];
        $display= $_GET['display'];
        $sort=(isset($_GET['sort']))?$_GET['sort']:'desc';
        $sortType=(isset($_GET['sortType']))?$_GET['sortType']:'question';

        $questions = $this->question->searchQuestions($questionName, $status, $display,$sort,$sortType);
        if(!empty($keyWords)){
            $questionIds = array();
            foreach($questions as $index => $question){
                array_push($questionIds, $question->id);
            }

            $questionKeywords = $this->keyword->questionKeywordSearch($questionIds, $keyWords);

            if(!empty($questionKeywords)){
                $ids = array();
                foreach($questionKeywords as $keyword){
                    array_push($ids, $keyword->question_id);
                }
                $questions = $this->question->getQuestion($ids,$sort,$sortType);
            }
            else{
                $questions = array();
            }
        }

        $questionIds = [];
        foreach($questions as $question) {
            if ($question->parent_question_id == 0) {
                array_push($questionIds, $question['id']);
            }else {
                $q = $this->question->find($question['id']);
                array_push($questionIds, $q->parent_question_id);
            }
        }

        if (count($questionIds) > 0) {
            $questions = $this->question->getQuestion($questionIds,$sort,$sortType);
        }

        $returnArray = [];
        $i=1;
        foreach($questions as $key => $question) {
            if ($question->parent_question_id == 0) {

                $array = $this->populateSubQuestion($question->id,$question->id);
                $subCount=count($array);

                $actions = '';
                if ($question->status == 1) {
                    /*$actions .=   "<a class='btn btn-info btn-circle btn-xm' data-toggle='tooltip' title='View' data-question_id='".$question['id']."' onclick='previewQuestion({$question['id']})'><i class='fa fa-eye'></i></a>
                        <a class='btn btn-info btn-circle btn-xm' data-toggle='tooltip' title='Edit' data-question_id='".$question['id']."' onclick='updateQuestion({$question['id']})'><i class='fa fa-paste'></i></a>                      
                        <a class='btn btn-success btn-circle btn-xm' data-toggle='tooltip' title='Active'  data-question_id='".$question['id']."'onclick='changeQuestionStatus({$question['id']}, 0, {$question['is_draft']})'><i class='fa fa-thumbs-o-up'></i></a>
                        <a class='btn btn-danger btn-circle btn-xm' data-toggle='tooltip' data-target='#locationDelete' title='Delete' data-question_id='".$question['id']."'onclick='deleteQuestion({$question['id']})'><i class='fa fa-trash-o'></i></a>";*/

                    $actions .=   "<a class='btn btn-info btn-circle btn-xm' data-toggle='tooltip' title='View' data-question_id='".$question['id']."' onclick='previewQuestion({$question['id']})'><i class='fa fa-eye'></i></a>                                 
                        <a class='btn btn-success btn-circle btn-xm' data-toggle='tooltip' title='Active'  data-question_id='".$question['id']."'onclick='changeQuestionStatus({$question['id']}, 0, {$question['is_draft']})'><i class='fa fa-thumbs-o-up'></i></a>
                        <a class='btn btn-danger btn-circle btn-xm' data-toggle='tooltip' data-target='#locationDelete' title='Delete' data-question_id='".$question['id']."'onclick='deleteQuestion({$question['id']})'><i class='fa fa-trash-o'></i></a>
                        <a class='btn btn-primary btn-circle btn-xm' title='Question log' data-question_id='".$question['id']."'onclick='questionLogView({$question['id']})'><i class='fa pe-7s-repeat'></i></a>";
                }else {
                    $actions .= "<a class='btn btn-info btn-circle btn-xm' data-toggle='tooltip' title='Edit' data-question_id='".$question['id']."' onclick='previewQuestion({$question['id']})'><i class='fa fa-eye'></i></a>
                    <a class='btn btn-warning btn-circle btn-xm' data-toggle='tooltip' title='Inactive' data-question_id='".$question['id']."'onclick='changeQuestionStatus({$question['id']}, 1, {$question['is_draft']})'><i class='fa fa-thumbs-o-down'></i></a>
                    <a class='btn btn-danger btn-circle btn-xm' data-toggle='tooltip' data-target='#locationDelete' title='Delete' data-question_id='".$question['id']."'onclick='deleteQuestion({$question['id']})'><i class='fa fa-trash-o'></i></a>
                    <a class='btn btn-primary btn-circle btn-xm' title='Question log' data-question_id='".$question['id']."'onclick='questionLogView({$question['id']})'><i class='fa pe-7s-repeat'></i></a>";
                }

                if ($question->is_draft == 1) {
                    $display = "<span title='Draft' style='padding: 5px;' data-question_id='".$question['id']."' class='badge badge-success'>Draft</span>";
                }else {
                    $display = "<span title='Published' style='padding: 5px;' data-question_id='".$question['id']."' class='badge badge-warning'>Published</span>";
                }

//                $logs = "<a class='btn btn-primary btn-circle btn-xm' title='Question log' data-question_id='".$question['id']."'onclick='questionLogView({$question['id']})'><i class='fa pe-7s-repeat'></i></a>";


                $arrayTemp = array(
                    'id' => $question->id,
                    'name' => substr($question->question,0,100),
                    'parent_question_id' => $question->parent_question_id,
                    "action" => $actions,
                    "display" => $display,
                    'children' => $array,
                    'createdBy' => $question->createdUser,
                    'updatedBy' => $question->updatedUser,
                    'createdAt' => date('m/d/Y g:i a', strtotime(str_replace('/', '-', $question->created_at))),
                    'level'=>$question->id,
                    'count'=>$subCount
                );
                $i++;
                $returnArray[]= $arrayTemp;
            }else {
//                $array = $this->populateSubQuestion($question->parent_question_id);
//                $arrayTemp = array('id' => $question->id, 'name' => $question['question'], 'questions' => $array, );
//                $returnArray['questions'][]= $arrayTemp;
            }
        }

        $data = $returnArray;
//        $links = \Pagination::makeLengthAware($data, count($data), 1);

//        foreach($questions as $question) {
//            $createdBy = $this->user->find($question['created_by'], array("*"));
//            $updatedBy = $this->user->find($question['updated_by'], array("*"));
//            $data[] = array(
//                //$question['id'],
//                $question['question'],
//                $createdBy->name,
//                $updatedBy->name,
//                date('m/d/Y g:i a', strtotime(str_replace('/', '-', $question['created_at']))),
//
//
//                ($question['status'] == 1)?
//                    $row[] =   "<a class='btn btn-info btn-circle' data-toggle='tooltip' title='Edit' data-question_id='".$question['id']."' onclick='updateQuestion({$question['id']})'><i class='fa fa-paste'></i></a>
//                        <a class='btn btn-success btn-circle' data-toggle='tooltip' title='Active'  data-question_id='".$question['id']."'onclick='changeQuestionStatus({$question['id']}, 0, {$question['is_draft']})'><i class='fa fa-thumbs-o-up'></i></a>
//                        <a class='btn btn-danger btn-circle' data-toggle='tooltip' data-target='#locationDelete' title='Delete' data-question_id='".$question['id']."'onclick='deleteQuestion({$question['id']})'><i class='fa fa-trash-o'></i></a>
//                    ":
//                    $row[] =     "<a class='btn btn-info btn-circle' data-toggle='tooltip' title='Edit' data-question_id='".$question['id']."' onclick='updateQuestion({$question['id']})'><i class='fa fa-paste'></i></a>
//                    <a class='btn btn-warning btn-circle' data-toggle='tooltip' title='Inactive' data-question_id='".$question['id']."'onclick='changeQuestionStatus({$question['id']}, 1, {$question['is_draft']})'><i class='fa fa-thumbs-o-down'></i></a>
//                    <a class='btn btn-danger btn-circle' data-toggle='tooltip' data-target='#locationDelete' title='Delete' data-question_id='".$question['id']."'onclick='deleteQuestion({$question['id']})'><i class='fa fa-trash-o'></i></a>
//                    ",
//
//                ($question['is_draft'] == 1)?
//                    $row[] =   "<a class='btn btn-success btn-circle'  title='Draft'  data-question_id='".$question['id']."'><i class='fa pe-7s-file'></i></a>
//                    ":
//                    $row[] =     "
//                    <a class='btn btn-info btn-circle' title='Published' data-question_id='".$question['id']."'><i class='fa pe-7s-photo-gallery'></i></a>
//                    ",
//                $row[] = "<a class='btn btn-primary btn-circle' title='Question log' data-question_id='".$question['id']."'onclick='questionLogView({$question['id']})'><i class='fa pe-7s-repeat'></i></a>"
//            );
//        }
//        return response()->json(["data" => $data]);

        //Get current page form url e.g. &page=6
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        //Create a new Laravel collection from the array data
        $collection = new Collection($data);

        //Define how many items we want to be visible in each page
        $count=$this->userSettings->getPerPage();

        if(isset($_GET['entries'])){
        $perPage = $_GET['entries'];
        }
        elseif(isset($count['type_value'])){
        $perPage = $count['type_value'];
        }else{
            $perPage = 25;
        }



        //Slice the collection to get the items to display in current page
        $currentPageSearchResults = $collection->slice (($currentPage - 1) * $perPage, $perPage)->all();

        //Create our paginator and pass it to the view
        $paginatedSearchResults= new LengthAwarePaginator($currentPageSearchResults, count($collection), $perPage);

        return response()->json(["data" => $paginatedSearchResults->toArray()]);
    }
    /**
     * Recursively find and populate question and sub question hierarchy
     * @param $parentId
     * @return array
     */
    private function populateSubQuestion($parentId,$level=false) {
        $i=0;
        $questions=$this->question->getSubQuestionForQuestionList($parentId);
        $arr = array();
        foreach($questions as $question) {
            if($question['parent_question_id'] == $parentId){
                if ($level) {
                    $level_name = $level. '.'.++$i;
                }
                else {
                    $level_name = $level.++$i;
                }
            }
            $subQuestion=$this->populateSubQuestion($question->id,$level_name);
            $arr[] = array(
                "id" => $question->id,
                "name" => substr($question->question,0,100),
                "parent_question_id" => $question->parent_question_id,
                "children" => $subQuestion,
                "createdBy" => $question->createdUser,
                "updatedBy" => $question->updatedUser,
                "createdAt" => date('m/d/Y g:i a', strtotime(str_replace('/', '-', $question->created_at))),
                "level" => $level_name,
                "count" =>count($subQuestion)
            );
        }
        return $arr;
    }
    // update question status
    public function updateQuestionStatus(){
        $questionId = $_POST['questionId'];
        $status= $_POST['status'];
        $question_data = array('status' => $status);

        $response = $this->question->update($question_data, $questionId);

        if($response) {
            $message =  Config::get('messages.QUESTION_STATUS_UPDATE_SUCCESS');
            return response()->json(array('success' => 'true', 'message'=> $message));
        } else {
            $message = Config::get('messages.QUESTION_STATES_UPDATE_FAILED');
            return response()->json(array('success' => 'false', 'message'=> $message));
        }

    }

    // delete question
    public function deleteQuestion(){
        $questionId = $_POST['questionId'];
        $question_data = array('is_deleted' => 1);

        \Log::info('====== ===== ======');
        // Start DB transaction
        DB::beginTransaction();

        try{

            $response = $this->question->update($question_data, $questionId,"supper_parent_question_id");
            \Log::info($response);
            if($response){

                $message =  Config::get('messages.QUESTION_DELETE_SUCCESS');

                // commit transaction
                DB::commit();
                return response()->json(array('success' => 'true', 'message'=> $message));
            }
            else{
                DB::rollback();
                $message = Config::get('messages.QUESTION_DELETE_FAILED');
                return response()->json(array('success' => 'false', 'message'=> $message));
            }
        }
        catch(exception $ex){
            DB::rollback();
            $message = Config::get('messages.QUESTION_DELETE_FAILED');
            return response()->json(array('success' => 'false', 'message'=> $message));
        }
        //////
//        $response = $this->question->update($question_data, $questionId);
//        \Log::info('====== ===== ======');
//        \Log::info($response);
//
//
//        if($response) {
//            $message =  Config::get('messages.QUESTION_DELETE_SUCCESS');
//            return response()->json(array('success' => 'true', 'message'=> 'dddd'));
//        } else {
//            $message = Config::get('messages.QUESTION_DELETE_FAILED');
//            return response()->json(array('success' => 'false', 'message'=> $message));
//        }

    }

    /**
     *  Create New Version.
     *
     * @return json
     */
    public function createNewVersion(){
        $questionId = $_POST['questionId'];
        $comment = $_POST['comment'];

        // Start DB transaction
        DB::beginTransaction();

        try{
            $question = $this->question->find($questionId);

            $response = $this->createParentQuestionVersion($question, $comment);



            if($response){
                $questionAnswers = $this->questionAnswer->findWhere(array('question_id' => $questionId));
                $this->createQuestionAnswerVersion($response, $questionAnswers, $question, $response->id);

                //create citation version
                //$this->createCitationVersion($question,$response);

                // commit transaction
                DB::commit();
                return Response()->json(array('success' => 'true',  'data' => $response->id, 'message' => 'successfully created the version'), 200);
            }
            else{
                DB::rollback();
                return response()->json(array('success' => 'false', 'message'=> 'error while creating the new version'));
            }
        }
        catch(exception $ex){
            DB::rollback();
            return response()->json(array('success' => 'false', 'message'=> 'error while creating the new version'));
        }

    }

    function createCitationVersion($question,$response)
    {
        //$data = array();
        //$data['supper_parent_question_id'] = $parent_question_id;
        $res = $this->question->findAllInQuestion($question,$response);

        //\Log::info('================ kkkkkkkkkkkkkkkk================'.json_encode($response,true)); die;
    }


    /**
     *  Create Parent Question Version
     * @param $question
     * @param $comment
     * @return array
     */
    private function createParentQuestionVersion($question, $comment){
        $data = array(
            "version_no" => intval($question->version_no + 1),
            "law"=>$question->law,
            "question" => $question->question,
            "explanation" => $question->explanation,
            "is_mandatory" => $question->is_mandatory,
            "is_draft" => 1,
            "is_archive" => 0,
            "comment" => $comment,
            "question_answer_id" => 0,
            "parent_question_id" => 0,
            "master_question_id" => $question->master_question_id,
            "previous_question_id" => $question->id,
            "status" => $question->status,
            "is_deleted" => $question->is_deleted,
            "created_by" => Auth::user()->id,
            "updated_by" => Auth::user()->id,
            "supper_parent_question_id" => 0
        );
        //\Log::info('================ kkkkkkkkkkkkkkkk================'.print_r($question,true)); die;

        $response = $this->question->create($data);
        if($response){
            // update supper parent question id
            $this->question->update(array('supper_parent_question_id' => $response->id), $response->id);
            $this->question->update(array('is_archive' => 1), $question->id);

            // create question classifications
            $this->createClassificationVersion($question->id, $response->id);

            // create question keywords
            $this->createQuestionKeywordVersion($question->id, $response->id);

            // create question action items
            $this->createQuestionActionItemsVersion($question->id, $response->id);

            // create citation
            $this->citation->createCitationVersion($question->id, $response->id);
        }

        return $response;

    }

    /**
     *  Create Child Question Version
     * @param $question
     * @param $childQuestion
     * @param $questionAnswerId
     * @param $parentQuestionId
     * @param $parentQuestionId
     * @param $superParentQuestionId
     *
     * @return array
     */
    private function createChildQuestionVersion($question, $childQuestion, $questionAnswerId, $parentQuestionId, $superParentQuestionId){
        $data = array(
            "version_no" => $question->version_no,
            "question" => $childQuestion->question,
            "law"=>$childQuestion->law,
            "explanation" => $childQuestion->explanation,
            "is_mandatory" => $childQuestion->is_mandatory,
            "is_draft" => 1,
            "is_archive" => 0,
            "comment" => "",
            "question_answer_id" => $questionAnswerId,
            "parent_question_id" => $parentQuestionId,
            "master_question_id" => $childQuestion->master_question_id,
            "previous_question_id" => $childQuestion->id,
            "status" => $childQuestion->status,
            "is_deleted" => $childQuestion->is_deleted,
            "created_by" => Auth::user()->id,
            "updated_by" => Auth::user()->id,
            "supper_parent_question_id" => $superParentQuestionId
        );


        $response = $this->question->create($data);

        // create question classifications
        $this->createClassificationVersion($childQuestion->id, $response->id);

        if($response){
            $this->question->update(array('is_archive' => 1), $childQuestion->id);
            $questionAnswers = $this->questionAnswer->findWhere(array('question_id' => $childQuestion->id));

            // create question action items
            $this->createQuestionActionItemsVersion($childQuestion->id, $response->id);

            $this->createQuestionAnswerVersion($response, $questionAnswers, $childQuestion, $superParentQuestionId);

            // create citation version
            $this->citation->createCitationVersion($childQuestion->id, $response->id);
            \Log::info('================ kkkkkkkkkkkkkkkk================'.json_encode($childQuestion,true));
        }

        //start update other sub questions into classification table
        $allQuestions = $this->question->findAllBy('supper_parent_question_id',$superParentQuestionId);
    }


    /**
     *  Create Question Answer Version
     * @param $question
     * @param $questionAnswers
     * @param $preQuestion
     * @param $superParentQuestionId
     *
     * @return array
     */
    private function createQuestionAnswerVersion($question, $questionAnswers, $preQuestion, $superParentQuestionId)
    {
        if (!empty($questionAnswers)) {
            foreach ($questionAnswers as $answer) {
                $data = array(
                    "pre_answer_id" => $answer->id,
                    "answer_value_id" => $answer->answer_value_id,
                    "answer_id" => $answer->answer_id,
                    "question_id" => $question->id,
                    "is_deleted" => $answer->is_deleted,
                    "created_by" => Auth::user()->id,
                    "updated_by" => Auth::user()->id,
                    "supper_parent_question_id" => $superParentQuestionId
                );
                $this->questionAnswer->create($data);
            }

            $childQuestions = $this->question->findChildQuestions($preQuestion);

            if (!empty($childQuestions)) {
                foreach ($childQuestions as $childQuestion) {
                    $questionAnswerId = $this->questionAnswer->findWhere(array('pre_answer_id' => $childQuestion->question_answer_id))->first();
                    $parentQuestionId = $this->question->findWhere(array('previous_question_id' => $childQuestion->parent_question_id, 'is_archive' => 0))->first();

                    $this->createChildQuestionVersion($question, $childQuestion, $questionAnswerId->id, $parentQuestionId->id, $superParentQuestionId);
                }
            }
        }

    }

    /**
     *  Create Question Classification Version
     * @param $oldQuestionId
     * @param $newQuestionId
     * @return array
     */
    private function createClassificationVersion($oldQuestionId, $newQuestionId){

        $classifications = $this->questionClassification->findWhere(array('question_id' => $oldQuestionId));


        foreach($classifications as $classification){
            $data = array(
                'question_id' => $newQuestionId,
                'entity_tag' => $classification->entity_tag,
                'option_value' => $classification->option_value,
                "created_by" => Auth::user()->id,
                "updated_by" => Auth::user()->id
            );
            $this->questionClassification->create($data);
        }
    }


    /**
     *  Create Question Keywords Version
     * @param $oldQuestionId
     * @param $newQuestionId
     * @return array
     */
    private function createQuestionKeywordVersion($oldQuestionId, $newQuestionId){
        $keywords = $this->keyword->findWhere(array('question_id' => $oldQuestionId));

        foreach($keywords as $keyword){
            $data = array(
                'question_id' => $newQuestionId,
                'keyword_id' => $keyword->keyword_id,
                "created_by" => Auth::user()->id,
                "updated_by" => Auth::user()->id
            );
            $this->keyword->create($data);
        }
    }


    /**
     *  Create Question Action Items Version
     * @param $oldQuestionId
     * @param $newQuestionId
     * @return array
     */
    private function createQuestionActionItemsVersion($oldQuestionId, $newQuestionId){
        $actionItems = $this->actionItem->findWhere(array('question_id' => $oldQuestionId));

        foreach($actionItems as $actionItem){
            $data = array(
                'question_id' => $newQuestionId,
                'name' => $actionItem->name,
                'status' => $actionItem->status,
                "created_by" => Auth::user()->id,
                "updated_by" => Auth::user()->id
            );
            $this->actionItem->create($data);
        }
    }


    /**
     *  Find question versions

     * @return Json
     */
    public function findQuestionVersions(){
        $questionId = $_GET['questionId'];

        $question = $this->question->find($questionId);
        $questionVersions = $this->question->findQuestionVersions($question->master_question_id, $questionId);

        $returnArray = Array();

        foreach($questionVersions as $questionVersion){
            $createdBy = $this->user->find($questionVersion->created_by);
            $updatedBy = $this->user->find($questionVersion->updated_by);


            $temp['id'] = $questionVersion->id;
            $temp['version_no'] = $questionVersion->version_no;
            $temp['user_name'] = $questionVersion->user->name;
            $temp['created_by'] = $createdBy->name;
            $temp['updated_by'] = $updatedBy->name;
            $temp['created_at'] = date('m/d/Y g:i a', strtotime(str_replace('/', '-', $questionVersion->created_at)));
            $temp['updated_at'] = date('m/d/Y g:i a', strtotime(str_replace('/', '-', $questionVersion->updated_at)));

            array_push($returnArray, $temp);
        }
        return Response()->json(array('success' => 'true',  'data' => $returnArray), 200);

    }

    /**
     *  Export Questions
     */
    public function export(){

        $result=$this->getQuestionsList();
        $pdf_generator=new PdfGenerator();
        $pdf_generator->ImprovedTable($result);


    }

    public function export_csv()
    {
        $questions = $this->getQuestionsList('all','all','all');
        $data = array();
        //echo json_encode($questions);

        if(count($questions) > 0)
        {
            foreach($questions as $question)
            {
                $licenses = $this->getLicencesForQuestion($question['question_id']);
                $city_count = $this->getCityCountForQuestion($question['question_id']);

                $data[] = array(
                    $question['level'],
                    $question['question_id'],
                    $question['question'],
                    $question['category_name'],
                    $question['action_items'][0]['name'],
                    isset($licenses[0]) ? $licenses[0] : '',
                    $city_count,
                    $question['is_archive'] == 1 ? "Archived" : "Not Archived",
                    $question['is_active'] == 1 ? "Active" : "Inactive",
                    $question['is_draft'] == 1 ? "Draft" : "Published",
                );

                /*if(count($question['action_items']) > 1)
                {
                    $x = 0;
                    foreach($question['action_items'] as $action_item)
                    {
                        if($x > 0)
                        {
                            $data[] = array(
                                $question['level'],
                                $question['question_id'],
                                $question['question'],
                                $question['category_name'],
                                $action_item['name'],
                                '',
                                $city_count,
                                $question['is_archive'] == 1 ? "Archived" : "Not Archived",
                                $question['is_active'] == 1 ? "Active" : "Inactive",
                                $question['is_draft'] == 1 ? "Draft" : "Published",
                            );
                        }
                        $x++;
                    }
                }*/

                if(count($licenses) > 1)
                {
                    $x = 0;
                    foreach($licenses as $license)
                    {
                        if($x > 0)
                        {
                            $data[] = array(
                                $question['level'],
                                $question['question_id'],
                                $question['question'],
                                $question['category_name'],
                                $question['action_items'][0]['name'],
                                $license,
                                $city_count,
                                $question['is_archive'] == 1 ? "Archived" : "Not Archived",
                                $question['is_active'] == 1 ? "Active" : "Inactive",
                                $question['is_draft'] == 1 ? "Draft" : "Published",
                            );
                        }
                        $x++;
                    }
                }
            }
        }
        //echo json_encode($data);

        // Define headers
        $headers = ['Level','ID', 'Question', 'Category', 'Action Items','Licenses','City Count','Archive','Active','Draft'];
        // Define file name
        $filename = "Questions.csv";
        // Create CSV file
        return $this->csv->create($data, $headers, $filename);

    }

    public function getCityCountForQuestion($question_id)
    {
        $cities = DB::table('question_classifications')
            ->select('option_value')
            ->where('question_id', '=', $question_id)
            ->where('entity_tag', '=', 'CITY')
            ->get();

        return count($cities);
    }

    public function getLicencesForQuestion($question_id)
    {
        $option_values = DB::table('question_classifications')

                        ->select('option_value')
                        ->where('question_id', '=', $question_id)
                        ->where('entity_tag', '=', 'LICENCE')
                        ->get();

        $licenses = array();
        if(count($option_values) > 0)
        {
            foreach($option_values as $option_value)
            {
                //\Log::info("=============option_values11111....===============".print_r($option_value->option_value,true));
                if($option_value->option_value == "ALL")
                {
                    array_push($licenses,$option_value->option_value);
                }
                else
                {
                    $split = explode(',', $option_value->option_value);

                    $res = DB::table('master_licenses')
                        ->select('name')
                        //->where('id', '=', $option_value->option_value)
                        ->whereIn('id', $split)
                        ->get();
                    $str = "";
                    if(count($res) > 0)
                    {
                        foreach($res as $r)
                        {
                            $str .= $r->name.",";
                        }
                    }
                    $str = substr($str, 0, -1);
                    array_push($licenses,$str);
                }
            }
        }
        //\Log::info("=============option_values....===============".print_r($licenses,true));
        return $licenses;
    }


    public function getAllQuestions()
    {
        $tree=''; $order_by='';
        $result = $this->question->getQuestionList();

        $dataset = [];
        $categories = [];

        foreach($result as $data){
            // Questions
            $dataset[$data->question_id]['question_id'] = $data->question_id;
            $dataset[$data->question_id]['parent_question_id'] = $data->parent_question_id;
            $dataset[$data->question_id]['question'] = $data->question;
            $dataset[$data->question_id]['explanation'] = $data->explanation;
            $dataset[$data->question_id]['appointment_id'] = $data->appointment_id;
            $dataset[$data->question_id]['category_id'] = "" . $data->category_id;
            $dataset[$data->question_id]['category_name'] = $data->category_name;
            $dataset[$data->question_id]['comment'] = $data->comment;
            $dataset[$data->question_id]['option_value'] = $data->option_value;
            $dataset[$data->question_id]['report_status'] = $data->report_status;

            // Categories
            $categories[$data->category_id]['id'] = $data->category_id;
            $categories[$data->category_id]['name'] = $data->category_name;

            // Action items
            if($data->answer_value_id == 2 or $data->answer_value_id == 3){
                $dataset[$data->question_id]['action_items'][$data->action_item_id]['id'] = $data->action_item_id;
                $dataset[$data->question_id]['action_items'][$data->action_item_id]['name'] = $data->action_item_name;
                $dataset[$data->question_id]['action_items'][$data->action_item_id]['status'] = $data->action_item_status;
            }

            // Set users
            if($data->action_item_user_id != ''){
                $dataset[$data->question_id]['action_items'][$data->action_item_id]['assigned_users'][$data->action_item_user_id]['id'] = $data->action_item_user_id;
                $dataset[$data->question_id]['action_items'][$data->action_item_id]['assigned_users'][$data->action_item_user_id]['name'] = $data->user_name;
            }

            // Answers
            $dataset[$data->question_id]['answers'][$data->answer_id]['answer_id'] = "" . $data->answer_id;
            $dataset[$data->question_id]['answers'][$data->answer_id]['answer_value_name'] = "" . $data->answer_value_name;
            $dataset[$data->question_id]['answers'][$data->answer_id]['answer_value_id'] = "" . $data->answer_value_id;

            $dataset[$data->question_id]['answer_value_name'] = "" . $data->answer_value_name;
            $dataset[$data->question_id]['answer_value_id'] = "" . $data->answer_value_id;

            // Set images
            if($data->image_name != ''){
                $dataset[$data->question_id]['images'][] = Config::get('simplifya.BUCKET_IMAGE_PATH') . ltrim(Config::get('simplifya.ACTION_COMMENT_IMG_DIR'), "/"). '/' . $data->image_name;
            }

            //set citations
            $citations = $this->citation->getCitations($data->question_id)->toArray();
            $citation_list = "";
            foreach($citations as $citation)
            {
                $citation_list .= " ".$citation['citation'].",";
            }
            //$dataset[$data->question_id]['citations'] = $citations;
            $citation_list = substr($citation_list, 0, -1);
            $dataset[$data->question_id]['citation_list'] = $citation_list != false ? "Citations : ".$citation_list : '';
        }
        \Log::debug("==== dataset ........" . print_r($dataset, true));
        // Init formated dataset
        $dataset_fotmated = [];

        // Format dataset
        foreach($dataset as $key => $data){
            // Set undefined images
            $data['images'] = isset($data['images']) ? array_values(array_unique(array_values($data['images']))) : [];

            // Set undefined action item users
            if(isset($data['action_items'])){
                foreach($data['action_items'] as $action_item){
                    $data['action_items'][$action_item['id']]['assigned_users'] = isset($data['action_items'][$action_item['id']]['assigned_users']) ? array_values($data['action_items'][$action_item['id']]['assigned_users']) : [];
                }
            }
            // Set undefined answers
            $data['answers'] = isset($data['answers']) ? array_values($data['answers']) : [];
            // Set undefined action items
            $data['action_items'] = isset($data['action_items']) ? array_values($data['action_items']) : [];

            $dataset_fotmated[$key] = $data;
        }

        // Get inspection report tree view configuration from master config
        $master_data_tree_view = $this->master_data->findBy('name', 'INSPECTION_REPORT_TREE_VIEW');
        $enable_tree_view = false;
        if (isset($master_data_tree_view)) {
            $enable_tree_view = ($master_data_tree_view->value == 1)? true : false;
        }

        // Build question tree
        if($tree != '' && $enable_tree_view){
            $dataset_fotmated = $this->buildTree($dataset_fotmated, 0);
        }else {
            /*$index = 1;
            foreach ($dataset_fotmated as &$dataSet){
                $dataSet['level'] = $index;
                $index++;
            }*/
            $tree_fotmated = $this->buildTree($dataset_fotmated, 0);
            $dataset_fotmated = $this->flattenQuestionList($tree_fotmated);
            foreach ($dataset_fotmated as &$dataSet){
                $dataSet['treeview'] = (int) $enable_tree_view;
                if (isset($dataSet['questions'])) {
                    unset($dataSet['questions']);
                }
            }
        }

        $dataset_all = [
            'categories' => array_values($categories),
            'questions' => array_values($dataset_fotmated),
        ];


        return $dataset_all;
    }


    public function flattenQuestionList($dataset_fotmated,&$flatten_data=array()){
        foreach ($dataset_fotmated as $dataset){

            array_push($flatten_data,$dataset);

            if (isset($dataset['questions'])){
                foreach ($dataset['questions'] as $question){
                    $sub_questions=array();
                    $sub_questions=[$question];
                    $this->flattenQuestionList($sub_questions,$flatten_data);
                }
            }

        }


        return $flatten_data;

    }

    

    /**
     *  Get all Questions
     * @return $flat_data
     */
    public function getQuestionsList($is_draft=0,$is_archive=0,$status=1){
        //\Log::info('================ START PARENT================'.print_r($is_archive,true)); die;
        $result = $this->question->getQuestionsList($is_draft,$is_archive,$status);


        $dataset = [];
        foreach($result as $data){
            // Questions
            $dataset[$data->id]['question_id'] = $data->id;
            $dataset[$data->id]['parent_question_id'] = $data->parent_question_id;
            $dataset[$data->id]['question'] = $data->question;
            $dataset[$data->id]['explanation'] = $data->explanation;
            $dataset[$data->id]['category_id'] = $data->category_id;
            $dataset[$data->id]['category_name'] = $data->category_name;

            $dataset[$data->id]['is_archive'] = $data->is_archive;
            $dataset[$data->id]['is_draft'] = $data->is_draft;
            $dataset[$data->id]['is_active'] = $data->status;

            // Categories
            $categories[$data->category_id]['id'] = $data->category_id;
            $categories[$data->category_id]['name'] = $data->category_name;

            // Action items
                $dataset[$data->id]['action_items'][$data->action_item_id]['id'] = $data->action_item_id;
                $dataset[$data->id]['action_items'][$data->action_item_id]['name'] = $data->action_item_name;
                $dataset[$data->id]['action_items'][$data->action_item_id]['status'] = $data->action_item_status;

        }


        $dataset_fotmated = [];

        // Format dataset
        foreach($dataset as $key => $data){

            // Set undefined action item users
            if(isset($data['action_items'])){
                foreach($data['action_items'] as $action_item){
                    $data['action_items'][$action_item['id']]['assigned_users'] = isset($data['action_items'][$action_item['id']]['assigned_users']) ? array_values($data['action_items'][$action_item['id']]['assigned_users']) : [];
                }
            }
            // Set undefined action items
            $data['action_items'] = isset($data['action_items']) ? array_values($data['action_items']) : [];

            $dataset_fotmated[$key] = $data;
        }
        $dataset_fotmated = $this->buildTree($dataset_fotmated);
        $flat_data=$this->flattenQuestionList($dataset_fotmated);
        return $flat_data;


    }
    /**
     * Build questions tree
     * @param array $elements
     * @param $parent_id
     * @return $branch
     */
    function buildTree(array &$elements, $parent_id = 0, $level=false) {

        $branch = array();$i = 0;
        foreach($elements as &$element) {


            if($element['parent_question_id'] == $parent_id){
                if ($level) {
                    $level_name = $level. '.'.++$i;
                }
                else {
                    $level_name = $level.++$i;
                }
                $element[ 'level' ] = $level_name;
                $children = $this->buildTree($elements, $element['question_id'], $level_name);
                if($children){
                    $element['questions'] = $children;
                }
                $branch[$element['question_id']] = $element;
                unset($element);
            }
        }
        return $branch;
    }

    function updateUserPagination(AddQuestionUserSettings $request){
        $userSetting = $this->userSettings->updateUserSetting(array('user_id' => Auth::user()->id,'type'=>'Question Pagination'),$request->entries);
        if($userSetting){
            return response()->json(["data" => 'suc']);
        }
    }


    /**
     * Get user History
     *
     * @return view
     */

    public function getUserHistory(){
        //        // Checking question_id_count key exist in session.
        if (Session::has('question_id_count')) {
            $userHistory=Session::get('question_id_count');
            Session::forget('question_id_count');
            return response()->json(["data" =>$userHistory ])->header('Cache-Control','no-store, no-cache, must-revalidate');

        }else{
            return response()->json(["data" => ""])->header('Cache-Control','no-store, no-cache, must-revalidate');
        }
    }


    public function saveUserQuestionSession(){
        if(isset($_POST['questionKeywords'])){

        \Log::info($_POST['questionId'].'=='.$_POST['currentPage'].'=='.$_POST['entries'].'=='.$_POST['questionName'].'=='.$_POST['status'].'=='.$_POST['display']);
        }
        $id = $_POST['questionId'];
        $current_page = $_POST['currentPage'];
        $entries = $_POST['entries'];
        $sort = $_POST['sort'];
        $sortType = $_POST['sortType'];
        (isset($_POST['questionName']))?$questionName = $_POST['questionName']:$questionName = null;
        (!empty($_POST['questionKeywords']))?$questionKeywords = implode(',',$_POST['questionKeywords']):$questionKeywords = null;
        (isset($_POST['status']))?$status =$_POST['status'] :$status = null;
        (isset($_POST['display']))?$display = $_POST['display']:$display = null;
        Session::put('question_id_count', array($id,$current_page,$entries,$questionName,$questionKeywords,$status,$display,$sort,$sortType));
        return response()->json(array('success' => 'true'));
    }


    public function questionCsv() {

        // Define headers
        $headers = ['Question Id', 'Question', 'License'];

        // Define file name
        $filename = "question-license.csv";


        $questions = $this->questionClassification->getAllActiveLicense();

        $data = [];
        foreach ($questions as $q) {

            $license_name = 'GENERAL';

            if ($q->option_value != 'GENERAL') {
                $license_name = $this->masterLicenses->getLicenseName(explode(',', $q->option_value))->name;
            }

            $data[] = [
                $q->question_id,
                $q->question,
                $license_name
            ];
        }

        // Create CSV file
        return $this->csv->create($data, $headers, $filename);
    }

    /**
     * When creating law type federal question
     * we do not need to create new classification, what we need
     * is just a copy of super parent classification data.
     * this should only happens when law type is `1` which is federal
     * @param $oldQuestionId
     * @param $newQuestionId
     */
    private function makeNewClassicationDataWhenLawTypeFederal($oldQuestionId, $newQuestionId) {

        DB::beginTransaction();
        try {
            $items = $this->questionClassification->findWhere(array('question_id' => $newQuestionId));
            foreach($items as $item){
                $this->questionClassification->delete($item->id);
            }
            $this->createClassificationVersion($oldQuestionId, $newQuestionId);
            // commit transaction
            DB::commit();
        }catch(Exception $e) {
            DB::rollback();
        }

    }
    /**
     * When updating law type parent federal question
     * we update all its all child questions
     * is just a copy of super parent classification data.
     * @param $superParentId
     */
    private function makeNewClassicationDataForAllChildernWhenLawTypeFederal($superParentId) {

        try {
            $items = $this->questionClassification->getChildQuestions($superParentId);
            $childToDelete=array();
            foreach($items as $item){
                array_push($childToDelete,$item->id);
            }
            $this->questionClassification->deleteAllChildQuestionClassifications($childToDelete);
            $this->createClassificationVersionForChild($superParentId);
            // commit transaction
        }catch(Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

    }

    /**
     *  Create Child Question Classification Version
     * @param $oldQuestionId
     * @param $newQuestionId
     * @return array
     */
    private function createClassificationVersionForChild($superParentId){

        $childQuestionsInClassifications=$this->questionClassification->getChildQuestions($superParentId,true);
        $superParentClassifications = $this->questionClassification->getSuperParentClassifications($superParentId);

        foreach ($childQuestionsInClassifications as $childQuestionsInClassification) {
                foreach($superParentClassifications as $superParentClassification){

                    $data = array(
                        'question_id' => $childQuestionsInClassification->question_id,
                        'entity_tag' => $superParentClassification->entity_tag,
                        'option_value' => $superParentClassification->option_value,
                        "created_by" => Auth::user()->id,
                        "updated_by" => Auth::user()->id
                    );
                    $this->questionClassification->create($data);
                }
        }

    }

    /**
     * Delete sub question
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteSubQuestion(){
        $questionId = $_POST['questionId'];
        $question_data = array('is_deleted' => 1);

        \Log::info('====== ===== ======');
        // Start DB transaction
        DB::beginTransaction();

        try{
            $allSubQuestions = $this->question->getChildQuestionCountByParent($questionId);

            if ($allSubQuestions) {
                return response()->json(array('success' => 'false', 'message'=> 'Please delete child question first'));
            }

            $response = $this->question->update($question_data, $questionId,"id");
            \Log::info($response);
            if($response){

                $message =  Config::get('messages.QUESTION_DELETE_SUCCESS');

                // commit transaction
                DB::commit();
                return response()->json(array('success' => 'true', 'message'=> $message));
            }
            else{
                DB::rollback();
                $message = Config::get('messages.QUESTION_DELETE_FAILED');
                return response()->json(array('success' => 'false', 'message'=> $message));
            }
        }
        catch(\Exception $ex){
            DB::rollback();
            $message = Config::get('messages.QUESTION_DELETE_FAILED');
            return response()->json(array('success' => 'false', 'message'=> $message));
        }

    }

    // update question status
    public function updateSubQuestionStatus(){
        $questionId = $_POST['questionId'];
        $status= $_POST['status'];
        $question_data = array('status' => $status);

        \Log::debug(" change status value " . $status);
        \Log::debug(" POST " . print_r($_POST, true));
        if ($status == 0) { // check if it going to in-activate question, then check sub question status first
            $allSubQuestions = $this->question->getChildQuestionCountByParent($questionId, true);
            \Log::debug(" all question count " . $allSubQuestions);
            if ($allSubQuestions > 0) {
                return response()->json(array('success' => 'false', 'message'=> 'Please change status of the child question first'));
            }
        }

        $response = $this->question->update($question_data, $questionId);

        if($response) {
            $message =  Config::get('messages.QUESTION_STATUS_UPDATE_SUCCESS');
            return response()->json(array('success' => 'true', 'message'=> $message));
        } else {
            $message = Config::get('messages.QUESTION_STATES_UPDATE_FAILED');
            return response()->json(array('success' => 'false', 'message'=> $message));
        }
    }

    /**
     * Show the form for create child question view
     *
     */

    public function createChild($parent_id, $answer_id){
        $breadcrumbValues = $this->makeParentLevelTree($parent_id);
        return view('question.createChild')->with(array('page_title' => 'Create Child Question', 'visibility'=>1, 'create' => true, 'levelTree' => array_reverse($breadcrumbValues)));
    }

    /**
     * Show the form for edit child question view
     * @param $question_id
     * @param int $visibility
     * @return
     */
    public function editAndViewChild($question_id, $visibility=0){
        $parent=$this->question->find($question_id,array('parent_question_id', 'is_draft'));
        $is_draft = ($parent->is_draft == 1)? true : false;
        $breadcrumbValues = $this->makeParentLevelTree($question_id);
        $pageTitle = 'View Child Question';
        if ($visibility == 1) {
            $pageTitle = 'Edit Child Question';
        }
        return view('question.createChild')->with(array('page_title' => $pageTitle, 'question_id'=>$question_id,'parent_id'=>$parent->parent_question_id,'visibility'=>$visibility, 'is_draft' => $is_draft, 'levelTree' => array_reverse($breadcrumbValues)));
    }


    /**
     * Get parent question details by id for child question view
     * @param $id
     * @return array values
     */

    public function getParentDetails(){
        $parent_id = $_GET['parent_id'];
        if (isset($parent_id)){
            $parentQuestion = $this->question->find($parent_id, array('law', 'supper_parent_question_id'));
            $parentLaw = $parentQuestion;
            $parentQuestion->supper_parent_question_id;
            $parent_audit_types = $this->questionClassification->findWhere(array('question_id' =>$parent_id , 'entity_tag' => 'AUDIT_TYPE'))->all();
            $auditTypes = $this->auditType->all(array('id', 'name'));
            $temp = array();
            foreach($parent_audit_types as $parent_audit_type)
            {
                foreach($auditTypes as $audit_type)
                {
                    if($audit_type->id == $parent_audit_type['option_value'])
                    {
                        array_push($temp,$audit_type);
                    }
                }
            }
            $auditTypes = $temp;

            $country = $this->questionClassification->findWhere(array('question_id' => $parent_id, 'entity_tag' => 'COUNTRY'))->first();
            $countries = $this->country->all(array('id', 'name'));
            $parentCountry=array();
            array_push($parentCountry,$country->option_value);
            {
                $temp = array();
                foreach($countries as $tmp)
                {
                    if(in_array($tmp->id,$parentCountry))
                    {
                        array_push($temp,$tmp);
                    }
                }
                $countries = $temp;
            }




            $state = $this->questionClassification->findWhere(array('question_id' => $parent_id, 'entity_tag' => 'STATE'))->first();
            $states = $this->state->findWhere(array('country_id' => $country->option_value, 'status' => true), array('id', 'name', 'country_id'))->all();
            $parentState=array();

            array_push($parentState,$state->option_value);
            {
                $temp = array();
                foreach($states as $tmp)
                {
                    if(in_array($tmp->id,$parentState))
                    {
                        array_push($temp,$tmp);
                    }
                }
                //Only when law is not Federal
                if($parentLaw->law!=1){

                    $states = $temp;
                }
            }


            $cities = $this->city->findWhere(array('status_id' => $state->option_value, 'status' => true), array('id', 'name'))->all();
            $parent_cities = $this->questionClassification->findWhere(array('question_id' => $parent_id, 'entity_tag' => 'CITY'))->all();

            {
                $temp = array();
                foreach($parent_cities as $parent_city)
                {
                    if ($parent_city != 'ALL') {
                        foreach($cities as $ct)
                        {
                            if($ct->id == $parent_city['option_value'])
                            {
                                array_push($temp,$ct);
                            }
                        }
                    }else {

                    }
                }
                if ($parentLaw->law != 2) {
                    $cities = $temp;
                }
            }

            $licences = $this->questionClassification->findWhere(array('question_id' => $parent_id, 'entity_tag' => 'LICENCE'))->all();
            $masterLicenses = $this->masterLicenses->findWhere(array('master_states_id' => $state->option_value), array('id', 'name', 'master_states_id', 'status', 'type'))->all();
            {
                $temp = array();
                foreach($licences as $licence)
                {
                    $licen_ids = explode(',', (string)$licence['option_value']);
                    foreach($masterLicenses as $ml)
                    {
                        if(in_array($ml->id,$licen_ids))
                        {
                            array_push($temp,$ml);
                        }
                    }
                }
                $temp = $this->unique_me($temp);
                if ($parentLaw->law != 1) {
                    $masterLicenses = $temp;
                }


            }

            $parentCategory=$this->questionClassification->findWhere(array('question_id' => $parent_id, 'entity_tag' => '1'))->first();


            $parentOtherClassifications=$this->questionClassification->getParentQustionOtherClassifications($parent_id);
            $otherClassifications=array();
            foreach ($parentOtherClassifications as $parentClassification){
                $data=array(
                    'id'=>$parentClassification->entity_tag,
                    'name'=>$parentClassification->name
                    );
                $parentClassificaionOptions=$this->questionClassification->getParentQustionOtherClassificationOptions($parent_id,$parentClassification->entity_tag);
                foreach ($parentClassificaionOptions as $parentClassificaionOption){
                    $data_options=array(
                        'id'=>$parentClassificaionOption->option_value,
                        'name'=>$parentClassificaionOption->name
                    );

                $data['option_value'][]=$data_options;
                }
                $otherClassifications[]=$data;
            }

            $parentOtherClassificationsNotRequired=$this->questionClassification->getParentQustionOtherClassificationsNotRequired($parent_id);
            $otherClassificationsNotRequired=array();
            foreach ($parentOtherClassificationsNotRequired as $parentOtherClassificationNotRequired){
                $dataNotRequired=array(
                    'id'=>$parentOtherClassificationNotRequired->entity_tag,
                    'name'=>$parentOtherClassificationNotRequired->name
                );
                $parentClassificaionOptions=$this->questionClassification->getParentQustionOtherClassificationOptions($parent_id,$parentOtherClassificationNotRequired->entity_tag);
                foreach ($parentClassificaionOptions as $parentClassificaionOption){
                    $data_options_not_required=array(
                        'id'=>$parentClassificaionOption->option_value,
                        'name'=>$parentClassificaionOption->name
                    );

                    $dataNotRequired['option_value'][]=$data_options_not_required;
                }
                $otherClassificationsNotRequired[]=$dataNotRequired;
            }

            $masterAnswers = $this->masterAnswer->findWhere(array('status' => '1'), array('id', 'name'))->all();


            $masterAnswerValue = $this->answerValue->findWhere(array('status' => '1'), array('id', 'name'))->all();

            return response()->json(array('success' => 'true','parentLawType' => $parentLaw->law,'countries'=>$countries,'states'=>$states,'cities'=>$cities,'masterLicenses'=>$masterLicenses,'parentCategory'=>$parentCategory->option_value,'auditTypes'=>$auditTypes,'otherClassifications'=>$otherClassifications,'otherClassificationsNotRequired'=>$otherClassificationsNotRequired, 'superParentQuestionId' => $parentQuestion->supper_parent_question_id, 'masterAnswerValue' => $masterAnswerValue, 'masterAnswers' => $masterAnswers), 200);
        }else{
            return response()->json(array('success' => 'true','parentLawType' =>'' ), 200);
        }
    }

    /**
     * Get parent question details by id for child question view
     * @param $id
     * @return array values
     */

    public function getchildDetailsForEditView(){
        $question_id = $_GET['question_id'];
        if (isset($question_id)){
            $childQuestion = $this->question->find($question_id, array('law','question','explanation','parent_question_id' ,'supper_parent_question_id','question_answer_id'));
            $law = $childQuestion;
//            $parentQuestion->supper_parent_question_id;
            $parent_audit_types = $this->questionClassification->findWhere(array('question_id' =>$question_id , 'entity_tag' => 'AUDIT_TYPE'))->all();
            $auditTypes = $this->auditType->all(array('id', 'name'));
            $actionItems = $this->actionItem->findWhere(array('question_id' => $question_id))->all();
            $citations_saved = $this->citation->getCitations($question_id);
            $temp = array();
            foreach($parent_audit_types as $parent_audit_type)
            {
                foreach($auditTypes as $audit_type)
                {
                    if($audit_type->id == $parent_audit_type['option_value'])
                    {
                        array_push($temp,$audit_type);
                    }
                }
            }
            $auditTypes = $temp;

            $country = $this->questionClassification->findWhere(array('question_id' => $question_id, 'entity_tag' => 'COUNTRY'))->first();
            $countries = $this->country->all(array('id', 'name'));
            $parentCountry=array();
            array_push($parentCountry,$country->option_value);
            {
                $temp = array();
                foreach($countries as $tmp)
                {
                    if(in_array($tmp->id,$parentCountry))
                    {
                        array_push($temp,$tmp);
                    }
                }
                $countries = $temp;
            }




            $state = $this->questionClassification->findWhere(array('question_id' => $question_id, 'entity_tag' => 'STATE'))->first();
            $states = $this->state->findWhere(array('country_id' => $country->option_value, 'status' => true), array('id', 'name', 'country_id'))->all();
            $parentState=array();

            array_push($parentState,$state->option_value);
            {
                $temp = array();
                foreach($states as $tmp)
                {
                    if(in_array($tmp->id,$parentState))
                    {
                        array_push($temp,$tmp);
                    }
                }
                //Only when law is not Federal
                if($law->law!=1){

                    $states = $temp;
                }
            }


            $cities = $this->city->findWhere(array('status_id' => $state->option_value, 'status' => true), array('id', 'name'))->all();
            $parent_cities = $this->questionClassification->findWhere(array('question_id' => $question_id, 'entity_tag' => 'CITY'))->all();

            {
                $temp = array();
                foreach($parent_cities as $parent_city)
                {
                    if ($parent_city != 'ALL') {
                        foreach($cities as $ct)
                        {
                            if($ct->id == $parent_city['option_value'])
                            {
                                array_push($temp,$ct);
                            }
                        }
                    }else {

                    }
                }
                if ($law->law != 2) {
                    $cities = $temp;
                }
            }

            $licences = $this->questionClassification->findWhere(array('question_id' => $question_id, 'entity_tag' => 'LICENCE'))->all();
            $masterLicenses = $this->masterLicenses->findWhere(array('master_states_id' => $state->option_value), array('id', 'name', 'master_states_id', 'status', 'type'))->all();
            {
                $temp = array();
                foreach($licences as $licence)
                {
                    $licen_ids = explode(',', (string)$licence['option_value']);
                    foreach($masterLicenses as $ml)
                    {
                        if(in_array($ml->id,$licen_ids))
                        {
                            array_push($temp,$ml);
                        }
                    }
                }
                $temp = $this->unique_me($temp);
                if ($law->law != 1) {
                    $masterLicenses = $temp;
                }


            }

            {
                $tempSelected = array();
                $selectedLices = array();
                foreach($licences as $licence)
                {
                    $licen_ids = explode(',', (string)$licence['option_value']);
                    foreach($masterLicenses as $ml)
                    {
                        if(in_array($ml->id,$licen_ids))
                        {
                            array_push($tempSelected,$ml);
                        }
                    }
                    array_push($selectedLices,$tempSelected);
                    $tempSelected=[];
                }
            }

            $parentCategory=$this->questionClassification->findWhere(array('question_id' => $question_id, 'entity_tag' => '1'))->first();


//            $parentOtherClassifications=$this->questionClassification->getParentQustionOtherClassifications($question_id);
//            $otherClassifications=array();
//            foreach ($parentOtherClassifications as $parentClassification){
//                $data=array(
//                    'id'=>$parentClassification->entity_tag,
//                    'name'=>$parentClassification->name
//                );
//                $parentClassificaionOptions=$this->questionClassification->getParentQustionOtherClassificationOptions($question_id,$parentClassification->entity_tag);
//                foreach ($parentClassificaionOptions as $parentClassificaionOption){
//                    $data_options=array(
//                        'id'=>$parentClassificaionOption->option_value,
//                        'name'=>$parentClassificaionOption->name
//                    );
//
//                    $data['option_value'][]=$data_options;
//                }
//                $otherClassifications[]=$data;
//            }

            $childOtherClassifications=$this->questionClassification->getParentQustionOtherClassifications($question_id);

            $otherClassifications=array();
            foreach ($childOtherClassifications as $childOtherClassification){
                $data=array(
                    'id'=>$childOtherClassification->entity_tag,
                    'name'=>$childOtherClassification->name
                    );
                $childOtherClassificationOptions=$this->questionClassification->getParentQustionOtherClassificationOptions($question_id,$childOtherClassification->entity_tag);
                foreach ($childOtherClassificationOptions as $childOtherClassificationOption){
                    $data_options=array(
                        'id'=>$childOtherClassificationOption->option_value,
                        'name'=>$childOtherClassificationOption->name
                    );
                $parentClassifications=$this->questionClassification->getParentQustionOtherClassificationOptions($childQuestion->parent_question_id,$childOtherClassification->entity_tag);
                    foreach ($parentClassifications as $parentClassification){
                        $parent_data_options=array(
                            'id'=>$parentClassification->option_value,
                            'name'=>$parentClassification->name
                        );
                    $data['option_value'][]=$parent_data_options;
                    }
                    $data['selected'][]=$data_options;
                }
                $otherClassifications[]=$data;
            }



            $childOtherClassificationsNotRequired=$this->questionClassification->getParentQustionOtherClassificationsNotRequired($question_id);
            $otherClassificationsNotRequired=array();
            foreach ($childOtherClassificationsNotRequired as $childOtherClassificationNotRequired){
                $dataNotRequired=array(
                    'id'=>$childOtherClassificationNotRequired->entity_tag,
                    'name'=>$childOtherClassificationNotRequired->name
                );
                $childClassificaionOptions=$this->questionClassification->getParentQustionOtherClassificationOptions($question_id  ,$childOtherClassificationNotRequired->entity_tag);
                foreach ($childClassificaionOptions as $childClassificaionOption){
                    $data_options_not_required=array(
                        'id'=>$childClassificaionOption->option_value,
                        'name'=>$childClassificaionOption->name
                    );
                    $parentClassificationsNot=$this->questionClassification->getParentQustionOtherClassificationOptions($childQuestion->parent_question_id,$childOtherClassificationNotRequired->entity_tag);
                    foreach ($parentClassificationsNot as $parentClassificationNot){
                        $parent_data_options_not=array(
                            'id'=>$parentClassificationNot->option_value,
                            'name'=>$parentClassificationNot->name
                        );
                        $dataNotRequired['option_value'][]=$parent_data_options_not;
                    }
                    $dataNotRequired['selected'][]=$data_options_not_required;
                }
                $otherClassificationsNotRequired[]=$dataNotRequired;
            }

            $masterAnswers = $this->masterAnswer->findWhere(array('status' => '1'), array('id', 'name'))->all();
            $masterAnswersSelected=DB::select("SELECT ms.id,ms.name, T.count,T.answer_value_id FROM master_answers ms LEFT JOIN (SELECT master_answers.id as master_answer_id,master_answers.name,Count(question_answers.id) as count ,question_answers.question_id,question_answers.answer_value_id  FROM master_answers LEFT JOIN question_answers ON question_answers.answer_id = master_answers.id WHERE question_answers.question_id = $question_id or question_answers.question_id IS NULL GROUP BY master_answers.id ) T on T.master_answer_id = ms.id");
            $masterAnswerValue = $this->answerValue->findWhere(array('status' => '1'), array('id', 'name'))->all();

            return response()->json(array('success' => 'true','childLawType' => $law->law,'countries'=>$countries,'states'=>$states,'cities'=>$cities,'masterLicenses'=>$masterLicenses,'parentCategory'=>$parentCategory->option_value,'auditTypes'=>$auditTypes,'otherClassifications'=>$otherClassifications,'otherClassificationsNotRequired'=>$otherClassificationsNotRequired, 'superParentQuestionId' => $childQuestion->supper_parent_question_id, 'masterAnswerValue' => $masterAnswerValue, 'masterAnswers' => $masterAnswers,'selectedLices'=>$selectedLices,'actionItems'=>$actionItems,'question'=>$childQuestion->question,'explanation'=>$childQuestion->explanation,'citations_saved'=>$citations_saved,'masterAnswersSelected'=>$masterAnswersSelected,'questionAnswerId'=>$childQuestion->question_answer_id), 200);
        }else{
            return response()->json(array('success' => 'true','parentLawType' =>'' ), 200);
        }
    }




    /**
     * Make tree level parent level questions
     * @param $parentQuestionId
     * @param array $treeValues
     * @internal param $answerId
     * @return array
     */
    public function makeParentLevelTree($parentQuestionId, &$treeValues = array())
    {
        if ($parentQuestionId == 0) {
            return $treeValues;
        } else {
            $question = $this->question->findBy('id', $parentQuestionId);
            $treeValues[] = array(
                'id' => $question->id,
                'question' => $question->question,
                'is_root' => ($question->parent_question_id == 0) ? 'yes' : 'no'
            );
            $this->makeParentLevelTree($question->parent_question_id, $treeValues);
            return $treeValues;
        }
    }

    public function getAllChildQuestionAjax() {
        $answerId = \Input::get('answerId');
        $questionIndexPrefix = \Input::get('questionIndex');
        $viewOnly = \Input::get('viewOnly');

        if (!isset($questionIndexPrefix) || empty($questionIndexPrefix)) {
            $questionIndexPrefix = '1';
        }

        $allQuestionsByAnswer = $this->question->getQuestionsBasedOnAnswer($answerId);
        $masterAnswers = $this->masterAnswer->findWhere(array('status' => '1'))->all();
        $masterAnswerValue = $this->answerValue->findWhere(array('status' => '1'))->all();

        $questionAnswer = $this->questionAnswer->findWhere(array('id' => $answerId))->first();
        $parentQuestionId = $questionAnswer->question_id;

        if (count($allQuestionsByAnswer)) {
            $c = 1;
            foreach ($allQuestionsByAnswer as &$q) {
                $questionAnswers = $this->questionAnswer->findWhere(array('question_id' => $q->id, 'is_deleted' => false))->all();
                $q->questionAnswers = $questionAnswers;
                $q->indexValue = $questionIndexPrefix . '.'.$c++;
            }
        }

        $html = View::make('question.ChildQuestions', array(
                'questions' => $allQuestionsByAnswer,
                'masterAnswers' => $masterAnswers,
                'masterAnswerValue' => $masterAnswerValue,
                'questionAnswerId' => $answerId,
                'parentQuestionId' => $parentQuestionId,
                'viewOnly' => $viewOnly,
            )
        );
        return Response()->json(array('success' => 'true', 'view' => $html->render(), 'parentQuestionId' => $parentQuestionId),200);
    }
}


