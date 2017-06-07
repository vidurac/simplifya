<?php namespace App\Repositories;

use App\Models\AppointmentClassification;
use App\Models\CompanyLocation;
use App\Models\MasterCity;
use App\Models\QuestionClassification;
use Bosnadev\Repositories\Eloquent\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class MasterCityRepository extends Repository{
    public function model()
    {
            return 'App\Models\MasterCity';
    }

    /**
     * @return mixed
     * get total number of rows in datatable
     */
    public static function getTotaleNumber()
    {
        $result = DB::select("SELECT COUNT(`id`) as count FROM `master_cities` WHERE `status` = 1");
        return $result;
    }


    public static function getFilteredTotaleNumber()
    {
        $result = DB::select("SELECT COUNT(`master_cities`.`id`) as count FROM `master_cities` JOIN `master_states` ON `master_states`.`id` = `master_cities`.`status_id` WHERE `master_cities`.`status` = 1 GROUP BY `master_states`.`id`");
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

    /**
     * Update City list
     * @param $city_withid
     * @param $city_withoutid
     * @param $state_id
     */
    public function cityListUpdate($city_withid, $city_withoutid, $state_id){
        $dataset = array();
        $master_city_list = array();
        $city_ids = "";

        if($city_withid!="" || $city_withid!=null){
            foreach ($city_withid as $item){
                $master_city = MasterCity::where('id', '=', $item['id'])->update(array('name' => $item['name']));
                $city_ids .= $item['id']. ',';
            }
            $option_ids = rtrim($city_ids, ',');
            $delete = MasterCity::where('status_id', '=', $state_id)->whereRaw("`id` NOT IN (".$option_ids.")")->update(array('status' => 0));
        }

        if($city_withoutid!="" || $city_withoutid!=null) {

            foreach ($city_withoutid as $item){

                $check = $this->model->where(array('name' => $item['name'], 'status' => 1))->get();

                if(isset($check[0])){
                    return $status = 0;
                }else{
                    $dataset = array(
                        'name' => $item['name'],
                        'status_id' => $state_id,
                        'status' => 1
                    );

                    $master_city_list = MasterCity::create($dataset);
                }
            }
        }

        if($master_city_list) {
            return $status = 1;
        }else{
            return $status = 1;
        }
    }

    public function getCityByCompanyId($company_id)
    {
         return DB::table('master_countries')
                    ->join('master_states', 'master_countries.id', '=', 'master_states.country_id')
                    ->join('master_cities', 'master_states.id', '=', 'master_cities.status_id')
                    ->where('master_countries.id',$company_id)
                    ->get();
    }

    public function getCountryByCityId($city_id)
    {
        return DB::table('master_cities')
            ->select('master_countries.id as country_id', 'master_countries.name as country_name')
            ->join('master_states', 'master_cities.status_id', '=', 'master_states.id')
            ->join('master_countries', 'master_states.country_id', '=', 'master_countries.id')
            ->where('master_cities.id',$city_id)
            ->get();
    }

    public function getStateByCityId($city_id)
    {
        return DB::table('master_cities')
            ->join('master_states', 'master_cities.status_id', '=', 'master_states.id')
            ->where('master_cities.id',$city_id)
            ->get();
    }

    public function getCityByStatus($status_id, $status)
    {
        return DB::table('master_cities')
            ->where('master_cities.status_id',$status_id)
            ->where('master_cities.status',$status)
            ->orderby('name', 'asc')
            ->get();
    }

    public function getAllCitiesOrderByAcs()
    {
        return DB::table('master_cities')
            ->orderby('name', 'asc')
            ->get();
    }

    public function getAllCitiesByStatus($status)
    {
        return DB::table('master_cities')
            ->where('master_cities.status',$status)
            ->orderby('name', 'asc')
            ->get();
    }
}