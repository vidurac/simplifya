<?php namespace App\Repositories;

use Bosnadev\Repositories\Eloquent\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class MasterApplicabilitiesRepository extends Repository{
    public function model()
    {
        return 'App\Models\MasterApplicabilities';
    }

    /**
     * Save or edit applicabilities
     * @param array $data
     * @throws \Exception
     */
    public function saveOrEdit(array $data) {
        try {

            \DB::beginTransaction();
            $r =$this->model->firstOrNew(array('id'=>$data['id']));
            $r->name = $data['name'];
            $r->type = $data['type'];
            $r->country_id = 1;
            $r->group_id = $data['group'];
            $r->status = 1;

            // Save applicability data
            $r->save();


            \DB::commit();
        }catch (Exception $e) {
            \DB::rollback();
            throw new Exception($e->getMessage(), $e->getCode());
        }

    }

    public function allApplicabilities(){
        return $this->model->join('master_countries','master_applicabilities.country_id','=','master_countries.id')
            ->select('master_applicabilities.id','master_applicabilities.name','master_countries.name as country','master_applicabilities.type','master_applicabilities.status','master_applicabilities.group_id','master_applicabilities.created_at')
            ->orderBy('master_applicabilities.created_at', 'DESC')
            ->get();
    }

    public function getApplicabilitesWith($where, $or=false){
        $query = $this->model->join('master_countries','master_applicabilities.country_id','=','master_countries.id')
            ->select(
                'master_applicabilities.id',
                'master_applicabilities.name',
                'master_countries.name as country',
                'master_applicabilities.type',
                'master_applicabilities.status',
                'master_applicabilities.group_id',
                'master_applicabilities.created_at'
            );
        foreach ($where as $field => $value) {
                $query = (!$or)
                    ? $query->where($field, '=', $value)
                    : $query->orWhere($field, '=', $value);
        }
        $query->where('master_applicabilities.status', 1);
        return $query->get();

    }

    public function getApplicabilityById($id){
        return $this->model
            ->where('id', $id)
            ->first();

    }

    public function changeApplicabilityStatus($id,$status)
    {
        return $this->model
            ->where('id', $id)
            ->update(array('status' => $status));
    }

    public function deleteApplicability($id)
    {
        return $this->model
            ->where('id', $id)
            ->delete();
    }
}