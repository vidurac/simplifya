<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\UsersRepository;
use App\Repositories\MasterUserRepository;
use App\Repositories\UploadRepository;
use App\Repositories\ImagesRepository;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use Authorizer;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request as ReqHeader;
use App\Repositories\DeviceTokenRepository;

class OauthController extends Controller
{

    /*
	|--------------------------------------------------------------------------
	| Auth Controller
	|--------------------------------------------------------------------------
	|
	|This controller handles the Authentication for API. Its primarily configured to handle authentication
	|Requests using Oauth2 plugin. Oauth plugin is set to validate using client crdentials.
	|
	*/

    private $user;
    private $image;
    private $upload;
    private $token;
    private $master_data;

    public function __construct(UsersRepository $user, ImagesRepository $image, UploadRepository $upload, DeviceTokenRepository $token, MasterUserRepository $master_data) {
        $this->user = $user;
        $this->image = $image;
        $this->upload = $upload;
        $this->token = $token;
        $this->master_data = $master_data;
    }

    public function index()
    {
        //
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
        //getting the user email address
        $userEmail =  trim(Input::get('username'));
        $userExist =$this->user->isuserExist($userEmail);
       
        if(!empty($userExist))
        {
            if($userExist[0]['status'] == 0) {

            } elseif($userExist[0]['status'] == 1) {
                if($userExist[0]['company']['status'] == 0){
                    return response()->json(array('success'=>'false', 'message'=>array(Config::get('messages.COMPANY_INPROGRESS'))), 400);
                } elseif($userExist[0]['company']['status'] == 1) {
                    return response()->json(array('success'=>'false', 'message'=>array(Config::get('messages.COMPANY_PENDING'))), 400);
                } elseif($userExist[0]['company']['status'] == 2) {
                    Input::merge([
                        'grant_type'  => $request->grant_type,  //password
                        'client_id'  => $request->client_id,
                        'client_secret' => $request->client_secret,
                        'username' => trim($request->username),
                        'password'  => $request->password
                    ]);

                    $token = Authorizer::issueAccessToken();

                    $user_obj = $this->user->getUserById($userExist[0]['id']);
                    $user = array();
                    if($user_obj) {
                        $image = $this->image->findWhere(array('entity_id' => $user_obj[0]['id'], 'entity_tag' => 'profile', 'type' => 'profile'))->first();
                        if(empty($image)){
                            $imageUrl = $this->upload->getImageUrl(Config::get('simplifya.BUCKET_IMAGE_PATH'), Config::get('aws.PROFILE_IMG_DIR'), Config::get('aws.PROFILE_DEFAULT_IMAGE'));
                        }
                        else{
                            $imageUrl = $this->upload->getImageUrl(Config::get('simplifya.BUCKET_IMAGE_PATH'), Config::get('aws.PROFILE_IMG_DIR'), $image->name);
                        }
                        $user[] = array('id' => $user_obj[0]['id'],
                            'name' => $user_obj[0]['name'],
                            'email' => $user_obj[0]['email'],
                            'master_user_group_id' => $user_obj[0]['master_user_group_id'],
                            'company_id' => $user_obj[0]['company_id'],
                            'title' => $user_obj[0]['title'],
                            'is_invite' => $user_obj[0]['is_invite'],
                            'status' => $user_obj[0]['status'],
                            'image_url' => $imageUrl,
                            'created_at' => $user_obj[0]['created_at'],
                            'updated_at' => $user_obj[0]['updated_at']
                        );
                    }
                    //get version no
                    $android_version = $this->master_data->findWhere(array('name' => 'ANDROID_VERSION'))->first();
                    $ios_version = $this->master_data->findWhere(array('name' => 'IOS_VERSION'))->first();

                    $return_data = array(
                        'success'  => 'true',
                        'message'  => array(Config::get('messages.USER_LOGIN_SUCCESS')),
                        'token'    => $token,
                        'user_id'  => $userExist[0]['id'],
                        'user'     => $user
                        /*'android_version' => $android_version,
                        'ios_version' => $ios_version,
                        'version_no' => $request->version_no,
                        'device_type' => $request->device_type*/
                    );

                    $clent_version = "";
                    $server_version = "";
                    $new_version_exist = 0;
                    if(isset($request->device_type) && $request->device_type == "ios" && !empty($ios_version))
                    {
                        $clent_version = explode(".",$request->version_no);
                        $server_version = explode(".",$ios_version->value);
                    }
                    if(isset($request->device_type) && $request->device_type == "android" && !empty($android_version))
                    {
                        $clent_version = explode(".",$request->version_no);
                        $server_version = explode(".",$android_version->value);
                    }

                    if($server_version != "")
                    {
                        if(count($server_version) > 0)
                        {
                            for($x = 0; $x < count($server_version); $x++)
                            {
                                if(isset($clent_version[$x]))
                                {
                                    if($server_version[$x] > $clent_version[$x])
                                    {
                                        $new_version_exist = 1;
                                        break;
                                    }
                                    if($server_version[$x] < $clent_version[$x])
                                    {
                                        $new_version_exist = 0;
                                        break;
                                    }
                                }
                                else
                                {
                                    //$new_version_exist = 1;
                                    //break;
                                }
                            }
                        }
                    }
                    if(count($server_version) > count($clent_version))
                    {
                        //$new_version_exist = 1;
                    }
                    $return_data['new_version_exist'] = $new_version_exist;
                    if($new_version_exist == 1)
                    {
                        $return_data['msg'] = "A new version is available. Please download for best experience!";
                    }

                    
                  $device_token =  $request->device_token;
                  $user_id = $user_obj[0]['id'];
                  $datetime = Carbon::now();

                  if($device_token != '' and $device_token != '(null)') {                  
                     $tokenUser = $this->token->isExistDeviceToken($device_token, $request->device_type); 
                     if($tokenUser){
                        $this->token->updateTokenUser($device_token, $user_id, $request->device_type);
                     } else { 
                        $this->token->insertDeviceToken($device_token, $user_id, $datetime, $request->device_type);
                     }
                  }

                    return response()->json($return_data, 200);

                } elseif($userExist[0]['company']['status'] == 3) {
                    return response()->json(array('success'=>'false', 'message'=>array(Config::get('messages.COMPANY_REJECTED'))), 400);
                } elseif($userExist[0]['company']['status'] == 4) {
                    return response()->json(array('success'=>'false', 'message'=>array(Config::get('messages.COMPANY_INACTIVATED'))), 400);
                } elseif($userExist[0]['company']['status'] == 5) {
                    return response()->json(array('success'=>'false', 'message'=>array(Config::get('messages.COMPANY_EXPIRE'))), 400);
                } elseif($userExist[0]['company']['status'] == 6) {
                    return response()->json(array('success'=>'false', 'message'=>array(Config::get('messages.COMPANY_SUSPEND'))), 400);
                }
            } else {
                return response()->json(array('success'=>'false', 'message'=>array(Config::get('messages.USER_INACTIVATED'))), 400);
            }
        }else{
            return response()->json(array('success'=>'false', 'message'=>array(Config::get('messages.USER_NOT_EXISTS'))), 400);
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
    public function destroy(Request $request)
    {
        $_token = '';
        $parts  = '';

        // Get Auth Token
        $token = ReqHeader::header('Authorization');
        // Get Device Type
        $device_type = ReqHeader::header('DeviceType');
        // Get Device Token
        $device_token = ReqHeader::header('DeviceToken');

        if($token != null) {
            $parts = explode(' ', $token);
        }

        if(isset($parts[1])){
            $_token = $parts[1];
        }

        // Remove Auth token
        $response = $this->user->RevokeAccessToken($_token);

        // Remove Device token
        if($device_type != '' and $device_token != '' and $device_type != null and $device_token != null){
            $device_token_status = $this->user->UnAssignDeviceToken($device_type, $device_token);
        }

        if($response){
            return response()->json(array('success' => 'true', 'message' => array(Config::get('messages.LOGOUT_SUCCESS'))), 200);
        }else{
            return response()->json(array('success' => 'false', 'error' => array(Config::get('messages.LOGOUT_FAIL'))), 400);
        }
    }
}
