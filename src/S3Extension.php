<?php

namespace Webbycrown\S3Extension;

use Carbon\Carbon;
use Closure;
use Exception;
use Illuminate\Cache\FileStore;
use Illuminate\Cache\Repository as Cache;
use Illuminate\Cache\Repository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

class S3Extension
{

    public function awsS3Operation( $s3Data = array() )
    {

        $action = array_key_exists( 'action', $s3Data ) ? $s3Data[ 'action' ] : null;

        $imageUrl = array_key_exists( 'imageUrl', $s3Data ) ? $s3Data[ 'imageUrl' ] : null;

        $uploadedPath = array_key_exists( 'location', $s3Data ) ? $s3Data[ 'location' ] : null;

        $fileName = ( isset( $imageUrl ) && !empty( $imageUrl ) && !is_null( $imageUrl ) ) ? basename( $imageUrl ) : null;

        if ( $imageUrl && $fileName ) {

            $path = ( isset( $uploadedPath ) && !empty( $uploadedPath ) && !is_null( $uploadedPath ) ) ? ( $uploadedPath . '/' . $fileName ) : $fileName;

            if ( $action == 'upload' ) {

                $path = '/' . $path;

                $uploadMedia = Storage::disk( 's3' )->put( $path, file_get_contents( $imageUrl ) );

                if ( $uploadMedia ) {

                    $fileUrl =  env( 'AWS_FILE_PATH' ) . $path;

                    return [
                        'imageName' => $fileName, 
                        'imageUrl' => $fileUrl,
                        'message' => 'Image Saved Successfully',
                        'status' => 'success',
                        'action' => $action,
                        'extension' => 'yes',
                    ];

                }

            }

            if ( $action == 'delete' ) {

                $delete = Storage::disk( 's3' )->delete( $path );

                if( $delete ){

                    return [
                        'imageName' => $fileName,
                        'imageUrl' => '',
                        'message' => 'Image Delete Successfully...',
                        'status' => 'success',
                        'action' => $action,
                        'extension' => 'yes',
                    ];

                }

            }

        }

        return [
            'imageName' => $fileName,
            'imageUrl' => '',
            'message' => 'Something Went Wrong',
            'status' => 'error',
            'action' => $action,
            'extension' => 'yes',
        ];

    }

}
