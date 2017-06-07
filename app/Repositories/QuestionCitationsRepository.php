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

class QuestionCitationsRepository extends Repository
{
    /**
     * @return string
     */
    public function model()
    {
        return 'App\Models\QuestionCitation';
    }
    /**
     * citations
     * @param $question_id
     * @return citation list
     */
    public function getCitations($question_id){

       // Get question citations
        return $this->model
            ->where('question_id', '=', $question_id)
            ->orderBy('order_id','ASC')
            ->get();
       
    }

    /**
     * citations
     * @param $question_id
     * @return citation list
     */
    public function getAllCitations(){

        // Get question citations
        return $this->model
            ->orderBy('citation','ASC')
            ->get();

    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function createCitationVersion($old_question_id, $new_question_id)
    {
        $old_citations = $this->getCitations($old_question_id);
        //\Log::info('================ kkkkkkkkkkkkkkkk================'.print_r($old_question_id." ".$new_question_id,true));

        if(count($old_citations) > 0)
        {
            foreach($old_citations as $old_citation)
            {
                if(!empty($old_citation->citation))
                {
                    $citation_data = array(
                        'question_id' => $new_question_id,
                        'citation' => $old_citation->citation,
                        'description' => $old_citation->description,
                        'link' => $old_citation->link,
                        'order_id' => $old_citation->order_id
                    );
                    $this->create($citation_data);
                }
                //\Log::info('================ cccccccccccccccccc================'.json_encode($old_citation,true));
            }
        }
        //die;
    }

    public function updateCitation($datas)
    {
        $existing_ids =  $this->model
                        ->where('question_id', '=', $datas[0]['question_id'])
                        ->select('id')
                        ->get()
                        ->toArray();

        $this->model->where('question_id', '=', $datas[0]['question_id'])->delete();
        //\Log::info('================ START PARENT================');

        foreach($datas as $current_citation)
        {
            unset($current_citation['id']);
            if(!empty($current_citation['citation']))
            {
                $this->model->create($current_citation);
            }
            /*$record_exist = false;
            foreach($existing_ids as $old_id)
            {
                if($old_id['id'] == $current_citation['id'])
                {
                    $this->model->where('id', '=', $current_citation['id'])->update($current_citation);
                    $record_exist = true;
                    break;
                }
                //\Log::info('================ START PARENT================'.print_r($current_citation['id'],true));
            }
            if($record_exist == false)
            {
                $this->model->where('id', '=', $current_citation['id'])->delete();
            }*/
        }
        //return $this->model->where('id', '=', $data)->update($data);
    }

}