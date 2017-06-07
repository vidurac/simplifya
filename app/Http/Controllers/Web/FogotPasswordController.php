<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\UsersRepository;
use App\Lib\sendMail;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;

class FogotPasswordController extends Controller
{
    private $user;

    public function __construct(UsersRepository $user)
    {
        $this->user = $user;

    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.resetPassword');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = array(
            'email'     => 'required|email',
        );

        // validate against the inputs from api
        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }
        else {

            $email = Input::get('email');
            $send_email = new sendMail();

            $user_data =$this->user->findWhere(array("email" => $email))->first();

            if(!empty($user_data)) {
                $name  = $user_data->name;
                $confirmation_code =  sha1(uniqid().$name);
                $data = array('password_confirmation_code' => $confirmation_code, 'password_is_confirm' => '0');

                $response = $this->user->update($data, $user_data->id);

                if($response) {
                    $data =  array('confirmation_code' => $confirmation_code, 'from' => Config::get('simplifya.SIMPLIFIYA_EMAIL'), 'system' => 'Simplifya');
                    $layout = 'emails.forgotpassword';
                    $subject = 'Reset your Simplifya password';
                    $send_email->mailSender($layout, $email, $name, $subject, $data);

                    return response()->json(array('success' => 'true', 'email'=> $email), 200);

                }else{
                    $messages =  "Server error";
                    return response()->json(array('success' => 'false', 'message'=> $messages), 400);
                }

            }else{
                $messages =  "A user with the email <".$email."> does not exist in our system";
                return response()->json(array('success' => 'false', 'message'=> $messages), 400);
            }

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    /**
     * Confirm password from email link
     *
     * @param  int  $confirmation_code
     * @return view
     */
    public function confirm($confirmation_code)
    {
        $conf_user = $this->user->findWhere(array('password_confirmation_code' => $confirmation_code))->first();

        if(!empty($conf_user)) {
            if(!($conf_user->password_is_confirm )) {
                $data = array('password_is_confirm' => '1');
                $response = $this->user->update($data, $conf_user->id);

                if($response) {
                    return View('auth.resetPasswordConfirm')->with(array('user_id' => $conf_user->id, 'email' =>$conf_user->email));

                } else {
                    return View('errors.400')->with(array('error' => 'Server error'));
                }

            } else {
                return View('errors.400')->with(array('error' => 'Already confirm confirmation token. Please try again'));
            }
        } else {
            return View('errors.400')->with('error', 'Confirmation token is invalid');
        }
    }


    /**
     * Update the new password
     *
     * @return Response
     */
    public function createpassword()
    {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $user = $this->user->findWhere(array('email' => $email))->first();

        if(!empty($user)) {

            $data = array('password' => Hash::make($password));
            $response = $this->user->update($data, $user->id);

            if($response) {
                return View('auth.login')->with('message', 'Your password has been changed successfully');

            } else {
                return View('errors.400')->with('error', 'Password Change Failed');
            }
        } else {
            return View('errors.400')->with('error', 'User does not exist in the system');
        }
    }
}
