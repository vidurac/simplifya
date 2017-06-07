<?php
/*=================================================
 * This helper was created to maintain a common
 * data values including common arrays,variables
 *================================================*/

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class Helpers{

    public static function get_avatar($avatar_id){
        $file_name = DB::table('images')->where('id', '=', $avatar_id)->get();
        if(!empty($file_name) and $file_name[0]->type == Config::get('simplifiya.UPD_TYPE_FB_PROFILE')){
            return $file_name[0]->path;
        }elseif(!empty($file_name)){
            return Config::get("simplifiya.BUCKET_URL").Config::get("aws.bucket").Config::get("simplifiya.PROFILE_IMG_DIR").'/'.$file_name[0]->path;
        }else{
            return Config::get('simplifiya.PROFILE_IMG_DEFAULT_DIR');
        }
    }

    public static function get_group_cover($avatar_id){
        $file_name = DB::table('images')->where('id', '=', $avatar_id)->get();
        if(!empty($file_name)){
            return Config::get("simplifiya.BUCKET_URL").Config::get("aws.bucket").Config::get("simplifiya.GROUP_IMAGE_DIR").'/'.$file_name[0]->path;
        }else{
            return Config::get('simplifiya.PROFILE_IMG_DEFAULT_DIR');
        }
    }

    public static function get_user_type($user_id)
    {
        $user_type = DB::table('users')
            ->join('master_user_groups', 'master_user_group_id', '=', 'master_user_groups.id')
            ->where('users.id', $user_id)
            ->select('master_user_groups.name')
            ->get();
        if(!empty($user_type)){
            return $user_type[0]->name;
        }else{
            return Config::get('messages.USER_GROUP_ERR');
        }
    }

    public static function get_entity_type($company_id)
    {
        $get_entity_type = DB::table('companies')
                           ->where('id', $company_id)
                           ->select('entity_type')
                           ->get();
        return $get_entity_type[0]->entity_type;
    }
}