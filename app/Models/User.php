<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password', 'master_user_group_id', 'company_id', 'title', 'is_invite', 'status', 'created_by', 'updated_by' ,'is_send_mail'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    public function companyUser() {
        return $this->hasMany('App\Models\CompanyUser', 'user_id');
    }

    public function masterUserGroup() {
        return $this->belongsTo('App\Models\MasterUserGroup', 'master_user_group_id');
    }

    public function company() {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

    public function action_comment()
    {
        return $this->hasOne('App\Models\AppointmentActionItemComment', 'user_id');
    }

    public function question() {
        return $this->hasMany('App\Models\Question', 'created_by');
    }

    public function rosterAssign() {
        return $this->hasMany('App\Models\RosterAssign', 'user_id');
    }

}
