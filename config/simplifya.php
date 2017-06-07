<?php
/**
 * Created by PhpStorm.
 * User: Nishan
 * Date: 5/25/2016
 * Time: 11:46 PM
 */

return [

    /**
     * company status types
     */
    'INPROGRESS'    => 0,
    'PENDING'       => 1,
    'ACTIVE'        => 2,
    'REJECT'        => 3,
    'INACTIVE'      => 4,
    'EXPIRE'        => 5,
    'SUSPEND'       => 6,

    /**
     * entity type
     */
    'Simplifya'            => 1,
    'MarijuanaBusiness'    => 2,
    'ComplianceCompany'    => 3,
    'GovernmentEntity'     => 4,


    /**
     * user group by entity type
     */
    'MasterAdmin'       => 1,
    'MjbMasterAdmin'    => 2,
    'MjbManager'        => 3,
    'MjbEmployee'       => 4,
    'CcMasterAdmin'     => 5,
    'CcInspector'       => 6,
    'GeMasterAdmin'     => 7,
    'GeInspector'       => 8,

    /**
     * license status types
     */
    'LICENSE_DELETED'       => 0,
    'LICENSE_ACTIVE'        => 1,
    'LICENSE_INACTIVE'      => 2,

    /**
     * Company Types on Subscription Manage
     */
    'COMPLIANCE_COMPANY_TYPE'   => 3,
    'GOVERNMENT_ENTITY_TYPE'    => 4,
    'MARIJUANA_COMPANY_TYPE'    => 2,

    /**
     * Report status types
     */
    'REPORT_PENDING'         => 0,
    'REPORT_PENDING_TXT'     => "Pending",
    'REPORT_COMPLETED'       => 1,
    'REPORT_COMPLETED_TXT'   => "Synced",
    'REPORT_STARTED'         => 2,
    'REPORT_STARTED_TXT'     => "Started",
    'REPORT_FINALIZED'       => 3,
    'REPORT_FINALIZED_TXT'   => "Finalized",

    /**
     *  Appointment status types
     */
    'APPOINTMENT_ACTIVE'            => 1,
    'APPOINTMENT_ACTIVE_TXT'       => "Active",
    'APPOINTMENT_CANCELED'          => 2,
    'APPOINTMENT_CANCELED_TXT'      => "Canceled",

    /**
     * Email Notification types
     */
    'MJB_SIGNED_UP_NO_PAYMENT' => 1,
    'MJB_PAYMENT_MADE_NO_AUDIT_ADDED' => 2,

    /**
     * Support email cron job interval
     */
    'MJB_SIGNED_UP_NO_PAYMENT_INTERVAL' => 48,
    'MJB_PAYMENT_MADE_NO_AUDIT_ADDED_INTERVAL' => 48,
    'SUPPORT_EMAILS_CRON_JOB_MAX_INTERVAL' => 120,

    /**
     * Image resize
     */
    'IMG_SIZE_COMMENT'      => "",

    /**
     * Upload type
     */
    'UPD_TYPE_PROFILE'                   => 'profile',
    'UPD_TYPE_COMPANY'                   => 'company',
    'UPD_TYPE_ACTION_COMMENT_PIC'        => 'comment_photo',
    'UPD_TYPE_QUESTION_COMMENT_PIC'      => 'question_photo',



    /***
     * Profile image
     **/
    'BUCKET_IMAGE_PATH'   => "https://simplifyas3.s3.amazonaws.com/",

    /**
     * S3 Bucket URL
     */
    'BUCKET_URL' => 'https://simplifyas3.s3.amazonaws.com/',


    'BUCKET_IMAGE_URL' => 'https://s3.amazonaws.com/',

    /**
     * S3 bucket folder structure
     */

    'PROFILE_IMG_DIR'           => "/profile",
    'ACTION_COMMENT_IMG_DIR'      => "/comment",
    'USER_PROFILE_IMG_DIR'           => "profile",
    'COMPANY_LOGO_IMG_DIR'           => "company",

    /*
     * Simplifiya email
     */
    'SIMPLIFIYA_EMAIL' => "noreply@simplifya.com",
    'SIMPLIFIYA_SUPPORT_EMAIL' => env('SIMPLIFIYA_SUPPORT_EMAIL', "support@simplifya.com"),
    'COMPANY' => "Simplifya",
    //'admin_email' => 'john.m@ceylonsolutions.com',
    //'admin_email' => 'manthindra.p@ceylonsolutions.com',
    'admin_email' => 'john@simplifya.com',

    /*
     * Default images
     */
    'DEFAULT_PROFILE_IMAGE' => 'default-profile-image.jpg',

    'IMG_SIZE_USER'          => "resize_user",
    'IMG_WATERMARK_TMP_DIR'  => "images/image_temp/",

    'RESIZE_USER_WIDTH'      => 200,
    'RESIZE_USER_HEIGHT'     => 0,
    'RESIZE_USER_CROP_WIDTH' => 200,
    'RESIZE_USER_CROP_HEIGHT'=> 200,
    'RESIZE_USER_CROP_START_X'=> 0,
    'RESIZE_USER_CROP_START_Y'=> 0,

    /*
     *  Google api key
     */
    'GoogleApiKey' => 'AIzaSyAZbiTNTnrOMUuuUroDe7kF_r4qvce-FLU',
    //'STRIPE_KEY'    => 'sk_test_rEI3IHfp9TDIlV8JcRhPQ5i8',
    'STRIPE_KEY'    => env('STRIPE_SECRET', false),
    /**
     * Audit report header
     */
    'AUDIT_REPORT_CLASSIC_VIEW' => 1,

    /*
     *  Question Laws
     */
    
    'LAW_TYPES'=>[
        '1'=>'Federal',
        '2'=>'State',
        '3'=>'Local Jurisdiction',
    ],

    /*
     *  Applicability Types
     */

    'APPLICABILITY_TYPES'=>[
        '1'=>'Federal',
        '2'=>'State',
        '3'=>'Local Jurisdiction',
    ],

    /*
    *  Applicability Groups
    */

    'APPLICABILITY_GROUPS'=>[
        '1'=>'Edibles',
        '2'=>'Water-based concentrates',
        '3'=>'Solvent-based concentrates',
    ]
];