<?php namespace App\Repositories;

use Bosnadev\Repositories\Eloquent\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class MasterLicenseApplicabilitiesRepository extends Repository{
    public function model()
    {
        return 'App\Models\MasterLicenseApplicabilities';
    }





}