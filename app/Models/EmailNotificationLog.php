<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class EmailNotificationLog
 * @package App\Models
 */
class EmailNotificationLog extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'email_notification_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['company_id', 'notification_type'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}
