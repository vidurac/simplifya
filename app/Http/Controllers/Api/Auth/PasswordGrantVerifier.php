<?php
/**
 * Created by PhpStorm.
 * User: Harsha
 * Date: 6/21/2016
 * Time: 11:52 AM
 */

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Support\Facades\Auth;

class PasswordGrantVerifier
{
    public function verify($username, $password)
    {
        $credentials = [
            'email'    => $username,
            'password' => $password,
        ];

        if (Auth::once($credentials)) {
            return Auth::user()->id;
        }

        return false;
    }
}