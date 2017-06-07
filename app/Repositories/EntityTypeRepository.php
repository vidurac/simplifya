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

class EntityTypeRepository extends Repository
{
    public function model()
    {
        return 'App\Models\MasterEntityType';
    }

    public function getPublicEntities()
    {
        return $this->model
                ->where('status', '=', '1')
                ->get();
    }
}