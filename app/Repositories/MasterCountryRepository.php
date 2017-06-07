<?php namespace App\Repositories;

use Bosnadev\Repositories\Eloquent\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class MasterCountryRepository extends Repository{
    public function model()
    {
        return 'App\Models\MasterCountry';
    }
    
    /**
     * @param $dataset
     * insert new country
     */
    public function insertCountry($dataset)
    {
        return $this->model->create($dataset);
    }

    /**
     * @param $sql
     * @return mixed
     * get all countries from database
     */
    public function getAllCountries($sql)
    {
        $result = DB::select($sql);
        return $result;
    }

    /**
     * @param $sql
     * @return mixed
     * get all countries from database
     */
    public function getAllCountryList()
    {
        $result = $this->model->All();
        return $result;
    }

    /**
     * @return mixed
     * getr total number of rows in datatable
     */
    public static function getTotaleNumber()
    {
        $result = DB::select("SELECT COUNT(`id`) as count FROM `master_countries`");
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
}