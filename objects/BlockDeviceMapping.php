<?php

namespace iRAP\AwsWrapper\Objects;

/* 
 * Represents a block device that can be attached to an aws instance.
 * 
 * Please refer to:
 * http://docs.aws.amazon.com/AWSEC2/latest/UserGuide/block-device-mapping-concepts.html
 * for more information
 * 
 * and Please refer to:
 * http://docs.aws.amazon.com/AWSSDKforPHP/latest/#m=AmazonEC2/request_spot_instances
 * for structure/layout/code
 */

class BlockDeviceMapping
{
    private $m_blockDevices;
    
    public function __construct()
    {
        
    }
    
    public function addBlockDevice(BlockDevice $device)
    {
        $this->m_blockDevices[] = $device;
    }
    
    public function toArray()
    {
        $arrayForm = array();
        foreach($this->m_blockDevices as $device)
        {
            /* @var $device \iRAP\AwsWrapper\Objects\BlockDevice */
            $arrayForm[] = $device->toArray();
        }
        
        return $arrayForm;
    }
}
