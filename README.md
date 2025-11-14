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
composer require webbycrown/s3-extension-for-bagisto
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

## 4. Configuration:

### Admin Panel Settings

Navigate to: **Admin Panel → Configuration → AWS → S3 Bucket**

Configure the following fields:

| Field | Description | Example |
|-------|-------------|---------|
| **Access Key** | AWS IAM Access Key ID | `AKIAIOSFODNN7EXAMPLE` |
| **Secret Key** | AWS IAM Secret Access Key | `wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY` |
| **Default Region** | AWS Region where bucket exists | `us-east-1` |
| **Bucket Name** | S3 Bucket name | `my-bagisto-store` |
| **Console URL** | AWS S3 Console URL | `https://s3.console.aws.amazon.com/...` |
| **AWS URL** | Public CDN/S3 URL for file access | `https://my-bucket.s3.amazonaws.com` or `https://cdn.mystore.com` |

> **Note:** The extension automatically updates your `.env` file when you save these settings.

### What is AWS_FILE_PATH?

`AWS_FILE_PATH` is the **public URL** where your S3 files are accessible. Examples:

```env
# Direct S3 URL
AWS_FILE_PATH=https://my-bucket.s3.amazonaws.com

# S3 with region
AWS_FILE_PATH=https://my-bucket.s3.us-east-1.amazonaws.com

# CloudFront CDN (recommended)
AWS_FILE_PATH=https://d1234567890.cloudfront.net
```

## 5. Usage:

### Basic Upload/Delete Operations

Apply this code wherever you want to upload or delete files to AWS S3:

~~~php
// Upload Example
$s3Data = [
    'action' => 'upload',
    'imageUrl' => storage_path('app/public/product/images/sample.jpg'),
    'location' => 'product/images'  // Optional: S3 folder structure
];

$result = app('Webbycrown\S3Extension\S3Extension')->awsS3Operation($s3Data);

// Response:
// [
//     'imageName' => 'sample.jpg',
//     'imageUrl' => 'https://my-bucket.s3.amazonaws.com/product/images/sample.jpg',
//     'message' => 'Image Saved Successfully',
//     'status' => 'success',
//     'action' => 'upload',
//     'extension' => 'yes'
// ]
~~~

~~~php
// Delete Example
$s3Data = [
    'action' => 'delete',
    'imageUrl' => storage_path('app/public/product/images/sample.jpg'),
    'location' => 'product/images'
];

$result = app('Webbycrown\S3Extension\S3Extension')->awsS3Operation($s3Data);
~~~

### Parameters

- **$action** : `upload` or `delete`
- **$imageUrl** : Full local file path (e.g., `storage_path('app/public/...')`)
- **$location** : S3 folder hierarchy (optional, e.g., `product/images`, `category/banners`)

### Get Uploaded S3 File URL

~~~php
// If you stored the full URL in database
$imageUrl = $product->image; // https://my-bucket.s3.amazonaws.com/product/images/sample.jpg

// Or construct from path
$s3Url = env('AWS_FILE_PATH') . '/' . $path;
~~~

## 6. Advanced Usage:

### Migrating Existing Images to S3

If you have existing images and want to migrate them to S3, create a custom command:

~~~php
<?php
// app/Console/Commands/MigrateImagesToS3.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Webkul\Product\Models\ProductImage;
use Webbycrown\S3Extension\S3Extension;

class MigrateImagesToS3 extends Command
{
    protected $signature = 'migrate:images-to-s3';
    protected $description = 'Migrate all product images to S3';

    public function handle()
    {
        $images = ProductImage::all();
        $s3 = app(S3Extension::class);

        $this->info("Migrating {$images->count()} images...");

        foreach ($images as $image) {
            $localPath = storage_path('app/public/' . $image->path);
            
            if (!file_exists($localPath)) {
                $this->warn("Skipped: {$localPath}");
                continue;
            }

            $result = $s3->awsS3Operation([
                'action' => 'upload',
                'imageUrl' => $localPath,
                'location' => 'product/images'
            ]);

            if ($result['status'] === 'success') {
                // Update database with S3 URL
                $image->update(['path' => $result['imageUrl']]);
                $this->info("✓ Migrated: {$image->path}");
            }
        }

        $this->info("Migration completed!");
    }
}
~~~

Run the migration:
```bash
php artisan migrate:images-to-s3
```

### Auto-Upload on Product Image Creation

Hook into Bagisto's image upload events:

~~~php
// In your EventServiceProvider or custom Observer

use Webkul\Product\Models\ProductImage;
use Webbycrown\S3Extension\S3Extension;

ProductImage::created(function ($productImage) {
    $localPath = storage_path('app/public/' . $productImage->path);
    
    $result = app(S3Extension::class)->awsS3Operation([
        'action' => 'upload',
        'imageUrl' => $localPath,
        'location' => 'product/images'
    ]);
    
    if ($result['status'] === 'success') {
        // Update path to S3 URL
        $productImage->update(['path' => $result['imageUrl']]);
    }
});
~~~

## 7. AWS S3 Setup:

### S3 Bucket Configuration

1. **Create S3 Bucket** in your AWS Console
2. **Set Bucket Policy** for public read access:

```json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Sid": "PublicReadGetObject",
            "Effect": "Allow",
            "Principal": "*",
            "Action": "s3:GetObject",
            "Resource": "arn:aws:s3:::your-bucket-name/*"
        }
    ]
}
```

3. **Configure CORS** (if accessing from browsers):

```json
[
    {
        "AllowedHeaders": ["*"],
        "AllowedMethods": ["GET", "HEAD"],
        "AllowedOrigins": ["*"],
        "ExposeHeaders": ["ETag"]
    }
]
```

4. **IAM User Permissions** (minimum required):

```json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Effect": "Allow",
            "Action": [
                "s3:PutObject",
                "s3:GetObject",
                "s3:DeleteObject",
                "s3:ListBucket"
            ],
            "Resource": [
                "arn:aws:s3:::your-bucket-name",
                "arn:aws:s3:::your-bucket-name/*"
            ]
        }
    ]
}
```

### CloudFront CDN (Optional but Recommended)

For better performance, create a CloudFront distribution:

1. Create CloudFront distribution pointing to your S3 bucket
2. Update **AWS URL** in admin panel with CloudFront URL
3. Example: `https://d1234567890.cloudfront.net`

## 8. FAQ:

**Q: Does this package automatically migrate my existing images?**  
A: No, you need to create a migration script (see Advanced Usage section).

**Q: Will new uploads automatically go to S3?**  
A: Not automatically. You need to integrate the `awsS3Operation()` method into your upload logic or use event observers.

**Q: Does it update URLs in the database?**  
A: No, you need to handle database updates in your implementation.

**Q: Can I use CloudFront?**  
A: Yes! Just set your CloudFront URL in the **AWS URL** field.

**Q: What happens to my local files?**  
A: They remain on your server. You can delete them after confirming S3 upload.

**Q: Is there a bulk upload command?**  
A: Not built-in yet, but you can create one (see Advanced Usage). This feature is planned for v1.1.0.

## 9. Troubleshooting:

**Images not displaying:**
- Check S3 bucket is publicly accessible
- Verify CORS configuration
- Ensure AWS_FILE_PATH is correctly set

**Upload fails:**
- Check AWS credentials are correct
- Verify IAM user has required permissions
- Check local file path exists

**Permission errors:**
- Ensure IAM user has `s3:PutObject` permission
- Check bucket policy allows uploads

## 10. Support:

- 🐛 [Report Issues](https://github.com/webbycrown/s3-extension-for-bagisto/issues)
- 📧 Email: info@webbycrown.com
- 🌐 Website: [webbycrown.com](https://webbycrown.com)

## 11. Changelog:

### v1.0.0 - 2025-11-14

#### ✨ Initial Stable Release

- 🎉 Initial release of **S3 Extension for Bagisto**
- ☁️ Seamless Amazon S3 integration for Bagisto e-commerce platform
- 📤 Upload and delete operations for media files, product images, and downloadable products
- 🛍️ Full compatibility with all Bagisto product types
- 🖼️ Automatic storage and retrieval of product images directly from S3 bucket
- 📝 Support for media files within product descriptions and content
- ⚡ Cache images served directly from Amazon S3 Server for improved performance
- 📁 Static file storage on Amazon S3 with flexible folder hierarchy
- ⚙️ Admin panel configuration interface for AWS S3 bucket settings
- ⏰ Configurable expiration headers for cached files
- 🔐 Secure credential management via environment variables
- 🎯 Easy-to-use API with `awsS3Operation()` method for upload/delete operations
- 🔧 Integration with Laravel Filesystem and League Flysystem
- 🌍 Global file accessibility for customers from any location

#### 📚 Documentation

- 📖 Comprehensive README with detailed configuration guide
- 🌐 Clear explanation of AWS_FILE_PATH with examples (S3 URL, CloudFront CDN)
- 💡 Advanced Usage section with migration command examples
- 🔧 Auto-upload event observer examples for seamless integration
- ❓ FAQ section answering common questions
- 🛠️ Troubleshooting guide for common issues
- 📋 AWS S3 bucket policy, CORS, and IAM permissions templates
- 📚 Complete AWS S3 setup guide

#### 🔄 Package

- 📦 Stable package version for production use
- ✨ Simple installation: `composer require webbycrown/s3-extension-for-bagisto`
- 🏷️ Properly tagged and versioned for Packagist
- 📦 Published to Packagist: [webbycrown/s3-extension-for-bagisto](https://packagist.org/packages/webbycrown/s3-extension-for-bagisto)
- 🐙 Open source on GitHub: [webbycrown/s3-extension-for-bagisto](https://github.com/webbycrown/s3-extension-for-bagisto)

---

<div align="center">
  <strong>Made with ❤️ by <a href="https://webbycrown.com">WebbyCrown</a></strong>
</div>
