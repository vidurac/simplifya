<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use Config;
use Illuminate\Support\Facades\Session;

class ReportMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $role_id = Auth::User()->master_user_group_id;
        if($role_id==1){
            return redirect('dashboard');
        }
        return $next($request);
    }
}