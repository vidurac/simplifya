<?php
/**
 * Created by PhpStorm.
 * User: Nishan
 * Date: 5/6/2016
 * Time: 3:38 PM
 */

namespace App\Repositories;

use Bosnadev\Repositories\Eloquent\Repository;
use App\Models\Image;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class UploadRepository extends Repository
{
    /**
     * @return string
     */
    public function model()
    {
        return 'App\Models\Image';
    }

    /**
     * Upload image
     * @param $user_id
     * @param $filename
     * @param $type
     * @return mixed
     */
    public function setFile($user_id, $filename, $type, $entity_tag, $entity_id, $appointment_id=null){

        // Add new profile image to tables
        $data       = array(
            'is_deleted'=> 0,
            'name'      => $filename,
            'entity_tag'=> $entity_tag,
            'entity_id' => $entity_id,
            'type'      => $type,
            'appointment_id' => $appointment_id,
            'created_by'=> $user_id
        );
        $result     = Image::create($data);
        $file_id    = $result->id;
        return $file_id;
    }
    
    /**
     * Check image already exists
     * @param $entity_tag
     * @param $image
     * @return $results
     */
    public function checkImageExist($entity_tag, $image){
       return $results = $this->model->where('entity_tag', '=', $entity_tag)->where('name', '=', $image)->count();
    }

    /**
     * Get Image Url
     * @param $imagePath
     * @param $directory
     * @param $image
     * @return string
     */
    public function getImageUrl($imagePath, $directory, $image){
        return $imagePath.$directory.$image;
    }


    /**
     * Upload profile picture
     * @param $user_id
     * @param $filename
     * @param $entity_tag
     * @param $entity_id
     * @param $type
     * @return mixed
     */
    public function setProfilePicture($user_id, $filename, $type, $entity_tag, $entity_id){

        // Add new profile image to tables
        $data = array(
            'is_deleted'=> 0,
            'name'      => $filename,
            'entity_tag'=> $entity_tag,
            'entity_id' => $entity_id,
            'type'      => $type,
            'created_by'=> $user_id
        );

        $image = $this->model->where("entity_tag", $entity_tag)->where("entity_id", $entity_id)->where("type", $type)->first();

        if($image){
            $this->update($data, $image->id);
            return $filename;
        }
        else{
            $result = $this->create($data);
            return $result->name;
        }
    }

}