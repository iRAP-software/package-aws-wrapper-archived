<?php

namespace iRAP\AwsWrapper\Objects;

/* 
 * This class is based upon:
 * http://docs.aws.amazon.com/AWSSDKforPHP/latest/#m=AmazonEC2/request_spot_instances
 * but as you can see the documentation is not finished, making it hard to complete this.
 */

class IamInstanceProfile
{
    private $m_arn; # no idea what this is yet
    private $m_name; # no idea what this is yet.
    
    public function __construct($arn, $name) 
    {
        $this->m_arn = $arn;
        $this->m_name = $name;
    }
    
    public function toArray()
    {
        return array(
            'Arn'  => $this->m_arn,
            'Name' => $this->m_name
        );
    }
}