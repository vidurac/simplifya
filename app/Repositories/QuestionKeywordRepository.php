<?php namespace App\Repositories;

use Bosnadev\Repositories\Eloquent\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class QuestionKeywordRepository extends Repository{
    public function model()
    {
        return 'App\Models\QuestionKeyword';
    }

    public function questionKeywordSearch($questionIds, $keyWords){
        $questionKeywords = $this->model->whereIn('question_id', $questionIds)->whereIn('keyword_id', $keyWords)->get();
        return $questionKeywords;
    }
}