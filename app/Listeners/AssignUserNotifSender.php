<?php

namespace App\Listeners;

use App\Events\AssignUserNotifRequest;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Lib\PushNotification;
use App\Repositories\UsersRepository;
use Illuminate\Support\Facades\Config;

class AssignUserNotifSender
{
   private $user;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(UsersRepository $user)
    {
       $this->user = $user;
    }

    /**
     * Handle the event.
     *
     * @param  AssignUserNotifRequest  $event
     * @return void
     */
    public function handle(AssignUserNotifRequest $event)
    {
         $users = $event->userNotifHelper->users;
         $action_item_id = $event->userNotifHelper->action_item_id;
         $appointment_id = $event->userNotifHelper->appointment_id;
        
         // Create notification data
         $data = array(
                        'action_item_id'        => $action_item_id,
                        'appointment_id'     => $appointment_id,
                        'notification_type'  => Config::get('pushnotification.NOTIFICATION_TYPE_ASSIGN_ACTION_ITEM'),
                     );
         
         // Default message
         $message = Config::get('messages.PUSH_NOTIF_ASSIGN_USR_MSG_DEFAULT');
         
         // Get user device tokens
         $tokens = $this->user->getDeviceTokens($users);
         
         $device_token_ios = [];
         $device_token_android = [];
         
         foreach($tokens as $token){
            if($token->device_type == 'ios'){
               $device_token_ios[] = $token->device_token;
            }else  if($token->device_type == 'android'){
               $device_token_android[] = $token->device_token;
            }
         }

         // Send notifications
         $notification = new PushNotification();
         // for ios
         if(!empty($device_token_ios)){
            //$notification->sendNotifications($device_token_ios, $message, $data, 'ios');
         }
         // for android
         if(!empty($device_token_android)){
            $notification->sendNotifications($device_token_android, $message, $data, 'android');
         }
    }
}
