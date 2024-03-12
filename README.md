# WebbyCrown S3 Extension for Bagisto

## 1. Introduction:

## 2. Requirements:

* **PHP**: 8.0 or higher.
* **Bagisto**: v2.0.*
* **Composer**: 1.6.5 or higher.

## 3. Installation:

- Install the package below.
```
composer require webbycrown/s3-extension-for-bagisto:dev-main
```

- Go to your Admin panel -> click configure menu -> click Aws S3 Bucket and full fill your bucket details.

- Go to the config/imagecache.php file and replace the following line under ‘paths’.

~~~php
'paths' => [
        storage_path('app/public'),
        public_path('storage'),
    ],
~~~

Replace to 

~~~php
'paths' => [
        env('AWS_FILE_PATH'),
    ],
~~~

***Note:*** imagecache.php file does not exists than install [ImageCache](https://github.com/intervention/imagecache) library.

```
composer dump-autoload
```

```
php artisan optimize:clear
```

- Apply this code wherever you want to upload or delete the file in Aws s3 bucket.

~~~php
$s3Data = array( 
 'action' => $action,
 'imageUrl' => $imageUrl,
 'location' => $location,
);
app('Webbycrown\S3Extension\S3Extension')->awsS3Operation($s3Data);
~~~

$action : ‘upload’ or ‘delete’ key
$imageUrl : file stored storage url
$location : location for aws s3 (folder hierarchy)

### Get uploaded s3 bucket file :

~~~php
env( 'AWS_FILE_PATH' ) . '/' . $path;
~~~