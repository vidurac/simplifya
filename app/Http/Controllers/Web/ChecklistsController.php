<?php

namespace App\Http\Controllers\Web;

use App\Repositories\MasterAuditTypeRepository;
use App\Repositories\MasterCityRepository;
use App\Repositories\MasterClasificationRepository;
use App\Repositories\MasterCountryRepository;
use App\Repositories\MasterLicenseRepository;
use App\Repositories\MasterStateRepository;
use App\Repositories\QuestionClassificationRepository;
use App\Repositories\QuestionRepository;
use App\Repositories\UsersRepository;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;


class ChecklistsController extends Controller
{

    private $question, $auditType, $country, $classification, $state, $city, $licence, $questionClassification, $user;

    /**
     * ChecklistsController constructor.
     * @param QuestionRepository $question
     * @param MasterAuditTypeRepository $auditType
     * @param MasterCountryRepository $country
     * @param MasterClasificationRepository $classification
     * @param MasterStateRepository $state
     * @param MasterCityRepository $city
     * @param MasterLicenseRepository $licence
     * @param QuestionClassificationRepository $questionClassification
     * @param UsersRepository $user
     */
    public function __construct(
        QuestionRepository $question,
        MasterAuditTypeRepository $auditType,
        MasterCountryRepository $country,
        MasterClasificationRepository $classification,
        MasterStateRepository $state,
        MasterCityRepository $city,
        MasterLicenseRepository $licence,
        QuestionClassificationRepository $questionClassification,
        UsersRepository $user
    ) {
        $this->question = $question;
        $this->auditType = $auditType;
        $this->country = $country;
        $this->classification = $classification;
        $this->state = $state;
        $this->city = $city;
        $this->licence = $licence;
        $this->questionClassification = $questionClassification;
        $this->user = $user;;

    }


    /**
     * Display checklist search and question grid.
     *
     * @return view
     */
    public function index()
    {

        if (Auth::user()->master_user_group_id == Config::get('simplifya.MasterAdmin')) {
            $auditTypes = $this->auditType->all(array('*'));
            $countries = $this->country->all(array('*'));
            $mainCategoryOptions = $this->classification->findClassifications(0);
            $classifications = $this->classification->findClassifications(3);
            $formated_categories = $this->getFormatCategories();
            $mainCategoryOptions[0]->masterClassificationOptions = $formated_categories;
            return view('checklist.index')->with(array(
                'page_title' => 'Checklist Manager',
                'auditTypes' => $auditTypes,
                'countries' => $countries,
                'mainCategoryOptions' => $mainCategoryOptions,
                'classifications' => $classifications
            ));
        } else {
            $message = Config::get('messages.ACCESS_DENIED');
            return Redirect::to("/dashboard")->with('error', $message);
        }
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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    /**
     *  Get states based on country or get all states.
     *
     * @return json
     */
    public function getStates()
    {
        $country_id = $_GET['countryId'];
        if ($country_id != 0) {
            $states = $this->country->with(array('masterStates'))->find($country_id, array('*'));
        } else {
            $states = $this->state->all(array("*"));
        }
        return Response()->json(array('success' => 'true', 'data' => $states), 200);
    }

    /**
     *  Get cities based on country or get all cities.
     *
     * @return json
     */
    public function getCities()
    {
        $state_id = $_GET['stateId'];
        if ($state_id != 0) {
            //$cities = $this->state->with(array('masterCity'))->find($state_id, array('*'));
            $cities = $this->state
                ->with([
                    'masterCity' => function ($query){
                        $query->orderBy('name', 'ASC');
                    }
                ])->find($state_id, array('*'));
        } else {
            //$cities = $this->city->all(array("*"));
            $cities = $this->city->getAllCitiesOrderByAcs();
        }


        return Response()->json(array('success' => 'true', 'data' => $cities), 200);
    }

    /**
     *  Get licences based on state or get all licences.
     *
     * @return json
     */
    public function getLicences()
    {
        $state_id = $_GET['stateId'];
        if ($state_id != 0) {
            $licences = $this->state->with([
                'masterLicense' => function ($query) {
                    $query->where('status', '=', 1);
                }
            ])->find($state_id, array('*'));
        } else {
            $licences = $this->licence->all(array("*"));
        }

        return Response()->json(array('success' => 'true', 'data' => $licences), 200);
    }


    /**
     * Search Questions based on filter.
     *      *
     * @return array
     */
    public function searchQuestions()
    {
        //declare and initialize variables
        $dataset = array();

        $auditType = $_GET['auditType'];
        $country = $_GET['country'];
        $state = $_GET['state'];
        $city = $_GET['city'];
        $mainCategory = $_GET['mainCategory'];
        $mainCatId = $_GET['mainCatId'];
        $classifications = $_GET['classifications'];
        $license = $_GET['license_type'];
        $city_only = isset($_GET['city_only'])? $_GET['city_only'] : false;


        if ($auditType != "") {
            $dataset["AUDIT_TYPE"] = $auditType;
        }

        if ($country != 0) {
            $dataset["COUNTRY"] = $country;
        }

        if ($state != 0) {
            $dataset["STATE"] = $state;
        }

        if ($city != 0) {
            $dataset["CITY"] = $city;
        }

        if ($mainCategory != "") {
            $dataset[$mainCatId] = $mainCategory;
        }


        if ($classifications != 0) {
            foreach ($classifications as $classification) {
                $dataset[$classification["classificationId"]] = $classification["value"];
            }
        }


        $license_type = array();

        if ($license) {
            foreach ($license as $value) {
                array_push($license_type, $value);
            }
        }

        $getList = $this->questionClassification->getAllQuestionsList($dataset, $license_type);
        $questions = $this->question->findQuestionChecklistArra($getList,$city_only,$city);
        //\Log::info("=============questions....===============".print_r($questions,true));

        $data = array();
        foreach ($questions as $question) {
            $user = $this->user->find($question['created_by'], array("*"));
            $data[] = array(
               //question['id'],
                strip_tags($question['question']),
                $user->name,
                date('m/d/Y g:i a', strtotime(str_replace('/', '-', $question['created_at']))),
                $row[] = "<a class='btn btn-info btn-circle' data-toggle='tooltip' title='View' data-question_id='" . $question['id'] . "' onclick='viewCheckListQuestion({$question['id']})'><i class='fa fa-paste'></i></a>"
            );
        }
        return response()->json(["data" => $data]);

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
}
