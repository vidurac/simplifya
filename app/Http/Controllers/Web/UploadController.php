<?php

namespace App\Http\Controllers\Web;

use App\Repositories\UploadRepository;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Aws\Laravel\AwsFacade as AWS;
use Imagick;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class UploadController extends Controller
{

    /*
    * Initialize private variables
    */
    private $upload;

    /**
     * UploadController constructor.
     * @param UploadRepository $upload
     */
    public function __construct(UploadRepository $upload){
        $this->upload = $upload;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Get user_id
        $user_data  = Auth::user();
        $user_id    = $user_data->id;
        $user_company_id=$user_data->company_id;

        // Acceptable file formats
        $image_formats = array('jpeg', 'jpg', 'png', 'gif');

        // Get Amazon API instance
        $s3 = AWS::createClient('s3');

        $file_ids   = array();
        $file_names = array();

        /*
         * Declare and Initialize variables
         */
        $file = $request->image;
        $x    = $request->x;
        $y    = $request->y;
        $w    = $request->w;
        $h    = $request->h;
        $category=$request->catagory;

        $bucket_path = $request->bucket_path;
        $upload_type = $request->upload_type;
        $config_type = 'DEFAULT';//$request->config_type;
        $genratedName  = uniqid() . $user_id . uniqid();
        $extension = '';

        $img = $file;

        if(strpos($img, "data:image/jpeg;base64,") !== false){
            $img = str_replace('data:image/jpeg;base64,', '', $img);
            $extension = 'jpg';
        }else if(strpos($img, "data:image/png;base64,") !== false){
            $img = str_replace('data:image/png;base64,', '', $img);
            $extension = 'png';
        }else if(strpos($img, "data:image/gif;base64,") !== false){
            $img = str_replace('data:image/gif;base64,', '', $img);
            $extension = 'gif';
        }else{
            $messages = Config::get("messages.FILE_UPLOAD_ERROR");
            return array('success' => 'false', 'error' => $messages);
        }

        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);

        $tmp_path = Config::get('simplifya.IMG_WATERMARK_TMP_DIR').$genratedName.'_.'.$extension;
        file_put_contents($tmp_path, $data);

        if($file != '') {

            /*
             * Declare and Initialize variables
             */
            $fileExt    = $extension;
            $path       = $tmp_path;
            $bucket     = "";
            $type       = "";
            $fileEdited = "";

            $f = finfo_open();

            $mimeType   = finfo_buffer($f, $file, FILEINFO_MIME_TYPE);
            $filename = $genratedName . '.' . $fileExt;

            if(in_array($fileExt, $image_formats)){
                $bucket  = Config::get("aws.bucket").$bucket_path;
                $type    = $upload_type;
                $fileEdited = $this->addWatermark($user_id, $file, $config_type, $extension, $tmp_path, $x, $y, $w, $h);
                $path = $fileEdited;
            }

            if($tmp_path != '') unlink($tmp_path);

            try {
                $filename = $genratedName . '.' . $filename;
                // Upload an object to Amazon S3
                $result = $s3->putObject(array(
                    'Bucket' => Config::get('aws.bucket'),
                    'Key' => ($category=='company')?Config::get('aws.COMPANY_LOGO_IMG_DIR').$filename:Config::get('aws.PROFILE_IMG_DIR') . $filename,
                    'SourceFile' => $path,
                    'body' => $path,
                    'ContentType' => 'image/jpg',
                    'ACL' => 'public-read'
                ));

                if(isset($result['ObjectURL'])) {
                    // Save user profile image in photo table
                    $file_id    = ($category=='company')?$this->upload->setProfilePicture($user_id, $filename, Config::get('simplifya.UPD_TYPE_COMPANY'), "company", $user_company_id):$this->upload->setProfilePicture($user_id, $filename, Config::get('simplifya.UPD_TYPE_PROFILE'), "profile", $user_id);
                    $file_names['fileid'] = $file_id;
                    $file_names['filename'] = $filename;
                }

            } catch(Exception $e) {
                $messages = Config::get("messages.FILE_UPLOAD_ERROR");
                return array('success' => 'false', 'error' => $messages);
            }

        }

        $image = ($category=='company')?$this->upload->findWhere(array("entity_tag" => "company", "entity_id" => $user_company_id, "type" => "company"))->last():$this->upload->findWhere(array("entity_tag" => "profile", "entity_id" => Auth::user()->id, "type" => "profile"))->last();

        if(empty($image)){

            $imageUrl = $this->upload->getImageUrl(Config::get('simplifya.BUCKET_IMAGE_PATH'), Config::get('aws.PROFILE_IMG_DIR'), Config::get('aws.PROFILE_DEFAULT_IMAGE'));
        }
        else{
            $imageUrl = $this->upload->getImageUrl(Config::get('simplifya.BUCKET_IMAGE_PATH'), ($category=='company')?Config::get('aws.COMPANY_LOGO_IMG_DIR'):Config::get('aws.PROFILE_IMG_DIR'), $image->name);
        }

        if($category=='company'){
            Session::put('company_image', $imageUrl);

        }else{

            Session::put('profile_image', $imageUrl);
        }

        $messages = Config::get("messages.FILE_UPLOAD_SUCCESS");
        return array('success' => 'true', 'message' => $messages, 'data' => $file_names);



    }

    /**
     * Add watermark
     *
     * @param $user_id
     * @param $file
     * @param string $type
     * @param $extension
     * @param $t_path
     * @param $cropx
     * @param $cropy
     * @param $cropw
     * @param $croph
     * @return string
     */
    public function addWatermark($user_id, $file, $type ='', $extension, $t_path, $cropx, $cropy, $cropw, $croph){


        $resize_user = array(
            'width'        => Config::get('simplifya.RESIZE_USER_WIDTH'),
            'height'       => Config::get('simplifya.RESIZE_USER_HEIGHT'),
            'crop_width'   => Config::get('simplifya.RESIZE_USER_CROP_WIDTH'),
            'crop_height'  => Config::get('simplifya.RESIZE_USER_CROP_HEIGHT'),
            'crop_start_x' => Config::get('simplifya.RESIZE_USER_CROP_START_X'),
            'crop_start_y' => Config::get('simplifya.RESIZE_USER_CROP_START_Y')
        );

        $resize_default = array(
            'width'        => $cropw,
            'height'       => $croph,
            'crop_width'   => $cropw,
            'crop_height'  => $croph,
            'crop_start_x' => $cropx,
            'crop_start_y' => $cropy
        );

        if($type == Config::get('simplifya.IMG_SIZE_USER')){
            $resize = $resize_user;
        }else{
            $resize = $resize_default;
        }

        $fileExt = $extension;
        $path    = $t_path;
        $watermark = imagecreatefrompng('images/watermark/watermark.png');
        $im = '';
        $tmp_path = Config::get('simplifya.IMG_WATERMARK_TMP_DIR').$user_id.uniqid().'.'.$fileExt;
        $imagick = new Imagick($path);

        $imagick->cropImage ($resize['crop_width'], $resize['crop_height'], $resize['crop_start_x'], $resize['crop_start_y']);
        $imagick->enhanceImage();
        $imagick->writeImage($tmp_path);

        if($fileExt == 'jpeg' or $fileExt == 'jpg'){
            $im = imagecreatefromjpeg($tmp_path);
        }elseif($fileExt == 'png'){
            $im = imagecreatefrompng($tmp_path);
        }elseif($fileExt == 'gif'){
            $im = imagecreatefromgif($tmp_path);
        }else{
            return '';
        }

        switch ($fileExt)
        {
            case "png":
                // integer representation of the color black (rgb: 0,0,0)
                $background = imagecolorallocate($im, 0, 0, 0);
                // removing the black from the placeholder
                imagecolortransparent($im, $background);
                /* turning off alpha blending (to ensure alpha channel information is preserved, rather than removed (blending with the rest of the image in the form of black)) imagealphablending($im, false); turning on alpha channel information saving (to ensure the full range of transparency is preserved) */
                imagesavealpha($im, true);
                break;
            case "gif":
                // integer representation of the color black (rgb: 0,0,0)
                $background = imagecolorallocate($im, 0, 0, 0);
                // removing the black from the placeholder
                imagecolortransparent($im, $background);
                break;
        }

        // Set the margins for the stamp and get the height/width of the stamp image
        $marge_right = 15;
        $marge_bottom = 25;
        $sx = imagesx($watermark);
        $sy = imagesy($watermark);
        $imgx = imagesx($im);
        $imgy = imagesy($im);
        $newwidth = 1200;
        $newheight = 500;
        $thumb = imagecreatetruecolor($newwidth, $newheight);

        // Copy the stamp image onto our photo using the margin offsets and the photo width to calculate positioning of the stamp.
        imagecopy($im, $watermark, imagesx($im) - $sx - $marge_right, imagesy($im) - $sy - $marge_bottom, 0, 0, 0, 0);

        if(!file_exists(Config::get('simplifya.IMG_WATERMARK_TMP_DIR'))){
            mkdir(Config::get('simplifya.IMG_WATERMARK_TMP_DIR'), 0777);
        }

        // Output and free memory
        if($fileExt == 'jpeg' or $fileExt == 'jpg'){
            header('Content-type: image/jpeg');
            imagejpeg($im, $tmp_path);
        }elseif($fileExt == 'png'){
            header('Content-type: image/png');
            imagepng($im, $tmp_path);
        }elseif($fileExt == 'gif'){
            header('Content-type: image/gif');
            imagegif($im, $tmp_path);
        }else{
            return '';
        }
        imagedestroy($im);
        return $tmp_path;
    }
}
