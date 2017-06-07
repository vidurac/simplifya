<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 7/25/2016
 * Time: 4:32 PM
 */

namespace App\Repositories;
use Bosnadev\Repositories\Eloquent\Repository;
use DB;

class LicenseRemindersRepositories extends Repository
{
    public function model()
    {
        return 'App\Models\LicenseReminder';
    }

    public function insertReminders($data)
    {
        return DB::table('license_reminders')->insert($data);
    }

    public function getLicenseRenewalDate()
    {
        return $this->model
                ->select(
                    'users.company_id',
                    'license_reminders.reminder',
                    'license_reminders.license_location_id',
                    'license_reminders.user_id',
                    'license_reminders.id as reminder_id',
                    'location_license2.license_number',
                    'location_license2.renewal_date',
                    'location_license2.license_id',
                    'location_license2.license_date'
                )
                ->join('company_location_licenses as location_license2', 'license_reminders.license_location_id', '=', 'location_license2.id')
                ->join('users', 'license_reminders.user_id', '=', 'users.id')
                ->get();
    }

    public function deleteReminders($result, $license_location_id) {
        return $this->model
                ->whereIn('reminder', $result)
                ->where('license_location_id', $license_location_id)
                ->delete();
    }
}