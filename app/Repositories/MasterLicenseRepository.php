<?php namespace App\Repositories;

use App\Models\MasterLicense;
use Bosnadev\Repositories\Eloquent\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class MasterLicenseRepository extends Repository{
    public function model()
    {
        return 'App\Models\MasterLicense';
    }

    public function getLicenseByStatesId($state_id)
    {
        return $this->model
                ->where('status',1)
                ->whereIn('master_states_id',$state_id)
                ->get();
    }

    public function getLicenseName(array $ids) {

        if (count($ids) == 1) {
            return $this->model
                ->select('name')
                ->where('status',1)
                ->where('id', $ids[0])
                ->first();
        }else {
            return $this->model
                ->select(DB::Raw('GROUP_CONCAT(name) as name'))
                ->where('status',1)
                ->whereIn('id', $ids)
                ->first();
        }
    }

    /**
     * Store master applicability ids to master licenses (M-to-M)
     * @param $id
     * @param array $ids
     */
    public function saveMasterApplicabilitiesForLicense($id, array $ids) {
        $obj = $this->find($id);
        $obj->applicabilities()->sync($ids);
    }

    /**
     * get master applicability ids for master licenses (M-to-M)
     * @param $id
     * @param array $ids
     */
    public function getMasterApplicabilitiesForLicense($id) {
        $obj = $this->find($id);
        return $obj->applicabilities()->get()->pluck('pivot.master_applicability_id')->toArray();
    }
}