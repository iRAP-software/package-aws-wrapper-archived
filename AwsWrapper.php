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
    
    public function __construct($apiKey, $secret, Enums\AwsRegion $region)
    {
        $this->m_apiKey = $apiKey;
        $this->m_secret = $secret;
        $this->m_region = $region;
    }
    
    
    /**
     * Fetch a client for interfacing with S3
     * @return S3\S3Client
     */
    public function getS3Client()
    {
        return new S3\S3Client($this->m_apiKey, $this->m_secret, $this->m_region);
    }
    
    
    /**
     * 
     * @return Ec2\Ec2Client
     */
    public function getEc2Client()
    {
        return new Ec2\Ec2Client($this->m_apiKey, $this->m_secret, $this->m_region);
    }
}