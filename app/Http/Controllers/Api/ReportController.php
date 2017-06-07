<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\UsersRepository;
use App\Repositories\AppointmentRepository;
use App\Repositories\CompanyUserRepository;
use Illuminate\Support\Facades\Auth;
use Authorizer;
use App\Http\Requests\AssginUsersApiRequest;
use App\Repositories\AppointmentActionItemUsersRepository;
use Illuminate\Support\Facades\Config;
use App\Http\Requests\ActionCommentApiRequest;
use App\Repositories\AppointmentActionItemCommentsRepository;
use App\Repositories\UploadRepository;
use App\Repositories\AppointmentCommentsNotifyUsersRepository;
use Aws\Laravel\AwsFacade as AWS;
use App\Repositories\AppointmentQuestionRepository;
use DateTime;
use App\Repositories\QuestionActionItemRepository;
use App\Lib\sendMail;
use App\Events\AssignUserNotifRequest;
use App\Events\AddCommentNotifRequest;

class ReportController extends Controller
{

    private $user;
    private $appointment;
    private $company_users;
    private $action_users;
    private $comment;
    private $upload;
    private $notification;
    private $appointment_question;
    private $action_item;

    //Construct method
    public function __construct(UsersRepository $user, AppointmentRepository $appointment, CompanyUserRepository $company_users, AppointmentActionItemUsersRepository $action_users, AppointmentActionItemCommentsRepository $comment, UploadRepository $upload, AppointmentCommentsNotifyUsersRepository $notification, AppointmentQuestionRepository $appointment_question, QuestionActionItemRepository $action_item){
        $this->user        = $user;
        $this->appointment = $appointment;
        $this->company_users = $company_users;
        $this->action_users = $action_users;
        $this->comment = $comment;
        $this->upload = $upload;
        $this->notification = $notification;
        $this->appointment_question = $appointment_question;
        $this->action_item = $action_item;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
        //
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
     * get all marijuana business user list by location
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsersByLocation($appointment_id='', $action_item_id='')
    {

        //get user id according to access token
        $userId = Authorizer::getResourceOwnerId();

        $errors = [];

        if($appointment_id == '') $errors[] = 'Appointment id required!';
        if($action_item_id == '') $errors[] = 'Action item id required!';

        if(!$errors){
            $user_arr = [];
            $dataset = [];
            $status = "";

            //get appointment details by appointment id
            $appointment = $this->appointment->find($appointment_id);
            $company_users = $this->company_users->findWhere(array('location_id' => $appointment->company_location_id));

            //get user list
            foreach ($company_users as $user)
            {
                $user_arr[] = $user->user_id;
            }

            $user_obj = $this->user->getUserById($userId);

            $user_details = $this->user->getLocationUsersWithAvatar($user_arr, $user_obj[0]['company_id']);
            $check_users = $this->user->checkUserAssigned($user_arr, $appointment_id, $action_item_id);

            foreach ($user_details as $detail){
               $status = false;
               foreach ($check_users as $check_user){
                  if($detail->id == $check_user->user_id){
                     $status = true;
                     break;
                  }
               }
               $dataset[] = array(
                  'id' => $detail->id,
                  'name' => $detail->name,
                  'image_name' => ($detail->image_name == null) ? Config::get('simplifya.BUCKET_IMAGE_PATH') . ltrim(Config::get('simplifya.PROFILE_IMG_DIR'), "/"). '/' .Config::get('simplifya.DEFAULT_PROFILE_IMAGE') : Config::get('simplifya.BUCKET_IMAGE_PATH') . ltrim(Config::get('simplifya.PROFILE_IMG_DIR'), "/"). '/' .$detail->image_name,
                  'status' => $status
               );
            }
            return Response()->json(array('success' => 'true', 'data' => $dataset), 200);
        }else{
            return Response()->json(array('success' => 'true', 'message' => $errors), 400);
        }
    }

    /**
     * Assign Users to action item
     *
     * @param AssginUsersRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    
    public function assignUsers(AssginUsersApiRequest $request)
    {
        
        $appointment_id = $request->appointment_id;
        $action_item_id = $request->action_id;
        
        $data_arr = [];
        $users_arr = [];
        $email_user = [];
        $action_user = array();
        $action_item_details = $this->action_item->find($action_item_id);
        
        if($action_item_details == ''){
           $message = Config::get('messages.ACTION_ITEM_USER_ASSIGNED_FAILED');
           return Response()->json(array('success' => 'false', 'message' => [$message]), 400);
        }
        // Get users list
        $users_list = json_decode($request->user_id);

        //check if location users is an array
        $check_arr = is_array($users_list);
        
        // Get user id from access token
        $user_id = Authorizer::getResourceOwnerId();

        if(empty($users_list)) {
            //remove all users in specified user and appointment
            $removeAll = $this->user->removeAssignedUser($appointment_id, $action_item_id);
            if($removeAll) {
                $message = Config::get('messages.ACTION_ITEM_USER_UPDATE_SUCCESS');
                return Response()->json(array('success' => 'true', 'message' => [$message]), 200);
            } else {
                $message = Config::get('messages.ACTION_ITEM_USER_UPDATE_SUCCESS');
                return Response()->json(array('success' => 'true', 'message' => [$message]), 200);
            }
        } else {
            $action_item_users = $this->action_users->findWhere(array('appointment_id' => $appointment_id, 'question_action_item_id' => $action_item_id));
            foreach($action_item_users as $action_item_user) {
                $action_user[] =  $action_item_user['user_id'];
            }

            if(count($action_user) > 0) {
                $user1 = array_diff((array)$users_list, $action_user);
                $user2 = array_diff($action_user, (array)$users_list);
                if (!empty($user1) && !empty($user2)) {
                    $this->addActionItemUser($user1, $appointment_id, $action_item_id, $user_id, $action_item_details);
                    foreach ($user2 as $user) {
                        $data[] = array(
                            'appointment_id' => $appointment_id,
                            'question_action_item_id' => $action_item_id,
                            'user_id' => $user
                        );
                    }
                    $this->action_users->deleteActionItemUsers($data);
                    $message = Config::get('messages.ACTION_ITEM_USER_UPDATE_SUCCESS');

                    // Send push notifications
                    $this->sendActionItemAssignedPushNotification($user1, $action_item_id, $appointment_id);

                    return Response()->json(array('success' => 'true', 'message' => [$message]), 200);
                } else {
                    if (!empty($user1)) {
                        $response = $this->addActionItemUser($user1, $appointment_id, $action_item_id, $user_id, $action_item_details);


                        if($response['success'] == 'true') {
                            // Send push notifications
                            $this->sendActionItemAssignedPushNotification($user1, $action_item_id, $appointment_id);

                            return Response()->json(array('success' => 'true', 'message' => [$response['message']]), 200);
                        } else {
                            return Response()->json(array('success' => 'true', 'message' => [$response['message']]), 200);
                        }
                    } else if (!empty($user2)) {
                            $data = array();
                            foreach ($user2 as $user) {
                                $data[] = array(
                                    'appointment_id' => $appointment_id,
                                    'question_action_item_id' => $action_item_id,
                                    'user_id' => $user
                                );
                                $delete_user[] = $user;
                            }
                            $response = $this->action_users->deleteActionItemUsers($data);
                            if ($response) {
                                $users_detail = $this->user->getUserEmailById($delete_user);
                                $layout = 'emails.action_item_remove';
                                $subject = 'Action item email';
                                foreach ($users_detail as $user_detail) {
                                    $email_data = array(
                                        'from' => Config::get('simplifya.SIMPLIFIYA_EMAIL'),
                                        'system' => 'Simplifya',
                                        'company' => 'Simplifya',
                                        'action_item' => $action_item_details->name
                                    );
                                    $this->sendActionItemMail($user_detail->email, $user_detail->name, $layout, $subject,
                                        $email_data);
                                }
                                $message = Config::get('messages.ACTION_ITEM_USER_UPDATE_SUCCESS');
                                return Response()->json(array('success' => 'true', 'message' => [$message]), 200);
                            }
                        }else{
                           $message = Config::get('messages.ACTION_ITEM_USER_UPDATE_SUCCESS');
                           return Response()->json(array('success' => 'true', 'message' => [$message]), 200);
                        }
                    }
            } else {
                foreach ((array)$users_list as $item) {
                    $data_arr[] = array(
                        'appointment_id' => $appointment_id,
                        'question_action_item_id' => $action_item_id,
                        'user_id' => $item,
                        'created_by' => $user_id,
                        'updated_by' => $user_id,
                    );
                    $email_user[] = $item;
                }
                //insert record
                $save_action_users = $this->action_users->insertUsers($data_arr);
                if ($save_action_users) {

                    // Send push notifications
                    $this->sendActionItemAssignedPushNotification($users_list, $action_item_id, $appointment_id);

                    // Send email notifications
                    $users_detail = $this->user->getUserEmailById($email_user);
                    $layout = 'emails.action_item_assign';
                    $subject = 'Action item email';
                    foreach($users_detail as $user_detail) {
                        $email_data = array('from' => Config::get('simplifya.SIMPLIFIYA_EMAIL'),'system' => 'Simplifya','company' => 'Simplifya', 'action_item' => $action_item_details->name);
                        $this->sendActionItemMail($user_detail->email, $user_detail->name, $layout, $subject, $email_data);
                    }

                    $message = Config::get('messages.ACTION_ITEM_USER_ASSIGNED_SUCCESS');
                    return Response()->json(array('success' => 'true', 'message' => [$message]), 200);
                } else {
                    $message = Config::get('messages.ACTION_ITEM_USER_ASSIGNED_FAILED');
                    return Response()->json(array('success' => 'false', 'data' => [$message]), 400);
                }
            }
        }
    }

    /**
     * Sending push notifications to action item assiged users
     * @param $users
     * @param $action_item_id
     * @param $appointment_id
     * @return array|null
     */
    public function sendActionItemAssignedPushNotification($users, $action_item_id, $appointment_id){
        // Send push notifications
        $data_pushnotif = new \stdClass();
        $users = is_array($users) ? $users : [$users];
        $data_pushnotif->users = array_values($users);
        $data_pushnotif->action_item_id = $action_item_id;
        $data_pushnotif->appointment_id = $appointment_id;

        return $status = event(new AssignUserNotifRequest($data_pushnotif));
    }

    /**
     * Send push notifications when commented on action items
     * @param $users
     * @param $action_item_id
     * @param $appointment_id
     * @param $user_name
     * @return array|null
     */
    public function sendAddCommentPushNotification($users, $action_item_id, $appointment_id, $user_name){
        // Send push notifications
        $data_pushnotif = new \stdClass();
        $data_pushnotif->users = array_values($users);
        $data_pushnotif->action_item_id = $action_item_id;
        $data_pushnotif->appointment_id = $appointment_id;
        $data_pushnotif->commented_users_name = $user_name;

        return $status = event(new AddCommentNotifRequest($data_pushnotif));
    }
    
    public function addActionItemUser($user1, $appointment_id, $action_item_id, $user_id, $action_item_details)
    {
        $data = array();
        foreach ($user1 as $user) {
            $data[] = array(
                'appointment_id' => $appointment_id,
                'question_action_item_id' => $action_item_id,
                'user_id' => $user,
                'created_by' => $user_id,
                'updated_by' => $user_id,
            );
            $email_user[] = $user;
        }
        //insert record
        $save_action_users = $this->action_users->insertUsers($data);
        if ($save_action_users) {
            $users_detail = $this->user->getUserEmailById($email_user);
            $layout = 'emails.action_item_assign';
            $subject = 'Action item email';
            foreach ($users_detail as $user_detail) {
                $email_data = array(
                    'from' => Config::get('simplifya.SIMPLIFIYA_EMAIL'),
                    'system' => 'Simplifya',
                    'company' => 'Simplifya',
                    'action_item' => $action_item_details->name
                );
                $this->sendActionItemMail($user_detail->email, $user_detail->name, $layout, $subject,
                    $email_data);
            }
            $message = Config::get('messages.ACTION_ITEM_USER_ASSIGNED_SUCCESS');
            return array('success' => 'true', 'message' => $message);
        } else {
            $message = Config::get('messages.ACTION_ITEM_USER_ASSIGNED_FAILED');
            return array('success' => 'false', 'data' => $message);
        }
    }
    public function sendActionItemMail($email, $name, $layout, $subject,$data)
    {
        $send_mail = new sendMail();
        $send_mail->mailSender($layout, $email, $name, $subject, $data);
    }
    

    /**
     * Insert comment to action item on the action item list
     *
     * @param ActionCommentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function insertComment(ActionCommentApiRequest $request)
    {
        $appointment_id = $request->appointment_id;
        $action_id = $request->action_id;
        $comment = $request->comment;
        $entity_tag = $request->entity_tag;
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $location = '';

        //get user id according to access token
        $user_id = Authorizer::getResourceOwnerId();
        if($latitude !='0.000000'){
            $location = $this->geocode($latitude, $longitude);
        }
        $dataset = array(
            'appointment_id' => $appointment_id,
            'question_action_item_id' => $action_id,
            'content' => $comment,
            'status' => 1,
            'user_id' => $user_id,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'location' => $location,
            'created_by' => $user_id,
            'updated_by' => $user_id
        );

        $save_comment = $this->comment->create($dataset);
        $entity_id = $save_comment['id'];

        // Insert notification mapping to each user
        $users = $this->action_users->getNotifiedUsers($appointment_id, $action_id);

        $notified_users = [];

        foreach ($users as $key => $user) {
            if ($user != $user_id) {
                $notifyData = array(
                    'user_id' => $user,
                    'appointment_action_item_comments_id' => $entity_id,
                    'type' => 1,
                    'created_by' => $user_id,
                    'updated_by' => $user_id
                );
                $notified_users[] = $user;
                $this->notification->create($notifyData);
            }
        }

        //Get Amazon API instance
        $s3 = AWS::createClient('s3');

        $pic_id = 0;
        $files = $request->imgInp;
        $image_formats = array('jpeg', 'jpg', 'png');

        $file_names = [];

        if($files != '' and is_array($files)){
            foreach ($files as $file) {

                if (!empty($file)) {

                    // Create name for file
                    $filename = $file->getClientOriginalExtension(); //getClientOriginalName();
                    $path = $file->getRealPath();
                    $fileExt = $filename;

                    try {
                        $generatedName = uniqid() . $user_id . uniqid();
                        $filename = $generatedName . '.' . $filename;

                        // Upload an object to Amazon S3
                        $result = $s3->putObject(array(
                            'Bucket' => Config::get('aws.bucket'),
                            'Key' => Config::get('aws.ACTION_COMMENT_IMG_DIR') . $filename,
                            'SourceFile' => $path,
                            'body' => $path,
                            'ContentType' => $file->getClientMimeType(),
                            'ACL' => 'public-read'
                        ));

                        if (isset($result['ObjectURL'])) {
                            // Save comment image in photo table
                            $pic_id = $this->upload->setFile($user_id, $filename, Config::get('simplifya.UPD_TYPE_ACTION_COMMENT_PIC'), $entity_tag, $entity_id, $filename, $appointment_id);
                            // collecting file names
                            $file_names[] = $filename;
                        }

                    } catch (Exception $ex) {
                        $messages = Config::get("messages.FILE_UPLOAD_ERROR");
                        //return response()->json(array('success' => 'false', 'error' => $messages), 400);
                    }
                }
            }
        }

         
         // prepare return dataset 
         $images_list = [];
         // creating full image paths
         foreach($file_names as $img){
            $images_list[] = Config::get('simplifya.BUCKET_IMAGE_PATH') . ltrim(Config::get('simplifya.ACTION_COMMENT_IMG_DIR'), "/"). '/' .$img;
         }
         
         $created_time = new DateTime($save_comment['created_at']);
         $created_date_formated = $created_time->format('F jS, Y');
         $created_time_formated = $created_time->format('g:i A');

         $user_obj = $this->user->find($user_id);

        // Send push notifications
        $this->sendActionItemAssignedPushNotification($notified_users, $action_id, $appointment_id, $user_obj->name);


        $return_data = [
             'comment_id'                => (string) $entity_id,
             'appointment_id'            => $dataset['appointment_id'],
             'question_action_item_id'   => $dataset['question_action_item_id'],
             'comment'                   => $dataset['content'],
             'created_at'                => $created_time_formated,
             'latitude'                  => (string) $dataset['latitude'],
             'longitude'                 => (string) $dataset['longitude'],
             'images'                    => $images_list,
             'name'                      => $user_obj->name,
             'date'                      => $created_date_formated,
             'time'                      => $created_time_formated
         ];
        
         if ($save_comment) {
             $message = Config::get('messages.ACTION_ITEM_USER_COMMENT_SUCCESS');
             return Response()->json(array('success' => 'true', 'data' => $return_data), 200);
         } else {
             $message = Config::get('messages.ACTION_ITEM_USER_COMMENT_FAILED');
             return Response()->json(array('success' => 'false', 'message' => $message), 400);
         }

    }

    /**
     * Get action items
     * @param type $appointment_id
     * @return type
     */
    public function getActionItems($appointment_id){
        $dataset = "";
        $action_item_arr = array();
        $master_answer_arr = array();

        //get user id according to access token
        $user_id = Authorizer::getResourceOwnerId();

        // Get user data
        $user_obj = $this->user->find($user_id);
        $user_role = $user_obj->master_user_group_id;

        // Set aswer ids
        $answer_id = [2,3];

        // Get master answer ids
        $getMasterAnswers = $this->appointment_question->findAnsweredAppointmentQuestions($appointment_id, $user_id, $user_role);

        // Collect master answer ids
        foreach ($getMasterAnswers as $item){
            $master_answer_arr[] = $item->master_answer_id;
        }

        //get all active answered questions with non-compliance answers from the appointment questions list
        $nonComplianceActionItems = $this->appointment_question->getAllNonComplianceQuestions($appointment_id, $master_answer_arr, $answer_id, $user_id, $user_role);

        // Collect action item ids
        foreach ($nonComplianceActionItems as $item){
            $action_item_arr[] = $item->action_item_id;
        }

        // get unread records
        $unread_records = $this->appointment_question->getUnreadRecords($appointment_id, $action_item_arr, $user_id);

        $unread_data = [];

        foreach($unread_records as $rec){
            $unread_data[$rec->question_action_item_id][] = $rec->appointment_action_item_comments_id;
        }

        $nonComplianceActionItemsWithCount = [];

        // Attach unread count
        foreach($nonComplianceActionItems as $data){
            $temp_data = $data;
            $temp_data->unread_count = isset($unread_data[$data->action_item_id]) ? count($unread_data[$data->action_item_id]) : 0;
            $nonComplianceActionItemsWithCount[] = $temp_data;
        }

        // Response
        return Response()->json(array('success' => 'true', 'data' => $nonComplianceActionItemsWithCount), 200);
    }
    
    /**
     * Get data for appointmentReportList
     * @param type $appointment_id
     * @return type
     */
    public function getAppointmentReportList($appointment_id, $tree=''){
      
      // Load data
      $dataset_all = $this->loadReportListData($appointment_id, $tree);
      
      // Return formated dataset
      return Response()->json(array('success' => 'true', 'data'=>$dataset_all), 200); 
    }
    
    /**
     * Load and prepare reports list
     * @param type $appointment_id
     * @return type
     */
    public function loadReportListData($appointment_id, $tree=''){
      // Get data from DB 
      $result = $this->appointment_question->getAppointmentReportList($appointment_id);
      
      $dataset = [];
      $categories = [];
      
      foreach($result as $data){
         // Questions
         $dataset[$data->question_id]['question_id'] = $data->question_id;
         $dataset[$data->question_id]['parent_question_id'] = $data->parent_question_id;
         $dataset[$data->question_id]['question'] = $data->question;
         $dataset[$data->question_id]['explanation'] = $data->explanation;
         $dataset[$data->question_id]['appointment_id'] = $data->appointment_id;
         $dataset[$data->question_id]['category_id'] = $data->category_id;
         $dataset[$data->question_id]['category_name'] = $data->category_name;
         $dataset[$data->question_id]['comment'] = $data->comment;
         $dataset[$data->question_id]['option_value'] = $data->option_value;
         $dataset[$data->question_id]['report_status'] = $data->report_status;
         
         // Categories
         $categories[$data->category_id]['id'] = $data->category_id;
         $categories[$data->category_id]['name'] = $data->category_name;
         
         // Action items
         if($data->answer_value_id == 2 or $data->answer_value_id == 3){
            $dataset[$data->question_id]['action_items'][$data->action_item_id]['id'] = $data->action_item_id;
            $dataset[$data->question_id]['action_items'][$data->action_item_id]['name'] = $data->action_item_name;
            $dataset[$data->question_id]['action_items'][$data->action_item_id]['status'] = $data->action_item_status;
         }
         
         // Set users
         if($data->action_item_user_id != ''){
            $dataset[$data->question_id]['action_items'][$data->action_item_id]['assigned_users'][$data->action_item_user_id]['id'] = $data->action_item_user_id;
            $dataset[$data->question_id]['action_items'][$data->action_item_id]['assigned_users'][$data->action_item_user_id]['name'] = $data->user_name;
         }
         
         // Answers
         $dataset[$data->question_id]['answers'][$data->answer_id]['answer_id'] = $data->answer_id;
         $dataset[$data->question_id]['answers'][$data->answer_id]['answer_value_name'] = $data->answer_value_name;
         $dataset[$data->question_id]['answers'][$data->answer_id]['answer_value_id'] = $data->answer_value_id;
         
         $dataset[$data->question_id]['answer_value_name'] = $data->answer_value_name;
         $dataset[$data->question_id]['answer_value_id'] = $data->answer_value_id;
         
         // Set images
         if($data->image_name != ''){
            $dataset[$data->question_id]['images'][] = Config::get('simplifya.BUCKET_IMAGE_PATH') . ltrim(Config::get('simplifya.ACTION_COMMENT_IMG_DIR'), "/"). '/' . $data->image_name;
         }
      }
      
      // Init formated dataset
      $dataset_fotmated = [];
        
      // Format dataset
      foreach($dataset as $key => $data){
         // Set undefined images
//         foreach($data['answers'] as $answer){
//            $data['answers'][$answer['answer_id']]['images'] = isset($answer['images']) ? $answer['images'] : [];
//         }
         $data['images'] = isset($data['images']) ? array_values(array_unique(array_values($data['images']))) : [];
         
         // Set undefined action item users
         if(isset($data['action_items'])){
            foreach($data['action_items'] as $action_item){
               $data['action_items'][$action_item['id']]['assigned_users'] = isset($data['action_items'][$action_item['id']]['assigned_users']) ? array_values($data['action_items'][$action_item['id']]['assigned_users']) : [];
            }
         }
         // Set undefined answers
         $data['answers'] = isset($data['answers']) ? array_values($data['answers']) : [];
         $data['questions'] = isset($data['questions']) ? array_values($data['questions']) : [];
         // Set undefined action items
         $data['action_items'] = isset($data['action_items']) ? array_values($data['action_items']) : [];
         
         $dataset_fotmated[$key] = $data;
      }
      
      // Build question tree
      if($tree != ''){
         $dataset_fotmated = $this->buildTree($dataset_fotmated, 0);
      }
      
      $dataset_all = [
          'categories' => array_values($categories),
          'questions' => array_values($dataset_fotmated)
      ];
      
      return $dataset_all;
    }
    
    /**
     * Build questions tree
     * @param array $elements
     * @param type $parent_id
     * @return type
     */
    function buildTree(array &$elements, $parent_id = 0) {

      $branch = array();

      foreach($elements as &$element) {

         if($element['parent_question_id'] == $parent_id){
            $children = $this->buildTree($elements, $element['question_id']);
            if($children){
               $element['questions'] = $children;
            }
            $branch[$element['question_id']] = $element;
            $branch = array_values($branch);
            unset($element);
         }
      }
      return $branch;
    }

    public function geocode($lat, $long){
        $location = '';
        $key = Config::get('simplifya.GoogleApiKey');
        $details_url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$lat.','.$long."&key=".$key;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $details_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $geolocs = json_decode(curl_exec($ch), true);
        if($geolocs['status'] == 'OK') {
            if(count($geolocs['results'][0]['address_components']) > 2) {
                return $geolocs['results'][0]['address_components'][2]['long_name'];
            } else {
                return $geolocs['results'][0]['address_components'][1]['long_name'];
            }
        } else {
            return $location;
        }
    }
}
