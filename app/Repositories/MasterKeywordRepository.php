<?php namespace App\Repositories;

use Bosnadev\Repositories\Eloquent\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class MasterKeywordRepository extends Repository{
    public function model()
    {
        return 'App\Models\MasterKeyword';
    }

    /**
     * Returns referrer specific payment details
     * @param $id
     * @return array
     * @throws \Exception
     */
    public function getAllKeyword() {
        $result = $this->model->All();
        return $result;
    }

    public function deleteKeyword($keywordId)
    {
        $keyword = $this->model->find($keywordId);
        $result = $keyword->delete();
        return $result;
    }

    public function updateKeyword($name, $id)
    {
        return $this->model->where('id', $id)
                            ->update(['name' => $name]);
    }

    public function getAllKeywords() {
        $result = DB::table('master_keywords')
                    ->orderBy('name', 'ASC')
                    ->groupBy('name')
                    ->get();
        return $result;
    }
}