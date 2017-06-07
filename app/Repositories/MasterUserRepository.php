<?php namespace App\Repositories;

use Bosnadev\Repositories\Eloquent\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class MasterUserRepository extends Repository{

    public function model()
    {
        return 'App\Models\MasterData';
    }

    public function getActionItemsOnOffStatus(){
        return $this->model->where('name','ACTION_ITEMS_ON_OFF')->first();
    }

    public function getStatusIndicatorOnOff(){
        return $this->model->where('name','STATUS_INDICATOR_ON_OFF')->first();
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getMJBFOC($name)
    {
        return $this->model->where('name', '=', $name)->get(array('*'));
    }

}