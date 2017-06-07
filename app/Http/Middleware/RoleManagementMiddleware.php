<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use Config;
use Illuminate\Support\Facades\Session;

class RoleManagementMiddleware
{

    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
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
        $URL = url('/').'/company/info';
        $role_id = Auth::User()->master_user_group_id;
        $status = Session('company_status');
        $entity_type = Session('entity_type');

        if($entity_type == 1) {
            if($request->url() == $URL) {
                return redirect('dashboard');
            } else {
                return $next($request);
            }
        } else {
            if($status == 0) {
                if($role_id == 2) {
                    if($request->url() == $URL) {
                        return $next($request);
                    } else {
                        return $next($request);
                    }
                } elseif($role_id == 3) {
                    Auth::logout();
                    return redirect('not/allowed');
                } elseif($role_id == 4) {
                    Auth::logout();
                    return redirect('not/allowed');
                } elseif($role_id == 5) {
                    return $next($request);
                } elseif($role_id == 6) {
                    Auth::logout();
                    return redirect('not/allowed');
                } elseif($role_id == 7) {
                    return $next($request);
                } elseif($role_id == 8) {
                    Auth::logout();
                    return redirect('not/allowed');
                }
            } elseif($status == 1) {
                if($entity_type == 3 || $entity_type == 4) {
                    Auth::logout();
                    return redirect('pending');
                }
            } elseif($status == 2) {
                if($request->url() == $URL) {
                    if(Session('is_first_attempt') == 0) {
                        if($role_id == 5 || $role_id == 7) {
                            return $next($request);
                        } else {
                            return redirect('dashboard');
                        }
                    } else {
                        return redirect('dashboard');
                    }
                }  else {
                    $is_able = $this->urlManager($request->url(), $role_id);
                    if($is_able) {
                        return $next($request);
                    } else {
                        return redirect('dashboard');
                    }
                }
            } elseif($status == 3) {
                $message = Config::get('messages.CC_GE_COMPANY_REJECTED');
                Session::put('reg_message', $message);
                if($entity_type == 3 || $entity_type == 4) {
                    Auth::logout();
                    return redirect('not/allowed');
                }
            } elseif($status == 4) {
                $message = Config::get('messages.CC_GE_COMPANY_INACTIVATED');
                Session::put('reg_message', $message);
                if($role_id == 2) {
                    return $next($request);
                } elseif($role_id == 3) {
                    Auth::logout();
                    return redirect('not/allowed');
                } elseif($role_id == 4) {
                    Auth::logout();
                    return redirect('not/allowed');
                } elseif($role_id == 5 || $role_id == 7) {
                    return $next($request);
                } elseif($role_id == 6 || $role_id == 8) {
                    Auth::logout();
                    return redirect('not/allowed');
                }
            } elseif($status == 5) {
                if($role_id == 2) {
                    if($request->url() == $URL) {
                        return $next($request);
                    } else {
                        return $next($request);
                    }
                } elseif($role_id == 3) {
                    Auth::logout();
                    return redirect('not/allowed');
                } elseif($role_id == 4) {
                    Auth::logout();
                    return redirect('not/allowed');
                } elseif($role_id == 5 || $role_id == 7) {
                    return $next($request);
                } elseif($role_id == 6 || $role_id == 8) {
                    Auth::logout();
                    return redirect('not/allowed');
                }
            } elseif($status == 6) {
                Auth::logout();
                return redirect('suspend');
            }
        }
        return $next($request);
    }

    public function urlManager($url, $role_id)
    {
        $is_access = true;
        switch ($url) {
            case url('/').'/configuration':
            case url('/').'/configuration/country':
            case url('/').'/configuration/country/new':
            case url('/').'/configuration/state':
            case url('/').'/configuration/state/new':
            case url('/').'/configuration/city':
            case url('/').'/configuration/city/new':
            case url('/').'/configuration/licenses':
            case url('/').'/configuration/mqcategories':
            case url('/').'/configuration/qcategory/options/1':
            case url('/').'/configuration/qcategories':
            case url('/').'/configuration/qcategories/new':
            case url('/').'/configuration/subscription/mjb':
            case url('/').'/configuration/subscription/mjb/new':
            case url('/').'/configuration/subscription/cc_ge':
            case url('/').'/configuration/subscription/cc_ge/new':
            case url('/').'/configuration/userGroup':
            case url('/').'/configuration/masterdata':
            case url('/').'/configuration/coupons':
            case url('/').'/configuration/coupons/new':
                if($role_id == 1){
                    $is_access = true;
                } else {
                    $is_access = false;
                }
        break;
            case url('/').'/checklist':
                if($role_id == 1){
                    $is_access = true;
                } else {
                    $is_access = false;
                }
        break;
            case url('/').'/mailchimp':
                if($role_id == 1){
                    $is_access = true;
                } else {
                    $is_access = false;
                }
        break;
            case url('/').'/company/manager':
                if($role_id == 1){
                    $is_access = true;
                } else {
                    $is_access = false;
                }
        break;
            case url('/').'/question':
            case url('/').'/question/create':
                if($role_id == 1){
                    $is_access = true;
                } else {
                    $is_access = false;
                }
        break;
            case url('/').'/reports/type/list':
            case url('/').'/company/list':
            case url('/').'/company/location/list':
            case url('/').'/company/users/list':
            case url('/').'/inspection/list':
                if($role_id == 1){
                    $is_access = true;
                } else {
                    $is_access = false;
                }
        break;
            default:
                $is_access = true;
        }
        return $is_access;
    }

}
