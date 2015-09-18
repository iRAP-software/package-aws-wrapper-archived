<?php

namespace iRAP\AwsWrapper\Objects;

/* 
 * Launch specification of instances.
 * This class is mainly based upon:
 * http://docs.aws.amazon.com/AWSSDKforPHP/latest/#m=AmazonEC2/request_spot_instances
 */

class LaunchSpecification
{
    private $m_image_id;
    private $m_keyName; # The name of the key pair for SSH authentication when deployed.
    private $m_instanceType;
    private $m_securityGroup;
    private $m_ebsOptimized = false;
    private $m_groupSet = null;
    private $m_networkInterfaces = array();
    private $m_blockDevices = array();
    private $m_ramDiskId = null;
    private $m_kernelId = null;
    private $m_placement = null;
    private $m_userData = null; # optional string of user data
    private $m_monitoringEnabled = false;
    private $m_iamProfile = array(); # optional array of IamInstanceProfile objects
    
    
    /**
     * Create the LaunchSpecification.
     * Note that only the required fields are in the parameters, there are many more options that
     * can be defined through the public methods, such as addNetworkInterface().
     * @param Ec2InstanceType $instanceType - the type of instance (size) to launch
     * @param String $imageId - the ID of the image we are going to launch
     */
    public function __construct(\iRAP\AwsWrapper\Enums\Ec2InstanceType $instanceType, $imageId)
    {
        self::validateImageId($imageId);
        $this->m_instanceType = $instanceType;
        $this->m_image_id = $imageId;
    }
    
    
    /**
     * Set the instance to be EBS optimized for an extra fee. (EBS storage is over a network and 
     * this allows better IO)
     * @param type $flag
     */
    public function setEbsOptimized($flag=true)
    {
        if ($flag)
        {
            $this->m_ebsOptimized = true;
        }
        else
        {
            $this->m_ebsOptimized = false;
        }
    }
    
    
    /**
     * Set the security for the launched instances.
     * E.g. sg-b00ef8df
     * @param string $securityGroup - the ID of the security group we wish to set.
     * @return void
     */
    public function setSecurityGroup($securityGroup)
    {
        self::validateSecurityGroup($securityGroup);
        $this->m_securityGroup = $securityGroup;
    }
    
    
    /**
     * Optionally define which keypair should be used for SSH authentication.
     * @param String $name - the name of the keypair
     * @return void.
     */
    public function setKeyPair($name)
    {
        $this->m_keyName = $name;
    }
    
    
    /**
     * Specify the ID of a kernel to select
     * @param type $kernelId
     * @return void
     */
    public function setKernelId($kernelId)
    {
        $this->m_kernelId = $kernelId;
    }
    
    
    /**
     * Set some optional data on the instance, specific to a user’s application, to provide in the 
     * launch request. All instances that collectively comprise the launch request have access to 
     * this data. User data is never returned through API responses.
     * @param String $userData
     * @throws Exception
     */
    public function setUserData($userData)
    {
        if (!is_string($userData))
        {
            throw new \Exception('User data must be a string.');
        }
        
        $this->m_userData = $userData;
    }
    
    
    /**
     * Specifiy the ID of the RAM disk to select. 
     * Some kernels require additional drivers at launch. Check the kernel requirements for 
     * information on whether or not you need to specify a RAM disk and search for the kernel ID.
     * @param type $ramDiskId
     * @return void
     */
    public function setRamDiskId($ramDiskId)
    {
        $this->m_ramDiskId = $ramDiskId;
    }
    
    public function addIamInstanceProfile(IamInstanceProfile $profile)
    {
        $this->m_iamProfile[] = $profile;
    }
    
    
    public function addNetworkInterface(NetworkInterface $networkInterface)
    {
        $this->m_networkInterfaces[] = $networkInterface;
    }
    
    
    public function addBlockDevice(BlockDevice $blockDevice)
    {
        $this->m_blockDevices[] = $blockDevice;
    }
    
    
    public function setPlacement(Placement $placement)
    {
        $this->m_placement = $placement;
    }
    
    
    /**
     * Enable monitoring.
     * @param type $flag
     */
    public function setMonitoring($flag=true)
    {
        $this->m_monitoringEnabled = $flag;
    }
    
    
    /**
     * Specifiy the subnet ID within which to launch the instance(s) for Amazon Virtual Private 
     * Cloud.
     * @param string $subnetId
     */
    public function setSubnetId($subnetId)
    {
        $this->m_subnetId = $subnetId;
    }
    
    
    /**
     * Converts this into an array form that can be used in requests.
     * @param void
     * @return Array $arrayForm - this object in array form.
     */
    public function toArray()
    {
        $arrayForm = array(
            'ImageId'       => $this->m_image_id,
            'InstanceType'  => (String)$this->m_instanceType,
        );
        
        if (isset($this->m_keyName))
        {
            $arrayForm['KeyName'] = $this->m_keyName;
        }
        
        if (isset($this->m_securityGroup))
        {
            $arrayForm['SecurityGroupIds'] = array($this->m_securityGroup);
        }
        
        if (isset($this->m_userData))
        {
            $arrayForm['UserData'] = $this->m_userData;
        }
        
        if (isset($this->m_placement))
        {
            /* @var $this->m_placement Placement */
            $arrayForm['Placement'] = $this->m_placement->toArray();
        }
        
        if (isset($this->m_kernelId))
        {
            $arrayForm['KernelId'] = $this->m_kernelId;
        }
        
        if (isset($this->m_ramDiskId))
        {
            $arrayForm['RamdiskId'] = $this->m_ramDiskId;
        }
        
        if (count($this->m_blockDevices) > 0)
        {
            $expandedBlockDevices = array();
            
            foreach ($this->m_blockDevices as $blockDevice)
            {
                /* @var $blockDevice BlockDevice */
                $expandedBlockDevices[] = $blockDevice->toArray();
            }
            
            $arrayForm['BlockDeviceMapping'] = $expandedBlockDevices;
        }
        
        
        if ($this->m_monitoringEnabled)
        {
            $arrayForm['Monitoring'] = array('Enabled'=> $this->m_monitoringEnabled);
        }
        
        
        if (count($this->m_networkInterfaces) > 0)
        {
            $networkInterfaces = array();
            
            foreach ($this->m_networkInterfaces as $index => $network_interface)
            {
                /* @var $network_interface NetworkInterface */
                $networkInterfaceArrayForm = $network_interface->toArray();
                $networkInterfaceArrayForm['DeviceIndex'] = $index;
                $networkInterfaces[] = $networkInterfaceArrayForm;
            }
            
            $arrayForm['NetworkInterfaces'] = $networkInterfaces;
        }
        else
        {
            # Cannot set the subnet ID if we have defined network interfaces.
            if (isset($this->m_subnetId))
            {
                $arrayForm['SubnetId'] = $this->m_subnetId;
            }
        }
        
        if (count($this->m_iamProfile) > 0)
        {
            $iamProfiles = array();
            
            foreach ($this->m_iamProfile as $profile)
            {
                /* @var $profile IamInstanceProfile */
                $iamProfiles[] = $profile->toArray();
            }
            
            $arrayForm['IamInstanceProfile'] = $iamProfiles;
        }
        
        if (isset($this->m_ebsOptimized) && $this->m_ebsOptimized == true)
        {
            $arrayForm['EbsOptimized'] = $this->m_ebsOptimized;
        }
        
        return $arrayForm;
    }
    
    
    private static function validateImageId($imageId)
    {
        print "validateImageId to be implemented." . PHP_EOL;
    }
    
    
    private static function validateSecurityGroup($securityGroup)
    {
        print "security Group validation has yet to be implemented" . PHP_EOL;
    }
    
    
    # Accessors
    public function getImageId() { return $this->m_image_id; }
}