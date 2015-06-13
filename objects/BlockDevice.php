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

class BlockDevice
{
    private $m_virtualName; # e.g. "MySpecialBlock"
    private $m_device_name; # pecifies the device name (e.g., /dev/sdh).
    private $m_ebs_devices = array();
    private $m_no_device; # Specifies the device name to suppress during instance launch. confusing.
    
    
    /**
     * Create a new block device.
     * @param string $virtual_name - e.g "My_device"
     * @param string $device_name - the device name (e.g., /dev/sdh).
     */
    public function __construct($virtual_name, $device_name)
    {
        $this->m_virtualName = $virtual_name;
        $this->m_device_name = $device_name;
    }
    
    
    /**
     * Add an ebs volume to the block device
     * @param Ebs $ebs - the EBS device thatyou wish to add to this device.
     */
    public function add_ebs(Ebs $ebs)
    {
        $this->m_ebs_devices[] = $ebs;
    }
    
    
    /**
     * Converts this object into an array form that can be placed into a request.
     * @return Array - this object in array form.
     */
    public function to_array()
    {
        $ebs = array();
        
        foreach($ebsDevices as $ebsDevice)
        {
            /* @var $ebsDevice Ebs */
            $ebs[] = $ebsDevice->to_array();
        }
        
        return array(
            'VirtualName'   => $this->m_virtualName,
            'DeviceName'    => $this->m_device_name,
            'Ebs'           => $ebs,
            'NoDevice'      => $this->m_no_device
        );
    }
    
    
    /*
    Ebs - array - Optional - Specifies parameters used to automatically setup Amazon EBS volumes when the instance is launched.

        x - array - Optional - This represents a simple array index.
            SnapshotId - string - Optional - The ID of the snapshot from which the volume will be created.
            VolumeSize - integer - Optional - The size of the volume, in gigabytes.
            DeleteOnTermination - boolean - Optional - Specifies whether the Amazon EBS volume is deleted on instance termination.
            VolumeType - string - Optional - [Allowed values: standard, io1]
            Iops - integer - Optional - 
     * 
     */
}
