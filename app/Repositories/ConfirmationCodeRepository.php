<?php
/**
 * Created by PhpStorm.
 * User: Harsha
 * Date: 7/21/2016
 * Time: 10:44 AM
 */

namespace App\Repositories;
use Bosnadev\Repositories\Eloquent\Repository;
use DB;

class ConfirmationCodeRepository extends Repository
{
    public function model()
    {
        return 'App\Models\ConfirmationCode';
    }

    public function getUserDetails($code)
    {
        return $this->model
                    ->select('users.id as id', 'users.email as email', 'users.status as status', 'confirmation_codes.is_confirm as is_confirm')
                    ->join('users', 'users.id', '=', 'confirmation_codes.user_id')
                    ->where('confirmation_codes.confirmation_code', $code)
                    ->get();
    }

    public function updateUserConfirmation($user_id, $is_confirm)
    {
        return $this->model
                ->where('user_id', $user_id)
                ->update(array('is_confirm' => $is_confirm));
    }
    public function updateUserConfirmationCode($user_id, $code)
    {
        return $this->model
                ->where('user_id', $user_id)
                ->update(array('confirmation_code' => $code));
    }
}