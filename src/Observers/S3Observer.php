<?php

namespace Webbycrown\S3Extension\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Webkul\Core\Models\CoreConfig;

class S3Observer
{
    
    /**
     * Handle the FeedbackDetail "created" event.
     *
     * @param  \App\Models\FeedbackDetail  $feedback
     * @return void
     */
    public function created(Model $model)
    {
        $this->update_aws_details_in_env_file();
    }

    /**
     * Handle the FeedbackDetail "updated" event.
     *
     * @param  \App\Models\FeedbackDetail  $feedback
     * @return void
     */
    public function updated(Model $model)
    {
        $this->update_aws_details_in_env_file();
    }
  
    /**
     * Handle the FeedbackDetail "deleted" event.
     *
     * @param  \App\Models\FeedbackDetail  $feedback
     * @return void
     */
    public function deleted(Model $model)
    {

    }
  
    /**
     * Handle the FeedbackDetail "restored" event.
     *
     * @param  \App\Models\FeedbackDetail  $feedback
     * @return void
     */
    public function restored(Model $model)
    {
          
    }
  
    /**
     * Handle the FeedbackDetail "force deleted" event.
     *
     * @param  \App\Models\FeedbackDetail  $feedback
     * @return void
     */
    public function forceDeleted(Model $model)
    {
          
    }

    private function update_aws_details_in_env_file()
    {
        $aws_access = CoreConfig::where( 'code', 'aws.s3_bucket.setting.access_key' )->first();
        $db_aws_access = $aws_access ? $aws_access->value : null;

        $aws_secret = CoreConfig::where( 'code', 'aws.s3_bucket.setting.secret_key' )->first();
        $db_aws_secret = $aws_secret ? $aws_secret->value : null;
        
        $aws_default_region = CoreConfig::where( 'code', 'aws.s3_bucket.setting.default_region' )->first();
        $db_aws_default_region = $aws_default_region ? $aws_default_region->value : null;
        
        $aws_bucket_name = CoreConfig::where( 'code', 'aws.s3_bucket.setting.bucket_name' )->first();
        $db_aws_bucket_name = $aws_bucket_name ? $aws_bucket_name->value : null;
        
        $aws_console_url = CoreConfig::where( 'code', 'aws.s3_bucket.setting.console_url' )->first();
        $db_aws_console_url = $aws_console_url ? $aws_console_url->value : null;
        
        $aws_file_url = CoreConfig::where( 'code', 'aws.s3_bucket.setting.aws_url' )->first();
        $db_aws_file_url = $aws_file_url ? $aws_file_url->value : null;

        $env_values = [
            'AWS_ACCESS_KEY_ID' => $db_aws_access,
            'AWS_SECRET_ACCESS_KEY' => $db_aws_secret,
            'AWS_DEFAULT_REGION' => $db_aws_default_region,
            'AWS_BUCKET' => $db_aws_bucket_name,
            'AWS_URL' => $db_aws_console_url,
            'AWS_USE_PATH_STYLE_ENDPOINT' => 'false',
            'AWS_FILE_PATH' => $db_aws_file_url,
        ];

        $env_file_path = base_path( '.env' );
        $current_env = file_get_contents( $env_file_path );

        foreach ( $env_values as $key => $value ) {

            $env_variable = strtoupper( $key ) . '=' . $value;
            
            if ( strpos( $current_env, $key ) !== false ) {
                $current_env = preg_replace( "/$key=.*/", $env_variable, $current_env );
            } else {
                $current_env .= "\n$env_variable";
            }
        }

        File::put( $env_file_path, $current_env );

        Artisan::call( 'optimize:clear' );
    }

}
