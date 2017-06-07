<?php namespace App\Repositories;

use Bosnadev\Repositories\Eloquent\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class QuestionClassificationRepository extends Repository{
    /**
     * @return string
     */
    public function model()
    {
        return 'App\Models\QuestionClassification';
    }

    /**Get all questions based on the question filtering criteria
     * @param $dataset
     * @param $license_type
     * @param bool $checkOnlyOnParent check child questions as well
     * @param array $parentQuestionIds check only on selected question ids (must be with $checkOnlyOnParent=false)
     * @return array
     */
    public function getAllQuestionsList($dataset, $license_type, $checkOnlyOnParent=true, $parentQuestionIds = array())
    {
        /*
         * Declare variables
         */
        $data = [];
        $end = "";
        $sWhere ="";
        $values = "";
        if ($checkOnlyOnParent) {
            $sWhere ="SELECT A.* FROM ( SELECT question_id, COUNT(question_id) as question_count, group_concat(entity_tag) as entity_tag, count(option_value) as options FROM `question_classifications` INNER JOIN questions on questions.id = question_id WHERE questions.parent_question_id = 0 AND questions.is_draft = 0 AND questions.is_archive = 0 AND questions.status = 1 AND questions.is_deleted = 0 AND ( ";
        }else {
            $sWhere ="SELECT A.* FROM ( SELECT question_id, COUNT(question_id) as question_count, group_concat(entity_tag) as entity_tag, count(option_value) as options FROM `question_classifications` WHERE ( ";
        }
        $idx = count($dataset);      //number of attributes
        $idy = count($license_type); //number of licenses
        $count = 0;
        if(count($dataset) > 0){
            foreach ($dataset as $key => $value) {
                if($key!="" && $idx != $count+1){
                    $end = ') OR (';
                }
                else{
                    $end = ')';
                }

                if ($key == 'CITY') {
                    $sWhere .= '`entity_tag` = \''.$key.'\' AND ';
                    $sWhere .= '(`option_value` = \''.$value.'\' OR `option_value` = \'ALL\') ';
                }else {
                    $sWhere .= '`entity_tag` = \''.$key.'\' AND ';
                    $sWhere .= '`option_value` = \''.$value.'\' ';
                }
                $sWhere .= $end;
                if($count == 0 ){
                    $values .= $value;
                }
                else{
                    $values .= ",".$value;
                }
                $count++;
            }
            $sWhere .= "GROUP BY question_id ) as A WHERE A.options='".$count."'";
            $result = DB::select($sWhere);
        }
        else{
            $result = $this->model->all();
        }

        // take `federal` questions
        $federalQuestions = $this->getFederalQuestions($dataset, $checkOnlyOnParent);

        $federalQuestionsIds = array();
        foreach($federalQuestions as $fq){
            $federalQuestionsIds[] = $fq->question_id;
        }

        $questionIds = array();
        foreach($result as $rs){
            $questions = $this->model->where('question_id', $rs->question_id)->where('entity_tag', 'LICENCE')->get();
            foreach($questions as $question){
                if(count($license_type) > 0){
                    $licences = "";
                    foreach($license_type as $index => $item){
                        if($index == 0){
                            $licences .= $item;
                        }
                        else{
                            $licences .= ",".$item;
                        }
                        if($question->option_value == $item){
                            if(!in_array($question->question_id, $questionIds)){
                                array_push($questionIds, $question->question_id);
                            }
                            break;
                        }
                    }
                    if($question->option_value == $licences){
                        if(!in_array($question->question_id, $questionIds)){
                            array_push($questionIds, $question->question_id);
                        }
                        break;
                    }
                }
                else{
                    array_push($questionIds, $question->question_id);
                }
            }
        }

        $returnQuestionIds = array_unique(array_merge($questionIds, $federalQuestionsIds));
        return $returnQuestionIds;

    }

    /** Find custom classifications
     * @param $questionId
     * @param $type
     * @return array
     */
    public function findCustomClassifications($questionId, $type){
        if($type == 'required'){
            return $this->model->where('question_id', $questionId)
                ->join('master_classifications', 'master_classifications.id', '=', 'question_classifications.entity_tag')
                ->where('master_classifications.is_main', '!=', '1')
                ->where('master_classifications.is_required', '1')
                ->where('question_classifications.entity_tag', '!=', 'AUDIT_TYPE')
                ->where('question_classifications.entity_tag', '!=', 'COUNTRY')
                ->where('question_classifications.entity_tag', '!=', 'STATE')
                ->where('question_classifications.entity_tag', '!=', 'CITY')
                ->where('question_classifications.entity_tag', '!=', 'LICENCE')
                ->select('question_classifications.id')
                ->get();
        }
        else{
            return $this->model->where('question_id', $questionId)
                ->join('master_classifications', 'master_classifications.id', '=', 'question_classifications.entity_tag')
                ->where('master_classifications.is_main', '!=', '1')
                ->where('master_classifications.is_required', '0')
                ->where('question_classifications.entity_tag', '!=', 'AUDIT_TYPE')
                ->where('question_classifications.entity_tag', '!=', 'COUNTRY')
                ->where('question_classifications.entity_tag', '!=', 'STATE')
                ->where('question_classifications.entity_tag', '!=', 'CITY')
                ->where('question_classifications.entity_tag', '!=', 'LICENCE')
                ->select('question_classifications.id')
                ->get();
        }
    }

    /**
     * Check options already exists in the system
     * @param $option_ids
     * @return mixed
     */
    public function checkOptionsExist($option_ids)
    {
        return $result = $this->model->whereIn('option_value', $option_ids)->get();
    }

    /**
     * Check if city occupied in several tables
     * @param $city_id
     */
    public function checkCitiesOccupied($city_id)
    {
        return $this->model->where('')->get();
    }

    /**
     * Returns country id by using city or type
     * @param $type
     */
    private function getCountryIdByCityOrState($type, $id) {
        $country = null;
        if ($type == 'CITY') {
            $country = DB::table('master_countries')->join('master_states', 'master_states.country_id', '=', 'master_countries.id')->join('master_cities', 'master_cities.status_id', '=', 'master_states.id')->where('master_cities.id', $id)->select('master_countries.id')->first();
        } else {
            $country = DB::table('master_countries')->join('master_states', 'master_states.country_id', '=', 'master_countries.id')->where('master_states.id', $id)->select('master_countries.id')->first();
        }

        if (!isset($country)) throw new Exception("No country record found");

        return $country->id;
    }

    private function getFederalQuestions($dataset, $checkOnlyOnParent=true) {

        $dataset['STATE'] = isset($dataset['STATE'])? $dataset['STATE'] : 'ALL';
        $dataset['CITY'] = 'ALL';
        $dataset['LICENCE'] = 'ALL';

        if ($checkOnlyOnParent) {
            $sWhere ="SELECT A.* FROM ( SELECT question_id, COUNT(question_id) as question_count, group_concat(entity_tag) as entity_tag, count(option_value) as options FROM `question_classifications` INNER JOIN questions on questions.id = question_id WHERE questions.parent_question_id = 0 AND questions.is_draft = 0 AND questions.is_archive = 0 AND questions.status = 1 AND questions.is_deleted = 0 AND ( ";
        }else {
            $sWhere ="SELECT A.* FROM ( SELECT question_id, COUNT(question_id) as question_count, group_concat(entity_tag) as entity_tag, count(option_value) as options FROM `question_classifications` WHERE ( ";
        }
        $idx = count($dataset);      //number of attributes

        \Log::debug("==== dataset " . print_r($dataset, true));
        $count = 0;

        if(count($dataset) > 0){
            foreach ($dataset as $key => $value) {

                if($key!="" && $idx != $count+1){
                    $end = ') OR (';
                }
                else{
                    $end = ')';
                }

                $sWhere .= '`entity_tag` = \''.$key.'\' AND ';
                if (is_array($value)) {
                    $sWhere .= '`option_value` IN (' . implode(',', $value) . ')';
                }else {
                    if ($key == 'STATE' && $dataset['STATE'] != 'ALL') {
                        $sWhere .= '(`option_value` = \''.$value.'\' OR `option_value` = \'ALL\') ';
                    }else {
                        $sWhere .= '`option_value` = \''.$value.'\' ';
                    }

                }
                $sWhere .= $end;
                $count++;
            }

            $sWhere .= "GROUP BY question_id ) as A WHERE A.options='".$count."'";
            $result = DB::select($sWhere);
        }
        else{
            $result = $this->model->all();
        }

        \Log::debug("====== FEDERAL RESULT " . print_r($result, true));

        return $result;
    }

    /**
     * Returns country id by using license id
     * @param array $ids
     * @return array
     * @internal param license $id id
     * @internal param $licenseId
     */
    private function getCountryIdByLicense(array $ids) {
        $countries = null;
        $countries = \DB::table('master_countries')->select('master_countries.id')->join('master_states', 'master_states.country_id', '=', 'master_countries.id')->join('master_licenses', 'master_licenses.master_states_id', '=', 'master_states.id')->whereIn('master_licenses.id', $ids)->groupBy('master_countries.id')->select('master_countries.id')->get();

        if (!isset($countries)) throw new Exception("No country record found");
        $countryIds = array();
        foreach ($countries as $country) {
            $countryIds[] = $country->id;
        }
        if (empty($countryIds)) throw new Exception("No country record found");

        return $countryIds;
    }

    private function getGeneralFederalQuestions($dataset, $checkOnlyOnParent=true) {
        \Log::debug("===== check general federal questions ");
        $dataset['STATE'] = 'GENERAL';
        $dataset['CITY'] = 'GENERAL';
        if (isset($dataset['LICENCE'])) {
            unset($dataset['LICENCE']);
        }

        if ($checkOnlyOnParent) {
            $sWhere ="SELECT A.* FROM ( SELECT question_id, COUNT(question_id) as question_count, group_concat(entity_tag) as entity_tag, count(option_value) as options FROM `question_classifications` INNER JOIN questions on questions.id = question_id WHERE questions.parent_question_id = 0 AND questions.is_draft = 0 AND questions.is_archive = 0 AND questions.status = 1 AND questions.is_deleted = 0 AND ( ";
        }else {
            $sWhere ="SELECT A.* FROM ( SELECT question_id, COUNT(question_id) as question_count, group_concat(entity_tag) as entity_tag, count(option_value) as options FROM `question_classifications` WHERE ( ";
        }
        $idx = count($dataset);      //number of attributes

        \Log::debug("==== GF dataset " . print_r($dataset, true));
        $count = 0;

        if(count($dataset) > 0){
            foreach ($dataset as $key => $value) {

                if($key!="" && $idx != $count+1){
                    $end = ') OR (';
                }
                else{
                    $end = ')';
                }

                $sWhere .= '`entity_tag` = \''.$key.'\' AND ';
                if (is_array($value)) {
                    $sWhere .= '`option_value` IN (' . implode(',', $value) . ')';
                }else {
                    $sWhere .= '`option_value` = \''.$value.'\' ';
                }
                $sWhere .= $end;
                $count++;
            }

            $sWhere .= "GROUP BY question_id ) as A WHERE A.options='".$count."'";
            $result = DB::select($sWhere);
        }
        else{
            $result = $this->model->all();
        }

        \Log::debug("====== GENERAL FEDERAL RESULT " . print_r($result, true));

        return $result;
    }


    public function getAllActiveLicense() {

        $results = $this->model
            ->select(array('questions.question','question_classifications.question_id', 'question_classifications.option_value'))
            ->join('questions', 'questions.id', '=', 'question_classifications.question_id')
            ->where('question_classifications.entity_tag', 'LICENCE')
            ->where('questions.is_draft', 0)
            ->where('questions.is_archive', 0)
            ->where('questions.status', 1)
            ->where('questions.is_deleted', 0)->get();

        return $results;
    }

    public function getDeletedChildQuestionMapping() {
        return DB::select("SELECT qp.id as parent_question_id,qc.id as child_question_id,qp.is_deleted as is_parent_deleted,qc.is_deleted as is_child_deleted from questions AS qc INNER JOIN (select q.* from questions q where q.parent_question_id=0 and q.is_deleted=1) as qp ON qp.id = qc.parent_question_id WHERE qc.parent_question_id != 0 AND qc.is_deleted = 0 GROUP BY qc.id");
    }
    public function getStatusMismatchSubQuestionsWithParent() {
        return DB::select("SELECT qp.id as parent_question_id, qc.id as child_question_id, qp.status as is_parent_active, qc.status as is_child_active from questions AS qc INNER JOIN (select q.* from questions q where q.parent_question_id=0 and q.`status`=0) as qp ON qp.id = qc.parent_question_id WHERE qc.parent_question_id != 0 AND qc.status = 1 GROUP BY qc.id");
    }

    public function getChildQuestions($superParentId,$gruopBy=false){
        $query= $this->model
            ->select('question_classifications.question_id','question_classifications.entity_tag','question_classifications.option_value','question_classifications.id')
            ->join('questions', 'questions.id', '=', 'question_classifications.question_id')
            ->where('questions.supper_parent_question_id',$superParentId)
            ->where('questions.parent_question_id','!=',0);
            if($gruopBy){
                $query->groupBy('question_classifications.question_id');
            }else{
            $query->whereIn('question_classifications.entity_tag',array('COUNTRY','STATE','CITY','LICENCE','AUDIT_TYPE'));
            }
        $result =$query->get();
        return $result;
    }

    public function getSuperParentClassifications($superParentId){
        return $this->model
            ->select('entity_tag','option_value')
            ->where('question_id',$superParentId)
            ->whereIn('entity_tag',array('COUNTRY','STATE','CITY','LICENCE','AUDIT_TYPE'))
            ->get();
    }

    public function deleteAllChildQuestionClassifications($childToDelete){
            $this->model->whereIn('id',$childToDelete)->delete();
    }

    /**
     * Returns masterClassification by using parent_id
     * @param array $parent_id
     * @return array
     */
    public function getParentQustionOtherClassifications($parent_id){
        return $this->model->select('question_classifications.entity_tag','master_classifications.name')->join('master_classifications','question_classifications.entity_tag','=','master_classifications.id')->where('question_id',$parent_id)->where('entity_tag','!=','1')->where('entity_tag','!=','COUNTRY')->where('entity_tag','!=','STATE')->where('entity_tag','!=','CITY')->where('entity_tag','!=','LICENCE')->where('entity_tag','!=','AUDIT_TYPE')->where('master_classifications.is_main','=',0)->where('master_classifications.is_required','=',1)->groupBy('entity_tag')->get();
    }
    /**
     * Returns masterClassificationNotRequred by using parent_id
     * @param array $parent_id
     * @return array
     */
    public function getParentQustionOtherClassificationsNotRequired($parent_id){
        return $this->model->select('question_classifications.entity_tag','master_classifications.name')->join('master_classifications','question_classifications.entity_tag','=','master_classifications.id')->where('question_id',$parent_id)->where('entity_tag','!=','1')->where('entity_tag','!=','COUNTRY')->where('entity_tag','!=','STATE')->where('entity_tag','!=','CITY')->where('entity_tag','!=','LICENCE')->where('entity_tag','!=','AUDIT_TYPE')->where('master_classifications.is_main','=',0)->where('master_classifications.is_required','=',0)->groupBy('entity_tag')->get();
    }

    public function getParentQustionOtherClassificationOptions($parent_id,$parentClassification){
        return $this->model->select('question_classifications.option_value','master_classification_options.name')->join('master_classification_options','question_classifications.option_value','=','master_classification_options.id')->where('question_id',$parent_id)->where('entity_tag',$parentClassification)->get();
    }



}