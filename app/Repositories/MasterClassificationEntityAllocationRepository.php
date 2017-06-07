<?php
/**
 * Created by PhpStorm.
 * User: Harsha
 * Date: 5/26/2016
 * Time: 10:34 AM
 */

namespace App\Repositories;

use Bosnadev\Repositories\Eloquent\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class MasterClassificationEntityAllocationRepository extends Repository
{
    public function model()
    {
        return 'App\Models\MasterClassificationEntityAllocation';
    }
}