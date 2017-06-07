<?php namespace App\Repositories;

use Bosnadev\Repositories\Eloquent\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class MasterClasificationRepository extends Repository{


    public function model()
    {
        return 'App\Models\MasterClassification';
    }

    /**
     * Get classification
     * @param $classification_id
     */
    public function getClassificationById($classification_id,$parent_id)
    {
        return $result = $this->model->with(array('masterClassificationAllocations'))
                        ->with([
                            'masterClassificationOptions' => function ($query) use($parent_id){
                                $query->where('master_classification_options.status', '=', '1');
                                $query->where('master_classification_options.parent_id', '=', $parent_id);
                            }
                        ])
                        ->where('id', $classification_id)
                        ->get();
    }

    // find Main Category
    public function findMainCategory(){
        $result = $this->model->where('is_main', '=', 1)->where('status', '=', 1)->get();
        return $result;
    }

    // find required classifications
    public function findClassifications($type){

        switch($type){
            case 0:
                $result = $this->model->with(array('masterClassificationOptions'=>function($q){
                    $q->orderBy('name', 'asc');
                    $q->where('status',1);
                }))->where('is_main', '=', 1)->where('status', '=', 1)->get();
                
                return $result;
                break;
            case 1:
                $reult = $this->model->with(array('masterClassificationOptions'))->where('is_main', '=', 0)
                    ->where('is_required', '=', 1)
                    //->where('status', '=', 1)
                    ->get();

                return $reult;
                break;
            case 2:
                $reult = $this->model->with(array('masterClassificationOptions'))->where('is_main', '=', 0)
                    ->where('is_required', '=', 0)
                    //->where('status', '=', 1)
                    ->get();
                return $reult;
                break;
            case 3:
                $reult = $this->model->with(array('masterClassificationOptions'))->where('is_main', '=', 0)
                    ->where('status', '=', 1)->get();
                return $reult;
                break;
        }
    }

    public function remove_question_category($id,$state)
    {
        if($state==1) {
            return $this->model
                ->where('id', $id)
                ->update(array('status' => 0));
        }elseif($state==0) {
            return $this->model
                ->where('id', $id)
                ->update(array('status' => 1));
        }
    }

    public static function getTotaleNumber()
    {
        $result = DB::select("SELECT COUNT(`id`) as count FROM `master_classifications`");
        return $result;
    }

    public static function currentRow()
    {
        $result = DB::select('SELECT FOUND_ROWS() as FilteredTotal');
        return $result;
    }

//    public function getAllCategories()
//    {
//        $result = DB::select("SELECT * FROM `master_classification_options` where status='1' and classification_id='1' ORDER BY name ASC ");
//        return $result;
//    }

    public function getAllCategories($id='')
    {
        $saved_categories = DB::select("select option_value from question_classifications where question_id='".$id."' and (entity_tag='1' or entity_tag='SUB_CATEGORY')");
        $del_ids = "";
        foreach($saved_categories as $saved_category)
        {
            $del_ids .= $saved_category->option_value.",";
        }
        $del_ids = substr($del_ids, 0, -1);
        $array=array_map('intval', explode(',', $del_ids));
        $array = implode("','",$array);
        //\Log::info("=============&&&&&&&&&&&&&&&&&&&&&&&&....===============".$del_ids);

        if(count($array) > 0)
        {
            $result = DB::select("SELECT * FROM `master_classification_options` where status='1' and classification_id='1' or id IN ('".$array."')  ORDER BY name ASC ");
        }
        else
        {
            $result = DB::select("SELECT * FROM `master_classification_options` where status='1' and classification_id='1'  ORDER BY name ASC ");
        }
        return $result;
    }


}