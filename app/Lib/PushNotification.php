<?php namespace App\Lib;

use Illuminate\Support\Facades\Config;


class PushNotification {    
   
   /**
    * Sending push notification for ios/android
    * @param type $deviceToken
    * @param type $message
    * @param type $data
    * @param type $type
    */
   public function sendNotifications($deviceTokens, $message, $data, $type=''){
      
      if($type == 'ios'){
         
         //== Send IOS notifications
         
         // Put your private key's passphrase here:
         $passphrase = '';
         // Cartificate path
         $certificate_path = Config::get("pushnotification.CERTIFICATE_IOS");

         $ctx = stream_context_create();
         stream_context_set_option($ctx, 'ssl', 'local_cert', $certificate_path);
         stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

         // Open a connection to the APNS server
         $fp = stream_socket_client(Config::get("pushnotification.PUSH_GATEWAY_IOS"), $err, $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

         if (!$fp) exit("Failed to connect: $err $errstr" . PHP_EOL);

         // Create the payload body
         $body['aps'] = array(
               'alert' => $message,
               'sound' => 'default'
            );

         // add other details
         $body['data'] =  $data;
         // Encode the payload as JSON
         $payload = json_encode($body);
         // Build the binary notification
         //$countss = 0;
         foreach($deviceTokens as $deviceToken){
            //$deviceTokenStr =  'f6ab7514137b0faeef94b34e87b2a4a8606325444f889c7757cabce04c25b7e6';
            //$deviceTokenStr = (string) $deviceToken;
             //$nl = "
             //";
             //$filel = fopen("test_apns.txt","a+");
             //fwrite($filel, $deviceToken.$nl);
             //fclose($filel);

            $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
            // Send it to the server
            $result = fwrite($fp, $msg, strlen($msg));
            //$countss++;
         }
         
         // Close the connection to the server
         fclose($fp);
         
         //print_r($deviceTokens);
         //echo 'C-'.$countss;
      
      }else if($type == 'android'){

          //$nl = "
           //  ";
          //$file2 = fopen("test_apns.txt","a+");
          //fwrite($file2, print_r($deviceTokens,1).$nl);
          //fclose($file2);
         
         //== Send Android notifications
         
         $registrationIds = $deviceTokens;
         // prep the bundle
         $msg = array(
                  'message'      => $message,
                  'title'        => $message,
                  'subtitle'     => $message,
                  'tickerText'	=> $message,
                  'vibrate'      => 1,
                  'sound'        => 1,
                  //'largeIcon'    => 'large_icon',
                  //'smallIcon'    => 'small_icon'
               );
         
         $msg = array_merge($msg, $data);
         
         $fields = array(
                     'registration_ids' 	=> $registrationIds,
                     'data'               => $msg
                  );
         
         // Set headers
         $headers = array(
            'Authorization: key=' . Config::get('pushnotification.API_ACCESS_KEY'),
            'Content-Type: application/json'
         );

         $ch = curl_init();
         curl_setopt( $ch,CURLOPT_URL, Config::get('pushnotification.PUSH_GATEWAY_ANDROID'));
         curl_setopt( $ch,CURLOPT_POST, true );
         curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
         curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
         curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
         curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
         $result = curl_exec($ch );
         curl_close( $ch );
         
      }
   }
}
