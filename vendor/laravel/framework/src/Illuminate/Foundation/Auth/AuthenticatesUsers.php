<?php

namespace Illuminate\Foundation\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use DB;
use App\Models\Company;
use App\Models\Image;
use App\Repositories\UploadRepository;
use Illuminate\Support\Facades\Config;



trait AuthenticatesUsers
{
    use RedirectsUsers;
    private $upload;

    public function __construct(UploadRepository $upload){
        $this->upload = $upload;
    }
    /**
     * Show the application login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogin()
    {
        if (view()->exists('auth.authenticate')) {
            return view('auth.authenticate');
        }

        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postLogin(Request $request)
    {
        $this->validate($request, [
            $this->loginUsername() => 'required', 'password' => 'required',
        ]);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        $throttles = $this->isUsingThrottlesLoginsTrait();

        if ($throttles && $this->hasTooManyLoginAttempts($request)) {
            return $this->sendLockoutResponse($request);
        }

        $credentials = $this->getCredentials($request);

        if (Auth::attempt($credentials, $request->has('remember'))) {
            if((Auth::User()->status == 0) ) {
                $this->getLogout();
                return redirect($this->loginPath())
                    ->withInput($request->only($this->loginUsername(), 'remember'))
                    ->withErrors([
                        $this->loginUsername() => 'User does not exist in the system',
                    ]);
            } elseif((Auth::User()->status == 2)) {
                $this->getLogout();
                return redirect($this->loginPath())
                    ->withInput($request->only($this->loginUsername(), 'remember'))
                    ->withErrors([
                        $this->loginUsername() => 'User account temporary suspended',
                    ]);
            }
            else {

                $image = Image::where("entity_tag", "profile")->where("entity_id", Auth::user()->id)->where("type", "profile")->first();

                if(empty($image)){
                    $imageUrl = Config::get('simplifya.BUCKET_IMAGE_PATH').Config::get('aws.PROFILE_IMG_DIR').Config::get('aws.PROFILE_DEFAULT_IMAGE');
                }
                else{
                    $imageUrl = Config::get('simplifya.BUCKET_IMAGE_PATH').Config::get('aws.PROFILE_IMG_DIR').$image->name;
                }

                $result = $this->getCompanyInfo();
                Session::put('profile_image', $imageUrl);
                Session::put('entity_type', $result['entity_type']);
                Session::put('company_status', $result['company_status']);
                Session::put('is_first_attempt', $result['is_attempt']);

                return $this->handleUserWasAuthenticated($request, $throttles);
            }

        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        if ($throttles) {
            $this->incrementLoginAttempts($request);
        }

        return redirect($this->loginPath())
            ->withInput($request->only($this->loginUsername(), 'remember'))
            ->withErrors([
                $this->loginUsername() => $this->getFailedLoginMessage(),
            ]);
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  bool  $throttles
     * @return \Illuminate\Http\Response
     */
    protected function handleUserWasAuthenticated(Request $request, $throttles)
    {
        if ($throttles) {
            $this->clearLoginAttempts($request);
        }

        if (method_exists($this, 'authenticated')) {
            return $this->authenticated($request, Auth::user());
        }

        return redirect()->intended($this->redirectPath());
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function getCredentials(Request $request)
    {
        return $request->only($this->loginUsername(), 'password');
    }

    /**
     * Get the failed login message.
     *
     * @return string
     */
    protected function getFailedLoginMessage()
    {
        return Lang::has('auth.failed')
                ? Lang::get('auth.failed')
                : 'These credentials do not match our records.';
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogout()
    {
        Auth::logout();

        return redirect(property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout : '/');
    }

    /**
     * Get the path to the login route.
     *
     * @return string
     */
    public function loginPath()
    {
        return property_exists($this, 'loginPath') ? $this->loginPath : '/';
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function loginUsername()
    {
        return property_exists($this, 'username') ? $this->username : 'email';
    }

    /**
     * Determine if the class is using the ThrottlesLogins trait.
     *
     * @return bool
     */
    protected function isUsingThrottlesLoginsTrait()
    {
        return in_array(
            ThrottlesLogins::class, class_uses_recursive(get_class($this))
        );
    }

    /**
     * $return array
     */
    public function getCompanyInfo()
    {
        $company = company::find(Auth::User()->company_id);
        $company_detail = array('entity_type' => $company->entity_type, 'company_status' => $company->status, 'is_attempt' => $company->is_first_attempt);
        return $company_detail;
    }
}
