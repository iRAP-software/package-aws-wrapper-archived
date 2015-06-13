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
    private $m_key_name; # The name of the key pair for SSH authentication when deployed.
    private $m_instance_type;
    private $m_security_group;
    private $m_ebs_optimized = false;
    private $m_group_set = null;
    private $m_network_interface_set = array();
    private $m_block_devices = array();
    private $m_ram_disk_id = null;
    private $m_kernel_id = null;
    private $m_placement = null;
    private $m_user_data = null; # optional string of user data
    private $m_monitoring_enabled = false;
    private $m_iam_profiles = array(); # optional array of IamInstanceProfile objects
    
    
    /**
     * Create the LaunchSpecification.
     * Note that only the required fields are in the parameters, there are many more options that
     * can be defined through the public methods, such as addNetworkInterface().
     * @param Ec2InstanceType $instance_type - the type of instance (size) to launch
     * @param String $image_id - the ID of the image we are going to launch
     */
    public function __construct(\Irap\AwsWrapper\Enums\Ec2InstanceType $instance_type, $image_id)
    {
        self::validate_image_id($image_id);
        $this->m_instance_type = $instance_type;
        $this->m_image_id = $image_id;
    }
    
    
    /**
     * Set the instance to be EBS optimized for an extra fee. (EBS storage is over a network and 
     * this allows better IO)
     * @param type $flag
     */
    public function set_ebs_optimized($flag=true)
    {
        if ($flag)
        {
            $this->m_ebs_optimized = true;
        }
        else
        {
            $this->m_ebs_optimized = false;
        }
    }
    
    
    /**
     * Set the security for the launched instances.
     * E.g. sg-b00ef8df
     * @param string $securityGroup - the ID of the security group we wish to set.
     * @return void
     */
    public function set_security_group($securityGroup)
    {
        self::validate_security_group($securityGroup);
        $this->m_security_group = $securityGroup;
    }
    
    
    /**
     * Optionally define which keypair should be used for SSH authentication.
     * @param String $name - the name of the keypair
     * @return void.
     */
    public function set_key_pair($name)
    {
        $this->m_key_name = $name;
    }
    
    
    /**
     * Specify the ID of a kernel to select
     * @param type $kernelId
     * @return void
     */
    public function set_kernel_id($kernelId)
    {
        $this->m_kernel_id = $kernelId;
    }
    
    
    /**
     * Set some optional data on the instance, specific to a user’s application, to provide in the 
     * launch request. All instances that collectively comprise the launch request have access to 
     * this data. User data is never returned through API responses.
     * @param String $userData
     * @throws Exception
     */
    public function set_user_data($userData)
    {
        if (!is_string($userData))
        {
            throw new \Exception('User data must be a string.');
        }
        
        $this->m_user_data = $userData;
    }
    
    
    /**
     * Specifiy the ID of the RAM disk to select. 
     * Some kernels require additional drivers at launch. Check the kernel requirements for 
     * information on whether or not you need to specify a RAM disk and search for the kernel ID.
     * @param type $ramDiskId
     * @return void
     */
    public function set_ram_disk_id($ramDiskId)
    {
        $this->m_ram_disk_id = $ramDiskId;
    }
    
    public function add_iam_instance_profile(IamInstanceProfile $profile)
    {
        $this->m_iam_profiles[] = $profile;
    }
    
    
    public function add_network_interface(NetworkInterface $networkInterface)
    {
        $this->m_network_interface_set[] = $networkInteface;
    }
    
    
    public function add_block_device(BlockDevice $blockDevice)
    {
        $this->m_block_devices[] = $blockDevice;
    }
    
    
    public function set_placement(Placement $placement)
    {
        $this->m_placement = $placement;
    }
    
    
    /**
     * Enable monitoring.
     * @param type $flag
     */
    public function set_monitoring($flag=true)
    {
        $this->m_monitoring_enabled = $flag;
    }
    
    
    /**
     * Specifiy the subnet ID within which to launch the instance(s) for Amazon Virtual Private 
     * Cloud.
     * @param string $subnetId
     */
    public function set_subnet_id($subnetId)
    {
        $this->m_subnetId = $subnetId;
    }
    
    
    /**
     * Converts this into an array form that can be used in requests.
     * @param void
     * @return Array $arrayForm - this object in array form.
     */
    public function to_array()
    {
        $arrayForm = array(
            'ImageId'       => $this->m_image_id,
            'InstanceType'  => (String)$this->m_instance_type,
            'ImageId'       => $this->m_image_id
        );
        
        if (isset($this->m_key_name))
        {
            $arrayForm['KeyName'] = $this->m_key_name;
        }
        
        if (isset($this->m_security_group))
        {
            $arrayForm['SecurityGroup'] = $this->m_security_group;
        }
        
        if (isset($this->m_user_data))
        {
            $arrayForm['UserData'] = $this->m_user_data;
        }
        
        if (isset($this->m_placement))
        {
            /* @var $this->m_placement Placement */
            $arrayForm['Placement'] = $this->m_placement->to_array();
        }
        
        if (isset($this->m_kernel_id))
        {
            $arrayForm['KernelId'] = $this->m_kernel_id;
        }
        
        if (isset($this->m_ram_disk_id))
        {
            $arrayForm['RamdiskId'] = $this->m_ram_disk_id;
        }
        
        if (count($this->m_block_devices) > 0)
        {
            $expandedBlockDevices = array();
            
            foreach ($this->m_block_devices as $blockDevice)
            {
                /* @var $blockDevice BlockDevice */
                $expandedBlockDevices[] = $blockDevice->to_array();
            }
            
            $arrayForm['BlockDeviceMapping'] = $expandedBlockDevices;
        }
        
        
        if ($this->m_monitoring_enabled)
        {
            $arrayForm['Monitoring.Enabled'] = $this->m_monitoring_enabled;
        }
        
        
        if (isset($this->m_subnetId))
        {
            $arrayForm['SubnetId'] = $this->m_subnetId;
        }
        
        if (count($this->m_network_interface_set) > 0)
        {
            $networkInterfaces = array();
            
            foreach ($this->m_network_interface_set as $network_interface)
            {
                /* @var $network_interface NetworkInterface */
                $networkInterfaces[] = $network_interface->to_array();
            }
            
            $arrayForm['NetworkInterfaceSet'] = $networkInterfaces;
        }
        
        if (count($this->m_iam_profiles) > 0)
        {
            $iamProfiles = array();
            
            foreach ($this->m_iam_profiles as $profile)
            {
                /* @var $profile IamInstanceProfile */
                $iamProfiles[] = $profile->toArray();
            }
            
            $arrayForm['IamInstanceProfile'] = $iamProfiles;
        }
        
        if (isset($this->m_ebs_optimized) && $this->m_ebs_optimized == true)
        {
            $arrayForm['EbsOptimized'] = $this->m_ebs_optimized;
        }
        
        return $arrayForm;
    }

    private static function validate_image_id($imageId)
    {
        print "validateImageId to be implemented." . PHP_EOL;
    }
    
    private static function validate_security_group($securityGroup)
    {
        print "security Group validation has yet to be implemented" . PHP_EOL;
    }
    
    
    # Accessors
    public function getImageId() { return $this->m_image_id; }
}