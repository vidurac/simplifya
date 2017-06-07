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

class RequestsRepository extends Repository
{
    /**
     * @return string
     */
    public function model()
    {
        return 'App\Models\request';
    }

    /**
     * get all requests from company id
     * @param $cId
     * @return $notifications
     */
    public function getRequests ($cId)
    {
    	$notifications = $this->model->where('to_company_id', '=', $cId)
    								 ->where('status', '=', 0)
    								 ->select(DB::raw('(SELECT name FROM companies WHERE id = requests.from_company_id) as name'), 'id')
    								 ->get(); 								 
    	return $notifications;								 
    }

    /**
     * get total number of requests
     * @return mixed
     */
    public static function getTotalNumber()
    {
        $result = DB::select("SELECT COUNT(`id`) as count FROM `requests`");
        return $result;
    }

    /**
     * get row count
     * @return $result
     */
    public static function currentRow()
    {
        $result = DB::select('SELECT FOUND_ROWS() as FilteredTotal');
        return $result;
    }

    /**
     * get page filtered total item count
     *
     * @param $companyId
     * @return mixed
     */
    public static function getFilteredTotalNumber($companyId)
    {
        $result = DB::select("SELECT COUNT(`id`) as count FROM `requests`".$companyId);
        return $result;
    }

    public function getUserLocations($user_id)
    {
        $location = [];
        $results = DB::table('company_users')
            ->select('company_locations.id')
            ->join('company_locations', 'company_locations.id', '=', 'company_users.location_id')
            ->where('company_users.user_id', $user_id)
            ->get();
        foreach ($results as $result) {
            $location[] = $result->id;
        }
        return $location;
    }
}