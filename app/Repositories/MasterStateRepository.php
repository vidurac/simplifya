<?php namespace App\Repositories;

use Bosnadev\Repositories\Eloquent\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class MasterStateRepository extends Repository{
    public function model()
    {
        return 'App\Models\MasterStates';
    }

    /**
     * @param $country_id
     * @return mixed
     * get all states list according to country id
     */
    public function getAllStatesByCountry($country_id)
    {
        return $this->model
                    ->select('master_states.id as id', 'master_states.name as name')
                    ->where('master_states.country_id', $country_id)
                    ->get();
    }

    /**
     * @param $state_id
     * @return mixed
     * get country list according to state id
     */
    public function getCountryByState($state_id)
    {
        return $this->model
            ->select('master_countries.id as country_id', 'master_countries.name as country_name')
            ->join('master_countries','master_states.country_id','=','master_countries.id')
            ->where('master_states.id', $state_id)
            ->get();
    }

    /**
     * @param $dataset
     * insert new state
     */
    public function insertState($dataset)
    {
        return $this->model->create($dataset);
    }

    public function getStateByStateId($state_id)
    {
        return $result = DB::table('master_states')
                         ->select('master_countries.name as country_name','master_countries.id as country_id','master_states.name as state_name','master_states.status as state_status')
                         ->join('master_countries','master_countries.id','=','master_states.country_id')
                         ->where('master_states.id', $state_id)
                         ->get();
    }

    /**
     * @param $sql
     * @return mixed
     * get all states from database
     */
    public function getAllStates($sql)
    {
        $result = DB::select($sql);
        return $result;
    }

    /**
     * @return mixed
     * get total number of rows in datatable
     */
    public static function getTotaleNumber()
    {
        $result = DB::select("SELECT COUNT(`id`) as count FROM `master_states`");
        return $result;
    }

    /**
     * @return mixed
     * get current row of the table
     */
    public static function  getFilteredTotaleNumber($country_id, $state_id)
    {
        $result = 0;
        if($country_id != '' && $state_id !='') {
            $result = DB::select("SELECT COUNT(`id`) as count FROM `master_states`  WHERE `country_id` = $country_id AND `id` = $state_id AND `status`=1");
        } else if($country_id != '' && $state_id =='') {
            $result = DB::select("SELECT COUNT(`id`) as count FROM `master_states`  WHERE `country_id` = $country_id");
        } else if($country_id == '' && $state_id !='') {
            $result = DB::select("SELECT COUNT(`id`) as count FROM `master_states`  WHERE `id` = $state_id AND `status`=1");
        } else {
            $result = DB::select("SELECT COUNT(`id`) as count FROM `master_states` WHERE `status`=1");
        }

        return $result;
    }

    /**
     * @return mixed
     * get current row of the table
     */
    public static function currentRow()
    {
        $result = DB::select('SELECT FOUND_ROWS() as FilteredTotal');
        return $result;
    }

    public function getCountryByStateId($state_id)
    {
        return DB::table('master_states')
            ->join('master_countries', 'master_states.country_id', '=', 'master_countries.id')
            ->where('master_states.id',$state_id)
            ->groupBy('master_countries.id')
            ->get();
    }
}