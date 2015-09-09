<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class S3Client
{
    private $m_awsClient;
    
    public function __construct($apiKey, $apiSecret, \iRAP\AwsWrapper\Enums\AmazonRegion $region)
    {
        $params = array(
            
        );
        
        $this->m_awsClient = new \Aws\S3\S3Client($params);
    }
    
    
    /**
     * Upload a file to s3
     * @param string $bucket - the bucket that we want to store our file in.
     * @param type $localFilepath - the path to where the file we want to upload currently is
     * @param type $remoteFilepath - the path you want the file to have inside the bucket, e.g. 
     *                               /data/data1.csv where "data" is NOT the name of the bucket.
     * @param string $mime_type - a mime type from 
     *                            http://www.iana.org/assignments/media-types/media-types.xhtml
     */
    public function uploadFile($bucket, $localFilepath, $remoteFilepath, $mime_type)
    {
        $s3_details = array(
            'fileUpload'  => $localFilepath,
            'acl'         => AmazonS3::ACL_PUBLIC, # @TODO support ACLs
            'contentType' => $mime_type
        );
        
        $response = $this->m_awsClient->create_object($bucket, $remoteFilepath, $s3_details);
        return $response;
    }
    
    
    /**
     * Upload an entire directory to S3
     */
    public function uploadDirectory()
    {
        
    }
    
    
    /**
     * Download an entire bucket from S3
     */
    public function downloadBucket()
    {
        
    }
    
    
    /**
     * Delete a bucket and all of its contents from S3
     * @param string $bucketName - the name of the bucket you wish to delete.
     * @return type
     */
    public function deleteBucket($bucketName)
    {
        $result = $client->deleteBucket([
            'Bucket' => $bucketName
        ]);
        
        return $result;
    }
    
    
    /**
     * List all the buckets in your region.
     */
    public function listBuckets()
    {
        $result = $client->listBuckets([/* ... */]);
    }
    
}