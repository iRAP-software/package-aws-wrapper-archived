<?php

namespace iRAP\AwsWrapper\Objects;

/* 
 * A single private IP that makes up part of the PrivateIpAddresses in a NetworkInterfaceSet
 * http://docs.aws.amazon.com/AWSSDKforPHP/latest/#m=AmazonEC2/request_spot_instances
 */


class PrivateIp
{
    private $m_name;
    private $m_primary;
    
    
    /**
     * 
     * @param type $ip - the actual ip address to set. e.g. 54.0.0.1
     * @param bool $primary - whether this is the primpary private ip
     */
    public function __construct($ip, $primary)
    {
        if (filter_var($ip, FILTER_VALIDATE_IP) !== FALSE)
        {
            throw new \Exception('PrivateIp - invalid ip: ' . $ip);
        }
        
        if (is_bool($primary) !== TRUE)
        {
            throw new \Exception('PrivateIp - primary needs to be a bool');
        }
        
        $this->m_ip = $ip;
        $this->m_primary = $primary;
    }
    
    
    public function to_array()
    {
        return array(
            'ip'      => $this->m_ip,
            'primary' => $this->m_primary
        );
    }
    
    
    public function is_primary()
    {
        return $this->m_primary;
    }
}