<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\AnswerStoreRequest;
use App\Repositories\AppointmentQuestionRepository;
use App\Repositories\UploadRepository;
use App\Repositories\AppointmentActionItemCommentsRepository;
use Illuminate\Support\Facades\Input;
use AWS;
use DB;
use Illuminate\Support\Facades\Config;
use Authorizer;

class AnswerController extends Controller
{
    private $appointment_question, $upload, $appointment_action;

    public function __construct(AppointmentQuestionRepository $appointment_question, UploadRepository $upload, AppointmentActionItemCommentsRepository $appointment_action)
    {
        $this->appointment_question = $appointment_question;
        $this->upload = $upload;
        $this->appointment_action = $appointment_action;
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
     * @param  \Illuminate\Http\AnswerStoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AnswerStoreRequest $request)
    {
        $appointment_id = $request->appointment_id;
        $question_id = $request->question_id;
        $answer_id = $request->answer_id;
        $q_comment = $request->question_comment;
        $q_images = $request->question_images;
        $action_item_id = $request->action_item_id;
        $action_comment = $request->action_comment;
        $action_image = $request->action_image;

        //get user id according to access token
        //$user_id = Authorizer::getResourceOwnerId();
        $user_id = 4;
        $appointment = $this->appointment_question->checkAppointmentQuestion($appointment_id, $question_id);
        if($appointment) {
            $status = false;
            // Start DB transaction
            DB::beginTransaction();
            try {
                $response = $this->appointment_question->storeQuestionAnswers($appointment_id, $question_id, $answer_id, $q_comment, $user_id);

                if($response) {
                    $entity_tag = 'answer_comment_photo';
                    foreach($q_images as $q_image) {

                        $this->imageUploadToAws($appointment_id, $q_image, $user_id, $appointment_id, $entity_tag);
                    }
                    if($action_item_id != '') {
                        $result =$this->storeActionItems($appointment_id, $action_item_id, $user_id, $action_comment, $action_image);
                        if($result['success'] = 'true') {
                            // All good
                            DB::commit();
                            return response()->json(array('success' => 'true', 'message' => array('Appointment action added successfully')), 200);
                        } else {
                            DB::rollback();
                            return response()->json(array('success' => 'false', 'message' => array('Appointment action insert failed')), 422);
                        }
                    } else {
                        // All good
                        DB::commit();
                    }
                } else {
                    DB::rollback();
                    return response()->json(array('success' => 'false', 'message' => array('Appointment answer update failed')), 422);
                }
            } catch(Exception $ex){
                // Someting went wrong
                DB::rollback();
            }
        } else {
            return response()->json(array('success' => 'false', 'message' => array('No appointments')), 400);
        }

    }

    public function storeQuestions(Request $requests)
    {
        
        $respond = [];
        //get user id according to access token
        $user_id = Authorizer::getResourceOwnerId();
        
        $dataSet = json_decode($requests->data);
        
        //$questions = $request->input('questions');
//        $dataSet = array (array (
//            'question_id' => 26,
//            'appointment_id' => 38,
//            'master_answer_id' => 10,
//            'comment' => 'test question',
//            'items' => array (array (
//                'action_item_id' => 58,
//                'comment' => 'item1',
//                'image_available' => 0
//                ), array (
//                'action_item_id' => 58,
//                'comment' => '',
//                'image_available' => 1
//                )
//                )
//            )
//        );

        if (!empty ($dataSet))
        {
            foreach ($dataSet as $key => $set) 
            {
                $question_id    = $set->question_id;
                $appointment_id = $set->appointment_id;    
                $answer_id      = $set->master_answer_id;
                $q_comment      = isset($set->comment) ? $set->comment : '';

                $result['question_id']    = (integer) $question_id;
                $result['appointment_id'] = (integer) $appointment_id;
                $result['type']           = 'q';
                $result['status']         = false;
                
                $appointment = $this->appointment_question->checkAppointmentQuestion($appointment_id, $question_id);

                if($appointment) 
                {   
                    // Start DB transaction
                    DB::beginTransaction();
                    try {
                        $response = $this->appointment_question->storeQuestionAnswers($appointment_id, $question_id, $answer_id, $q_comment, $user_id);

                        if($response) 
                        {
                            $result['id']     = (integer) $appointment[0]['id'];
                            $result['status'] = true;
                            $respond[] = $result;

                            if(!empty ($set->items)) 
                            {
                                foreach ($set->items as $key => $item) 
                                {
                                    $data = array('appointment_id' => (integer)$appointment_id,
                                            'question_action_item_id' => (integer) $item->action_item_id,
                                            'user_id' => $user_id,
                                            'created_by' => $user_id,
                                            'status'    => 1,
                                            'content' => $item->comment
                                            );
                                    $appointment_action = $this->appointment_action->create($data);
                                    
                                    
                                    
                                    $action_id = $appointment_action->id;
                                    $result['question_id']    = (integer) $question_id;
                                    $result['action_item_id'] = (integer) $item->action_item_id;
                                    $result['type']   = 'a';
                                    if($action_id != '') {
                                        $result['id']     = (integer) $action_id;
                                        $result['status'] = true;
                                        $respond[]        = $result; 
                                        // All good
                                        //DB::commit();
                                        
                                    } else {
                                        $result['status'] = false;
                                        $result['id']     = '';
                                        $respond[]        = $result; 
                                        DB::rollback();
                                        
                                    }    
                                } 
                                
                            }
                            
                            // All good
                            DB::commit();
                            
                        } else {
                            $result['status'] = false;
                            $respond[] = $result;
                            DB::rollback();
                        }
                    } catch(Exception $ex){
                        $result['status'] = false;
                        $respond[] = $result;
                        DB::rollback();
                    }
                }
            }
            
            if($result['status']){
               return response()->json(array('success' => 'true', 'data' => $respond, 'message' => ['Record saved successfully!']), 200);
            }else{
               return response()->json(array('success' => 'false', 'error' => ["Error saving records!"]), 200);
            }
            
        } else
        {
            return response()->json(array('success' => 'false', 'message' => array('No qustions')), 400);    
        }
    }

    public function storeImages (Request $request)
    {

      $respond = [];

      // Get user id from access token
      $user_id = Authorizer::getResourceOwnerId();
        
      $entity_id = $request->id;
      $appointment_id = $request->appointment_id;

      if ($request->type == 'question_photo')
      {
         $entity_tag = 'question_photo';
      } elseif ($request->type == 'comment_photo')
      {
         $entity_tag = 'comment_photo';
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
               $image_id = $this->imageUploadToAws($entity_id, $image, $user_id, $appointment_id, $entity_tag, true);
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
                    $pic_id = $this->upload->setFile($user_id, $filename, $entity_tag, $entity_tag, $entity_id, $filename, $appointment_id);
                    
                    return $pic_id;
                }

            } catch (Exception $ex) {
                $messages = Config::get("messages.FILE_UPLOAD_ERROR");
                return array('success' => 'false', 'error' => $messages);
                //return response()->json(array('success' => 'false', 'error' => $messages), 400);
            }
        }
    }

    public function storeActionItems($appointment_id, $action_item_id, $user_id, $action_comment, $action_images)
    {
        $data = array('appointment_id' => $appointment_id,
            'question_action_item_id' => $action_item_id,
            'user_id' => $user_id,
            'created_by' => $user_id,
            'status'    => 1,
            'content' => $action_comment
        );
        $appointment_action = $this->appointment_action->create($data);
        if($appointment_action) {
            $entity_tag = 'action_item_comment_photo';
            foreach($action_images as $action_image) {
                $this->imageUploadToAws($appointment_action->id, $action_image, $user_id, $appointment_id, $entity_tag);
            }
            return array('success' => 'true', 'message' => 'Appointment action added successfully');
        } else {
            return array('success' => 'false', 'message' => 'Appointment action insert failed');
            //return response()->json(array('success' => 'false', 'message' => 'Appointment action insert failed'), 422);
        }
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
}
