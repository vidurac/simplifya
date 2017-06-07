<?php
/**
 * Created by PhpStorm.
 * User: Harsha
 * Date: 5/5/2016
 * Time: 4:06 PM
 */

namespace App\Repositories;
use Bosnadev\Repositories\Contracts\RepositoryInterface;
use Bosnadev\Repositories\Eloquent\Repository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use DB;


class UserSettingsRepository extends Repository
{
    public function model() {
        return 'App\Models\UserSettings';
    }
    public function updateUserSetting(array $userData,$entries){
        $userSetting=$this->model->firstOrNew($userData);
        $userSetting->user_id=Auth::user()->id;
        $userSetting->type='Question Pagination';
        $userSetting->type_value=$entries;
        return $userSetting->save();
    }

    public function getPerPage(){
        $query =  $this->model->where('user_id', Auth::user()->id)
        ->where('type','Question Pagination');
        $result = $query->first();
       return $result;;
    }

}