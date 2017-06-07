<?php

namespace App\Http\Controllers\Api;

use App\Models\MasterData;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\AppointmentGetRequest;
use App\Repositories\AppointmentRepository;
use App\Repositories\AppointmentActionItemCommentsRepository;
use DateTime;
use Authorizer;
use App\Repositories\UsersRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\MasterUserRepository;
use App\Repositories\UploadRepository;
use App\Http\Requests\InspectionSaveRequest;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use AWS;

class AppointmentController extends Controller
{

    private $appointment;
    private $comment;
    private $user;
    private $company;
    private $masterData;
    private $upload;

    public function __construct(AppointmentRepository $appointment, AppointmentActionItemCommentsRepository $comment, UsersRepository $user, CompanyRepository $company,MasterUserRepository $masterData,UploadRepository $upload)
    {
        $this->appointment = $appointment;
        $this->comment = $comment;
        $this->user = $user;
        $this->company = $company;
        $this->masterData = $masterData;
        $this->upload = $upload;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(AppointmentGetRequest $request)
    {
        $company = array();
        $data = array();

        //get user id according to access token
        $userId = Authorizer::getResourceOwnerId();

        $appointments = $this->appointment->getAppointmentByAssignUserId($userId);
        $actionItemsOnOff=$this->masterData->getActionItemsOnOffStatus()->value;
        $statusIndicatorOnOff=$this->masterData->getStatusIndicatorOnOff()->value;


        if($appointments) {
            foreach($appointments as $appointment) {
                $company[] = array('id' => $appointment['company_id'], 'name' => $appointment['company_name']);
                $created_time = new DateTime($appointment['inspection_date_time']);
                $prev_date = $created_time->format('F jS');
                if($prev_date == $created_time->format('F jS')) {
                    $data[$prev_date][] = array(
                        'date' => $created_time->format('F jS'),
                        'date_f' => $created_time->format('m/d/Y'),
                        'time' => $created_time->format('h:i A'),
                        'company_name' => $appointment['company_name'],
                        'company_id' => $appointment['company_id'],
                        'location_name'  => $appointment['loc_name'],
                        'state' => $appointment['state'],
                        'zip_code' => ($appointment['zip_code'] == null)?'':$appointment['zip_code'],
                        'city_name' => $appointment['city_name'],
                        'country_name' => $appointment['country_name'],
                        'appointment_id' => $appointment['appointment_id'],
                        'address_line_1'   => $appointment['address_line_1'],
                        'address_line_2' => $appointment['address_line_2'],
                        'inspection_number' => $appointment['inspection_number'],
                        'assign_to' => $userId,
                        'audit_type' => $appointment['option_value']
                    );
                }
            }

            $data2 = array();
            foreach($data as $key=>$val) {

                $date_created_at = new DateTime($key);

                $created_at = $date_created_at->format('m/d/Y');

                $data2[] = array('appointment_date' =>$key, 'created_at'=>$created_at, 'appointment' => $val);
            }
            return response()->json(array('success' => 'true', 'action_items_status'=>$actionItemsOnOff,'status_indicator'=>$statusIndicatorOnOff,'data' => $data2, 'company' => $company), 200);
        } else {
            return response()->json(array('success' => 'true', 'action_items_status'=>$actionItemsOnOff,'status_indicator'=>$statusIndicatorOnOff,'data' => [], 'company' => [], 'message' => array('No appointments')), 200);
        }

    }

    public function appointmentList ($type, AppointmentGetRequest $request)
    {
        //get user id according to access token
        $user_id = Authorizer::getResourceOwnerId();

        $userdata = $this->user->getUserById($user_id);

        $role         = $userdata[0]['master_user_group_id'];
        $company_id   = $userdata[0]['company_id'];
        $companydata = $this->company->getCompanyById($company_id);
        $company_type = $companydata[0]->entity_type;

        $appointments = $this->appointment->getAppointmentForMobile($role, $company_type, $company_id, $user_id, $type);



        if($appointments) {
            // init arrays
            $data = [];
            $company = [];

            foreach($appointments as $appointment) {


                $company[] = array('id' => $appointment->company_id, 'name' => $appointment->company_name);
                $created_time = new DateTime($appointment->inspection_date_time);
                //$prev_date = $created_time->format('jS F Y');
                //if($prev_date == $created_time->format('jS F Y')) {

                $datatemp = array(
                    'date' => $created_time->format('F jS, Y'),
                    'created_at' => $created_time->format('m/d/Y'),
                    'time' => $created_time->format('g:i A'),
                    'company_name' => $appointment->company_name,
                    'company_id' => $appointment->company_id,
                    'location_name'  => $appointment->loc_name,
                    'state' => $appointment->state,
                    'country_name' => $appointment->country_name,
                    'appointment_id' => $appointment->appointment_id,
                    'address_line_1'   => $appointment->address_line_1,
                    'address_line_2' => $appointment->address_line_1,
                    'inspection_number' => $appointment->inspection_number,
                );

                if(isset($appointment->option_value)){
                    $datatemp['audit_type'] = $appointment->option_value;
                }

                if(isset($appointment->from_company_name)){
                    $datatemp['from_company_name'] = $appointment->from_company_name;
                }

                if(isset($appointment->audit_type_name)){
                    $datatemp['audit_type_name'] = $appointment->audit_type_name;
                }

                $data[] = $datatemp;
                //}
            }

            $data2 = array();

            foreach($data as $key=>$val) {
                $data2[] = (array) $val;
            }

            // Set pagination
            $current_page = 0;//$appointments->currentPage();
            $total_pages = 0;//ceil($appointments->total()/$appointments->perPage());

            return response()->json(array('success' => 'true', 'appointments' => $data2, 'company' => $company, 'current_page'=>$current_page, 'total_pages'=>$total_pages ), 200);
        } else {
            return response()->json(array('success' => 'true', 'appointments' => [], 'company' => [], 'message' => array('No appointments')), 200);
        }
    }

    /**
     * Get action item comments
     * @param type $appointment_id
     * @param type $action_id
     * @param AppointmentGetRequest $request
     * @return type
     */
    public function getActionComments ($appointment_id, $action_id, AppointmentGetRequest $request)
    {

        //get user id according to access token
        $user_id = Authorizer::getResourceOwnerId();

        // Mark commets as read
        $comments = $this->comment->getActionComments($appointment_id, $action_id, $user_id);
        $update_status = $this->comment->readComments($appointment_id, $action_id, $user_id);

        $data = [];
        // Set action item details.
        $action_item_data = [];

        $action_item_results = $this->appointment->getActionItemDetails($action_id);

        if(isset($action_item_results[0])){
            $action_item_data['id'] = $action_item_results[0]->id;
            $action_item_data['name'] = $action_item_results[0]->name;
        }else{
            $action_item_data = (object) [];
        }

        if($comments) {

            $data_formated = [];
            foreach($comments as $comment) {

                $created_time = new DateTime($comment->created_at);
                $location = ($comment->location == null) ? '': ' at '.$comment->location;
                $data[$comment->id]['id'] = $comment->id;
                $data[$comment->id]['date'] = $created_time->format('F jS, Y');
                $data[$comment->id]['time'] = $created_time->format('g:i A').$location;
                $data[$comment->id]['name'] = $comment->name;
                $data[$comment->id]['comment'] = $comment->content;
                $data[$comment->id]['status'] = ($comment->status == null) ? '1' : $comment->status;
                $data[$comment->id]['location'] = ($comment->location == null) ? '': $comment->location;
                if($comment->image_name != ''){
                    $data[$comment->id]['images'][] = Config::get('simplifya.BUCKET_IMAGE_PATH') . ltrim(Config::get('simplifya.ACTION_COMMENT_IMG_DIR'), "/"). '/' . $comment->image_name;
                }
            }

            foreach($data as $item){
                $item['images'] = isset($item['images']) ? array_values(array_unique($item['images'])) : [];
                $data_formated[] = $item;
            }

            return response()->json(array('success' => 'true', 'action_item'=>$action_item_data, 'data' => array_values($data_formated)), 200);
        } else {

            return response()->json(array('success' => 'true', 'action_item'=>$action_item_data, 'message' => array('No appointments')), 204);
        }
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
     * Save inspection data
     * @param InspectionSaveRequest $request
     * @return type
     */
    public function saveInspectionData(InspectionSaveRequest $request){
//        return response()->json(array('success' => 'false', 'message' => $request->appointment_id), 400);
        // Get appointment id
        $appointment_id = $request->appointment_id;

        // Prepare dataset
        $dataset = [
            'start_inspection'    => $request->start_time,
            'finish_inspection'   => $request->end_time,
            'start_latitude'      => $request->start_latitude,
            'start_longitude'     => $request->start_longitude,
            'finish_latitude'     => $request->finish_latitude,
            'finish_longitude'    => $request->finish_longitude,
            'report_status'       => $request->report_status,
            'verified_by'         => $request->verified_by
        ];

        // Update
        $status = $this->appointment->saveInspection($dataset, $appointment_id);

        $upload=$this->storeImages($request);
        if($upload){

            return response()->json(array('success' => 'true', 'message' => ['Audit data updated successfully!'], 'data' => $dataset), 200);
        }else{

            return response()->json(array('success' => 'false', 'message' => ['Something went wrong on uploading!']), 400);

        }

    }

    public function storeImages ($request)
    {

        $respond = [];

        // Get user id from access token
        $user_id = Authorizer::getResourceOwnerId();


        $entity_id = 0;
        $appointment_id = $request->appointment_id;

        if ($request->type == 'e_signature')
        {
            $entity_tag = 'e_signature';
        }

        // Get files array
        $images = Input::file('images');

        if (!empty ($images))
        {


            foreach ($images as $image)
            {
                // Get file name
                $filename = $image->getClientOriginalName();

                // Check record exists
                $validation_status = $this->upload->checkImageExist($entity_tag, $filename);
                if(!$validation_status){
                    // Upload new files
                    $result['image'] = $image;

                    $image_id = $this->imageUploadToAws($entity_id, $image, $user_id, $appointment_id, $entity_tag,true);
                    if ($image_id != '')
                    {
                        // Upload success
                        $result['image'] = true;
                    } else{
                        // Upload fail
                        $result['image'] = false;
                        return response()->json(array('success' => 'true', 'message' => ['Something went wrong! Please try again']), 400);
                    }
                    $respond[] = $result;
                }else{
                    // Record already exist
                    return response()->json(array('success' => 'true', 'message' => ['Record already exists!']), 200);
                }
            }

            return response()->json(array('success' => 'true', 'data'=>['appointment_id'=>$appointment_id, 'entity_id'=>$entity_id], 'message' => ['Record added successfully!']), 200);

        }else{
            return response()->json(array('success' => 'false', 'message' => array('no images')), 400);
        }

        return response()->json(array('success' => 'false', 'message' => ['Something went wrong!']), 400);

    }

    /**
     *
     */
    public function imageUploadToAws($entity_id, $images, $user_id, $appointment_id = '', $entity_tag, $org_name='')
    {
        //Get Amazon API instance
        $s3 = AWS::createClient('s3');

        $pic_id = 0;
        $file = $images;
        $image_formats = array('jpeg', 'jpg', 'png');

        if(!empty($file)) {
            // Create name for file
            $filename = $file->getClientOriginalExtension(); //getClientOriginalName();
            $path = $file->getRealPath();
            $fileExt = $filename;

            try{
                if($org_name === true){
                    $filename = $file->getClientOriginalName();

                }else{
                    $generatedName = uniqid().$user_id.uniqid();
                    $filename = $generatedName.'.'.$filename;
                }
                // Upload an object to Amazon S3
                $result = $s3->putObject(array(
                    'Bucket'        => Config::get('aws.bucket'),
                    'Key'           => Config::get('aws.ACTION_COMMENT_IMG_DIR').$filename,
                    'SourceFile'    => $path,
                    'body'          => $path,
                    'ContentType'   => $file->getClientMimeType(),
                    'ACL'           => 'public-read'
                ));

                if(isset($result['ObjectURL'])) {
                    // Save image in image table
                    //$pic_id = $this->upload->setFile($user_id, $filename, Config::get('simplifya.UPD_TYPE_ACTION_COMMENT_PIC'), $entity_tag, $entity_id, $filename, $appointment_id);
                    $pic_id = $this->upload->setFile($user_id, $filename, $entity_tag,$entity_tag, $entity_id, $appointment_id);

                    return $pic_id;
                }

            } catch (Exception $ex) {
                $messages = Config::get("messages.FILE_UPLOAD_ERROR");
                return array('success' => 'false', 'error' => $messages);
                //return response()->json(array('success' => 'false', 'error' => $messages), 400);
            }
        }
    }
}
