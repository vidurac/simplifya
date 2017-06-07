<?php
// Push notification config

return [
    
    // Notification type
    'NOTIFICATION_TYPE_ASSIGN_ACTION_ITEM'  => 'ASSIGN_ACTION_ITEM',
    'NOTIFICATION_TYPE_ADD_COMMENT'         => 'ADD_COMMENT',
    'NOTIFICATION_TYPE_ADD_APPOINTMNT'      => 'ADD_APPOINTMENT',
    'NOTIFICATION_TYPE_VERIFY'              => 'ACCOUNT_VERIFICATION',
    
    /*==( IOS )==*/
   
    //Push Notification certufucate parth
   
    //'CERTIFICATE_IOS' => '../keys/apns-dev.pem',
    'CERTIFICATE_IOS' => '../keys/SimplifyaCert.pem',

    //'PUSH_GATEWAY_IOS' => 'ssl://gateway.sandbox.push.apple.com:2195',
    'PUSH_GATEWAY_IOS' => 'ssl://gateway.push.apple.com:2195',
   
    /*==( ANDROID )==*/
    
    // API key Google GCM
    //'API_ACCESS_KEY' => 'AIzaSyD1Y-YDMpsd2MQiBIdAUb5oZwrPJRLzqgQ',
    'API_ACCESS_KEY' => 'AIzaSyD0uJO4OLBdAiYslYX3cAh1q6x49zURur0',
   
    // push gateway android
    //'PUSH_GATEWAY_ANDROID'  => 'https://android.googleapis.com/gcm/send',
    'PUSH_GATEWAY_ANDROID'  => 'https://fcm.googleapis.com/fcm/send',
    
   
];
