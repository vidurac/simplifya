<?php

use Aws\Laravel\AwsServiceProvider;

return [

    /*
    |--------------------------------------------------------------------------
    | Your AWS Credentials
    |--------------------------------------------------------------------------
    |
    | In order to communicate with an AWS service, you must provide your AWS
    | credentials including your AWS Access Key ID and AWS Secret Access Key.
    |
    | To use credentials from your credentials file or environment or to use
    | IAM Instance Profile credentials, please remove these config settings from
    | your config or make sure they are null. For more information, see:
    | http://docs.aws.amazon.com/aws-sdk-php-2/guide/latest/configuration.html
    |
    */
    //'key'    => 'AKIAIGOWJPK7E74ONMIA',
    //'secret' => '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',

    'credentials' => [
        'key'    => '',
        'secret' => '',
    ],

    /*
    |--------------------------------------------------------------------------
    | AWS Region
    |--------------------------------------------------------------------------
    |
    | Many AWS services are available in multiple regions. You should specify
    | the AWS region you would like to use, but please remember that not every
    | service is available in every region. To see what regions are available,
    | see: http://docs.aws.amazon.com/general/latest/gr/rande.html
    |
    */
    //'region' => env('AWS_REGION', 'us-west-2'),

    'region' => 'us-east-1',

    // You can override settings for specific services
    //'Ses' => [
    //    'region' => 'us-east-1',
    //],

    /*
    |--------------------------------------------------------------------------
    | AWS Config File Location
    |--------------------------------------------------------------------------
    |
    | Instead of specifying your credentials and region here, you can specify
    | the location of an AWS SDK for PHP config file to use. These files provide
    | more granular control over what credentials and regions you are using for
    | each service. If you specify a filepath for this configuration setting,
    | the others in this file will be ignored. See the SDK user guide for more
    | information: http://docs.aws.amazon.com/aws-sdk-php-2/guide/latest/configuration.html#using-a-custom-configuration-file
    |
    */
    'config_file' => null, //env('AWS_CONFIG_FILE'),


    /**
     * Bucket name
     */
    'bucket' => 'simplifyas3',

    'ACTION_COMMENT_IMG_DIR' => 'comment/',
    'PROFILE_IMG_DIR'           => 'profile/',
    'COMPANY_LOGO_IMG_DIR'           => 'company/',
    'PROFILE_DEFAULT_IMAGE'           => 'default-profile-image.jpg',
    'COMPANY_DEFAULT_IMAGE'           => 'default-company-image.png',

    /*
    |--------------------------------------------------------------------------
    | AWS SDK Configuration
    |--------------------------------------------------------------------------
    |
    | The configuration options set in this file will be passed directly to the
    | `Aws\Sdk` object, from which all client objects are created. The minimum
    | required options are declared here, but the full set of possible options
    | are documented at:
    | http://docs.aws.amazon.com/aws-sdk-php/v3/guide/guide/configuration.html
    |
    */
    'version' => 'latest',
//    'ua_append' => [
//        'L5MOD/' . AwsServiceProvider::VERSION,
//    ],

];
