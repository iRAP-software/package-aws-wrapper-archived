<?php

namespace iRAP\AwsWrapper\Objects;

/* 
 * A single network interface that can make up part of a networkInterface set in a
 * LaunchSpecification.
 * http://docs.aws.amazon.com/AWSSDKforPHP/latest/#m=AmazonEC2/request_spot_instances
 * 
 * http://docs.aws.amazon.com/AWSCloudFormation/latest/UserGuide/aws-resource-ec2-network-interface.html#cfn-awsec2networkinterface-privateipaddress
 */

class NetworkInterface
{
    private $m_networkInterfaceId;
    private $m_assosciatePublicIpAddress;
    private $m_deviceIndex;
    private $m_subnetId;
    private $m_groups; # not sure what this represents.
    private $m_description;
    private $m_securityGroupId; # - string|array - Pass a string for a single value, or an indexed array for multiple values.
    private $m_deleteOnTermination;
    private $m_privateIpAddresses; # array of private ip addresses.
    private $m_secondaryPrivateIpAddress;
    private $m_secondaryPrivateIpAddressCount;
    
    # Helper member variables not directly related to SDK
    private $m_hasSpecifiedPrimaryIp = false;
    
    
    /**
     * Utilize one of the public create methods to create one of these objects.
     */
    private function __construct(){}
    
    
    
    /**
     * Create a NetworkInterfaceObject that represents one that already exists in Amazon
     * @param type $network_interface_id - the id of the network interface to attach to.
     * @param bool $assosciatePublicIp - whether to dynamically allocate a public ip to the NIC.
     * @param type $deviceIndex - ???
     * @param type $subnetId
     * @param Array<PrivateIp> $privateIp
     * @param type $securityGroupId
     * @param bool $deleteOnTermination - flag indicating whether this interface should be destroyed
     *                                    when the ec2 instance is terminate.
     * @param int $secondary_private_ip_address_count - The number of secondary private IP addresses 
     *                                              that Amazon EC2 automatically assigns to the 
     *                                              network interface. Amazon EC2 uses the value 
     *                                              of the PrivateIpAddress property as the primary 
     *                                              private IP address. If you don't specify that 
     *                                              property, Amazon EC2 automatically assigns both
     *                                              the primary and secondary private IP addresses.
     * param String $description - optionally set a description for the interface.
     */
    public function createFromExisting($network_interface_id,
                                       $assosciatePublicIp,
                                       $deviceIndex,
                                       $subnetId,
                                       $privateIp,
                                       $securityGroupId,
                                       $deleteOnTermination,
                                       $secondary_private_ip_address_count,
                                       $description='')
    {
        self::validate_secondary_ip_addresss_count($secondary_private_ip_address_count);
        self::validate_ip_addresses($privateIp, $secondary_private_ip_address_count);
        
        $this->m_assosciatePublicIpAddress = $assosciatePublicIp;
        $this->m_privateIpAddress = $privateIp;
        $this->m_networkInterfaceId = $network_interface_id;
        $this->m_deviceIndex = $deviceIndex;
        $this->m_subnetId = $subnetId;
        
        $this->m_deleteOnTermination = $deleteOnTermination;
        $this->m_secondaryPrivateIpAddress = $secondary_private_ip_address_count;
        $this->m_securityGroupId = $securityGroupId;
        $this->m_description = $description;
    }
    
    /**
     * Create a new NIC from scratch. This will let AWS create an ID etc.
     * @param bool $assosciatePublicIp - whether to assosciate a public ip with the NIC.
     * @param bool $deleteOnTermination - whether to delete this NIC when the EC2 instance is 
     *                                    terminated.
     * @return NetworkInterface
     */
    public static function createNew($assosciatePublicIp, $deleteOnTermination)
    {
        $networkInterface = new NetworkInterface();
        $networkInterface->m_deleteOnTermination = $deleteOnTermination;
        $networkInterface->m_assosciatePublicIpAddress = $assosciatePublicIp;
        return $networkInterface;
    }
    
    
    /**
     * Add a private IP to the network interface.
     * @param string $ip - the private IP, e.g. 192.168.1.1
     * @param bool $isPrimary - specify whether this is the primary private IP address. You can only
     *                          have one primary.
     */
    public function addPrivateIp($ip, $isPrimary)
    {
        if ($isPrimary)
        {
            if ($this->m_hasSpecifiedPrimaryIp)
            {
                throw new Exception("You cannot specify two primary IPs on a network interface!");
            }
            
            $this->m_hasSpecifiedPrimaryIp = true;
        }
        
        $privateIp = new \stdClass();
        $privateIp->ip = $ip;
        $privateIp->isPrimary = $isPrimary;
        $this->m_privateIpAddresses[] = $privateIp;
    }
    
    
    /**
     * Converts this object into an array form that can be used for requests.
     * @return Array - assoc array of this object for a request.
     */
    public function toArray()
    {
        $arrayForm = array();
        
        if (isset($this->m_assosciatePublicIpAddress))
        {
            $arrayForm['AssociatePublicIpAddress'] = $this->m_assosciatePublicIpAddress;
        }
        
        if (isset($this->m_deleteOnTermination))
        {
            $arrayForm['DeleteOnTermination'] = $this->m_deleteOnTermination;
        }
        
        if (isset($this->m_description))
        {
            $arrayForm['Description'] = $this->m_description;
        }
        
        if (isset($this->m_deviceIndex))
        {
            $arrayForm['DeviceIndex'] = $this->m_deviceIndex;
        }
        
        if (isset($this->m_groups))
        {
            $arrayForm['Groups'] = $this->m_groups;
        }
        
        if (isset($this->m_networkInterfaceId))
        {
            $arrayForm['NetworkInterfaceId'] = $this->m_networkInterfaceId;
        }
        
        if (count($this->m_privateIpAddresses) > 0)
        {
            if (count($this->m_privateIpAddresses) == 1)
            {
                $privateIp = array_shift($this->m_privateIpAddresses);
                $arrayForm['PrivateIpAddress'] = $privateIp->ip;
            }
            else
            {
                $privateIpAddresses = array();
                
                foreach ($this->m_privateIpAddresses as $privateIp)
                {
                    $privateIpAddresses[] = array(
                        'Primary'          => $privateIp->isPrimary,
                        'PrivateIpAddress' => $privateIp->ip
                    );
                }
                
                $arrayForm['PrivateIpAddresses'] = $privateIpAddresses;
            }
        }
        
        if (isset($this->m_secondaryPrivateIpAddressCount))
        {
            $arrayForm['SecondaryPrivateIpAddressCount'] = $this->m_secondaryPrivateIpAddressCount;
        }
        
        if (isset($this->m_subnetId))
        {
            $arrayForm['SubnetId'] = $this->m_subnetId;
        }
        
        return $arrayForm;
    }
    
    
    /**
     * Validates the ip addresses passed to this object. This ensures that they are of the correct
     * type (PrivateIp) and that there are not two primaries.
     * @param array $ipAddresses
     * @param int $secondaryPrivateIpAddressCount - 
     * @throws Exception
     */
    private static function validate_ip_addresses(Array $ipAddresses, $secondaryPrivateIpAddressCount)
    {
        if (count($ipAddresses) == 0 && $secondaryPrivateIpAddressCount == 0)
        {
            throw new \Exception('You need to provide a private ip, or set ' .
                                '$secondaryPrivateIpAddressCount to be larger than 0');
        }
        
        $have_primary = false;
        
        foreach ($ipAddresses as $ip)
        {
            if (!($ip instanceof PrivateIp))
            {
                throw new \Exception('Network interface ips need to be instances of PrivateIp');
            }
            
            if ($ip->is_primary())
            {
                if ($have_primary)
                {
                    throw new \Exception('Cannot have two primary private ip addresses');
                }
                else
                {
                    $have_primary = true;
                }
            }
        }
        
        # If primary Ip is not set on private, one of amazons allocated ips from 
        # $secondaryPrivateIpAddressCount will be made primary, so check if this is 0 when no 
        # primary set.
        if (!$have_primary && $secondaryPrivateIpAddressCount == 0)
        {
            throw new \Exception('Need a primary IP!');
        }
    }
    
    
    /**
     * Validates that a user provided secondaryIpAddress count is acceptable.
     * @param int $secondaryPrivateIpAddressCount - the user specified 
     *            secondaryPrivateIpAddressCount.
     * @throws Exception
     */
    private static function validate_secondary_ip_addresss_count($secondaryPrivateIpAddressCount)
    {
        if (!is_int($secondaryPrivateIpAddressCount))
        {
            throw new \Exception('secondaryPrivateIpAddressCount needs to be an integer');
        }
        
        if ($secondaryPrivateIpAddressCount < 0)
        {
            throw new \Exception('secondaryPrivateIpAddressCount cannot be less than 0');
        }
    }
}
