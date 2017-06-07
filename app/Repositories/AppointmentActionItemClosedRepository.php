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

class AppointmentActionItemClosedRepository extends Repository
{
    /**
     * @return string
     */
    public function model()
    {
        return 'App\Models\AppointmentActionItemClosed';
    }

    public function createDeletedActionItem($dataset)
    {
        return $this->model->create($dataset);
    }
    public function isRowExist($appointment_id,$action_item_id)
    {
        return $this->model
            ->select('id')
            ->where('appointment_id','=',$appointment_id)
            ->where('action_item_id','=',$action_item_id)
            ->get()->count();
    }

    public function deleteDeletedActionItem($action_item_id,$appointment_id)
    {
        return $this->model
            ->where('appointment_id','=',$appointment_id)
            ->where('action_item_id','=',$action_item_id)
            ->delete();
    }

}