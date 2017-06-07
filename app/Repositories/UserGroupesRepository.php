<?php
/**
 * Created by PhpStorm.
 * User: Harsha
 * Date: 5/9/2016
 * Time: 12:26 PM
 */

namespace App\Repositories;
use Bosnadev\Repositories\Eloquent\Repository;
use Illuminate\Support\Facades\DB;

class UserGroupesRepository extends Repository
{
    /**
     * @return string
     */
    public function model() {
        return 'App\Models\MasterUserGroup';
    }

    /**
     * get user group array by entity ID
     * @param $entity_id
     * @return mixed
     */
    public function getGroupeId($entity_id)
    {
        return $result = $this->model
                    ->where('entity_type_id', '=',$entity_id)
                    ->get()
                    ->toArray();
    }

    /**
     * @param $sql
     * @return mixed
     * get all user groups from database
     */
    public function getAllUserGroups($sql)
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
        $result = DB::select("SELECT COUNT(`id`) as count FROM `master_user_groups`");
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