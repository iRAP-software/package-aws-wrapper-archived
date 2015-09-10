<?php

/* 
 * This test ensures that we can upload a file to S3
 */

require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/Settings.php');

function main()
{
    $apiKey = API_KEY;
    $secret = API_SECRET;
    $region = \iRAP\AwsWrapper\Enums\AwsRegion::create_EU_W1();
    
    $bucketName = "awswrapper-testing";
    
    $awsWrapper = new iRAP\AwsWrapper\AwsWrapper($apiKey, $secret, $region);
    $s3Client = $awsWrapper->getS3Client();
    
    # Create the bucket
    $acl = \iRAP\AwsWrapper\S3\Acl::createPublicRead();
    
    try
    {
        $response = $s3Client->createBucket($bucketName, $acl);
    }
    catch(Aws\S3\Exception\S3Exception $e)
    {
        if ($e->getCode() == "BucketAlreadyOwnedByYou") # @TODO objectify these codes
        {
           print "You have already created that bucket" . PHP_EOL; 
        }
        else
        {
            print $e->getMessage();
            die();
        }
    }
   
    # Upload a file to the new bucket
    $uploadResult = $s3Client->uploadFile(
        $bucketName, 
        $localFile = __DIR__ . '/upload-test-file.txt', 
        $remoteFilepath='/upload-test-file.txt', 
        \iRAP\AwsWrapper\S3\Acl::createPublicRead(),
        \iRAP\AwsWrapper\S3\StorageClass::createStandard(), 
        $mimeType='text/plain'
    );
    
    print_r($uploadResult);
    
    $secondUploadResult = $s3Client->createFile(
        $bucketName, 
        $content = "created a file from a string rather than a file", 
        $remoteFilepath='/created-file.txt', 
        \iRAP\AwsWrapper\S3\Acl::createPublicRead(),
        \iRAP\AwsWrapper\S3\StorageClass::createStandard(), 
        $mimeType='text/plain'
    );
    
    print_r($secondUploadResult);
    
    # Delete the file.
    
    
    # Delete the bucket (it doesnt have to be empty)
}

main();