<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace iRAP\AwsWrapper;

class AwsWrapper
{
    private $m_apiKey;
    private $m_secret;
    private $m_region;
    
    public function __construct($api_key, $secret, Enums\AmazonRegion $region)
    {
        $this->m_apiKey = $api_key;
        $this->m_secret = $secret;
        $this->m_region = $region;
    }
    
    public function getS3Client()
    {
        return new S3Client($apiKey, $apSecret, $region);
    }
    
    public function getEc2Client()
    {
        return new S3Client($apiKey, $apSecret, $region);
    }
}
