<?php

namespace App\Repositories;

use Bosnadev\Repositories\Eloquent\Repository;
use Illuminate\Support\Facades\DB;

/**
 * Class EmailNotificationLogRepository
 * @package App\Repositories
 */
class EmailNotificationLogRepository extends Repository {

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function model()
    {
        return 'App\Models\EmailNotificationLog';
    }

    public function findEmailLogWithType($id, $type)
    {
        $emailLogs = $this->model
            ->where('company_id', '=', $id)
            ->where('notification_type', '=', $type)->first();
        return $emailLogs;
    }

    public function insertMultipleEmailLog($data)
    {
        return DB::table('email_notification_logs')->insert($data);
    }
}