<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use Config;
use Illuminate\Support\Facades\Session;
use App\Repositories\RosterRepository;

class RosterMiddleware
{


    private $roster,$rosterTask;

    /**
     * RosterController constructor.
     * @param RosterRepository $roster
     */
    public function __construct(RosterRepository $roster)
    {
        $this->roster = $roster;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $roster_company=$roster=$this->roster->getRosterById($request->route()->parameter('roster_id'))->company_id;
        $user_company = Auth::User()->company_id;
        if($roster_company!=$user_company){
            return redirect('dashboard');
        }
        return $next($request);
    }
}