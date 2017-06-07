<?php namespace App\Repositories;

use Bosnadev\Repositories\Contracts\RepositoryInterface;
use Bosnadev\Repositories\Eloquent\Repository;
use Illuminate\Support\Facades\DB;

class DeviceTokenRepository extends Repository {
    
   public function model() {
      return 'App\Models\DeviceToken';
   }
    
   public function isExistDeviceToken($token, $device_type){
      $result =  DB::table('device_tokens')
              ->where('device_token', '=', $token)
              ->where('device_type', '=', $device_type)
              ->count();
      return ($result > 0);
   }
        
   public function updateTokenUser($device_token, $user_id, $device_type){
       return DB::table('device_tokens')
               ->where('device_token', '=', $device_token)
               ->where('device_type', '=', $device_type)
               ->update(array('user_id' => $user_id));
   }
        
   public function insertDeviceToken($device_token, $user_id, $time, $device_type){
      $dataset = array(
                     'user_id'      => $user_id, 
                     'device_token' => $device_token, 
                     'device_type'  => $device_type,
                     'created_at'   => $time,
                     'updated_at'   => $time
                    );
      return $status = DB::table('device_tokens')->insert($dataset);
   }
        
   public function getAllTokensByUserId($user_id) {
      return DB::table('device_tokens')
               ->select('device_token')
               ->where('user_id', '=', $user_id)
               ->get();
   }

}
