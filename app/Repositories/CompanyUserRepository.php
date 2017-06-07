<?php
/**
 * Created by PhpStorm.
 * User: Harsha
 * Date: 5/17/2016
 * Time: 5:07 PM
 */

namespace App\Repositories;
use Bosnadev\Repositories\Eloquent\Repository;
use DB;

class CompanyUserRepository  extends Repository
{
    public function model()
    {
        return 'App\Models\CompanyUser';
    }

    public function insertMultipleLocations($user_locations)
    {
       return DB::table('company_users')->insert($user_locations);
    }

    public function getUserByCompanyId($company_id ) {
        return $this->model
            ->with('User','CompanyLocation','User.masterUserGroup')
            ->whereHas('User', function($q) use($company_id){
                $q->where('company_id', '=' ,$company_id);
                $q->where('is_invite', '=' , 1);
            })
            ->get();
    }

    public function deleteUserLocations($result, $user_id) {
        return $this->model
                    ->whereIn('location_id', $result)
                    ->where('user_id', $user_id)
                    ->delete();
    }

    public function getUserLocation($user_id)
    {
        return $this->model
            ->join('company_locations', 'company_locations.id','=','company_users.location_id')
            ->where('user_id', $user_id)
            ->select('company_locations.name')
            ->get();
    }
}