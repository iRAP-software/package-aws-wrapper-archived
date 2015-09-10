<?php

/* 
 * Client for interfacing with AWS Ec2
 * You may find this useful:
 * http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-ec2-2015-04-15.html
 */

namespace iRAP\AwsWrapper\Ec2;

class Ec2Client
{
    private $m_client;
    
    public function __construct($apiKey, $apiSecret, \iRAP\AwsWrapper\Enums\AwsRegion $region)
    {
        $credentials = array(
            'key'    => $apiKey,
            'secret' => $apiSecret
        );
        
        $params = array(
            'credentials' => $credentials,
            'version'     => '2015-04-15',
            'region'      => (string) $region,
        );
        
        $this->m_client = new \Aws\Ec2\Ec2Client($params);
    }
    
    
    /**
     * http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-ec2-2015-04-15.html#createimage
     */
    public function createImage()
    {
        
    }
    
    
    public function createKeyPair()
    {
        
    }
    
    
    /**
     * http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-ec2-2015-04-15.html#createsnapshot
     */
    public function createSnapshot()
    {
        
    }
    
    
    public function deleteSnapshot()
    {
        
    }
    
    
    public function cancelSpotInstanceRequests()
    {
        
    }
    
    
    /**
     * 
     * http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-ec2-2015-04-15.html#describeinstances
     */
    public function describeInstances()
    {
        
    }
    
    
    /**
     * Launch some on demand instances (fixed price).
     * http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-ec2-2015-04-15.html#runinstances
     */
    public function runInstances(\iRAP\AwsWrapper\Requests\RequestRunInstances $request)
    {
        return $request->send($this->m_client);        
    }
    
    
    /**
     * Alias for RunInstances
     */
    public function requestOnDemandInstances()
    {
        $this->runInstances();
    }
    
    
    public function requestSpotInstances()
    {
        
    }
    
    
    public function requestSpotFleet()
    {
        
    }
    
    /**
     * http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-ec2-2015-04-15.html#startinstances
     */
    public function startInstances()
    {
        
    }
    
    
    public function stopInstances()
    {
        
    }
    
    
    public function terminateInstances()
    {
        
    }
}