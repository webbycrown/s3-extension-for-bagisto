<?php
return [

    /**
     * AWS.
     */
    [
        'key'  => 'aws',
        'name' => 'Aws',
        'info' => 'Aws',
        'sort' => 7,
    ], [
        'key'  => 'aws.s3_bucket',
        'name' => 'S3 Bucket',
        'info' => 'S3 Bucket',
        'icon' => 'settings/tax.svg',
        'sort' => 1,
    ], [
        'key'    => 'aws.s3_bucket.setting',
        'name'   => 'AWS S3 Bucket Setting',
        'info'   => 'AWS S3 Bucket Setting',
        'sort'   => 1,
        'fields' => [
            [
                'name'    => 'access_key',
                'title'   => 'Access Key',
                'type'    => 'text',
                'default' => '',
            ],[
                'name'    => 'secret_key',
                'title'   => 'Secret Key',
                'type'    => 'text',
                'default' => '',
            ],[
                'name'    => 'default_region',
                'title'   => 'Default Region',
                'type'    => 'text',
                'default' => '',
            ],[
                'name'    => 'bucket_name',
                'title'   => 'Bucket Name',
                'type'    => 'text',
                'default' => '',
            ],[
                'name'    => 'console_url',
                'title'   => 'Console Url',
                'type'    => 'text',
                'default' => '',
            ],[
                'name'    => 'aws_url',
                'title'   => 'Aws Url',
                'type'    => 'text',
                'default' => '',
            ],
        ],
    ],

];