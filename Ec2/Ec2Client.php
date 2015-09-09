<?php

/* 
 * Client for interfacing with AWS Ec2
 */

namespace iRAP\AwsWrapper\Ec2;

class Ec2Client
{
    private $m_client;
    
    public function __construct($apiKey, $apiSecret, \iRAP\AwsWrapper\Enums\Ec2Region $region)
    {
        $params = array(
            
        );
        
        $this->m_client = Aws\Ec2\Ec2Client::factory($params);
    }
}