# WebbyCrown S3 Extension for Bagisto

## 1. Introduction:

"S3 Extension for Bagisto" seamlessly integrates with Amazon S3, empowering store admins to effortlessly upload downloadable products, media files, product content, and images from their local disk to the S3 server. This free extension enables users to store and retrieve static files and media content directly from the S3 bucket, with added functionality to set expiration headers for enhanced control.

### Features:

- Compatible with all product types in Bagisto.
- Bucket manages the storage and retrieval of media files across various product categories, including product images, media within product descriptions, editing images, and more.
- Effortlessly store and retrieve files directly from the Amazon S3 Server.
- Customers can conveniently access and download files from the Amazon Server at any time and from any location.
- Cache images are now served directly from the Amazon S3 Server.
- Users have the option to save static files on the Amazon S3 server.
- The module offers versatile settings for configuring preferences.
- The extension allows setting expires headers for cached files, enhancing control over caching mechanisms.

## 2. Requirements:

* **PHP**: 8.0 or higher.
* **Bagisto**: v2.0.*
* **Composer**: 1.6.5 or higher.

## 3. Installation:

- Install the package below.
```
composer require webbycrown/s3-extension-for-bagisto:dev-main
```

- Go to your Admin panel -> click **configure** menu -> click **Aws S3 Bucket** and full fill your bucket details.

- Go to the **config/filesystems.php** and add the following code.

~~~php
's3' => [
  'driver' => 's3',
  'key' => env('AWS_ACCESS_KEY_ID'),
  'secret' => env('AWS_SECRET_ACCESS_KEY'),
  'region' => env('AWS_DEFAULT_REGION'),
  'bucket' => env('AWS_BUCKET'),
  'url' => env('AWS_URL'),
  'endpoint' => env('AWS_ENDPOINT'),
  'use_path_style_endpoint' =>env('AWS_USE_PATH_STYLE_ENDPOINT', false),
],
~~~

- Go to the **config/imagecache.php** file and replace the following line under **‘paths’**.

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

- **$action** : `upload` or `delete` key
- **$imageUrl** : file local storage url
- **$location** : location for aws s3 (folder hierarchy)

### Get uploaded s3 bucket file :

~~~php
env( 'AWS_FILE_PATH' ) . '/' . $path;
~~~

---

<div align="center">
  <strong>Made with ❤️ by <a href="https://webbycrown.com">WebbyCrown</a></strong>
</div>
