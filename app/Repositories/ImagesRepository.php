<?php
/**
 * Created by PhpStorm.
 * User: Nishan
 * Date: 5/6/2016
 * Time: 3:38 PM
 */

namespace App\Repositories;

use Bosnadev\Repositories\Eloquent\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class ImagesRepository extends Repository
{
    public function model()
    {
        return 'App\Models\Image';
    }
    
    public function findImageByEntity($dataset){
        $result = $this->model->where('entity_id' , $dataset['entity_id'])
                            ->where('is_deleted', $dataset['is_deleted'])
                            ->where('type', $dataset['type'])
                            ->get();
        return $result;
    }
}