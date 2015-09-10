<?php

namespace iRAP\AwsWrapper\Objects;

/* 
 * Represents a block device that can be attached to an aws instance.
 */

class BlockDevice
{
    private $m_virtualName; # e.g. "MySpecialBlock"
    private $m_deviceName; # pecifies the device name (e.g., /dev/sdh).
    private $m_ebsDevices = array();
    private $m_no_device; # Specifies the device name to suppress during instance launch. confusing.
    
    
    /**
     * Create a new block device.
     * @param string $virtual_name - e.g "My_device"
     * @param string $device_name - the device name (e.g., /dev/sdh).
     */
    public function __construct($virtual_name, $device_name)
    {
        $this->m_virtualName = $virtual_name;
        $this->m_deviceName = $device_name;
    }
    
    
    /**
     * Add an ebs volume to the block device
     * @param Ebs $ebs - the EBS device thatyou wish to add to this device.
     */
    public function addEbs(Ebs $ebs)
    {
        $this->m_ebsDevices[] = $ebs;
    }
    
    
    /**
     * Converts this object into an array form that can be placed into a request.
     * @return Array - this object in array form.
     */
    public function toArray()
    {
        $ebs = array();
        
        foreach ($this->m_ebsDevices as $ebsDevice)
        {
            /* @var $ebsDevice Ebs */
            $ebs[] = $ebsDevice->to_array();
        }
        
        return array(
            'VirtualName'   => $this->m_virtualName,
            'DeviceName'    => $this->m_deviceName,
            'Ebs'           => $ebs,
            'NoDevice'      => $this->m_no_device
        );
    }
}
