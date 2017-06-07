<?php
/**
 * Created by PhpStorm.
 * User: Nishan
 * Date: 5/9/2016
 * Time: 9:48 AM
 */

namespace App\Repositories;

use Bosnadev\Repositories\Eloquent\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;


class AppointmentClassificationRepository extends Repository
{
    /**
     * @return string
     */
    public function model()
    {
        return 'App\Models\AppointmentClassification';
    }

    public function getLicenceDataByAppointmentId($appointmentId)
    {
        return DB::table('appointment_classifications')
            ->select('master_licenses.name','master_licenses.id')
            ->join('master_licenses', 'master_licenses.id', '=', 'appointment_classifications.option_value')
            ->where('appointment_classifications.appointment_id', $appointmentId)
            ->where('appointment_classifications.entity_type', "LICENCE")
            ->get();

    }

    /**
     * Get license data with company license number
     * @param $appointmentId
     * @return mixed
     */
    public function getLicenceDataWithLicenseNumberByAppointmentId($appointmentId, $locationId)
    {
        return DB::table('appointment_classifications')
            ->select('master_licenses.name','master_licenses.id', 'company_location_licenses.license_number')
            ->join('master_licenses', 'master_licenses.id', '=', 'appointment_classifications.option_value')
            ->join('company_location_licenses', 'company_location_licenses.license_id', '=', 'appointment_classifications.option_value')
            ->where('appointment_classifications.appointment_id', $appointmentId)
            ->where('company_location_licenses.location_id', $locationId)
            ->where('appointment_classifications.entity_type', "LICENCE")
            ->get();

    }

    public function getLicenceNumberByAppointmentId($licenceId,$locationId)
    {
        return DB::table('company_location_licenses')
            ->select('company_location_licenses.license_number')
            ->where('company_location_licenses.license_id', $licenceId)
            ->where('company_location_licenses.location_id',$locationId )
            ->first();

    }

    /**
     * Returns appointment audit type by id
     * @param $id
     */
    public function getAuditTypeByAppointmentId($id) {
        return $this->model->where('appointment_id', $id)->where('entity_type', 'AUDIT_TYPE')
            ->join('master_audit_types', 'master_audit_types.id', '=', 'appointment_classifications.option_value')
            ->select('master_audit_types.name')
            ->first();
    }

}