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
    private $m_description;
    private $m_securityGroupId; # - string|array - Pass a string for a single value, or an indexed array for multiple values.
    private $m_deleteOnTermination = false; # - boolean - Optional -
    private $m_privateIpAddress = array();
    private $m_secondaryPrivateIpAddress;
    
    
    /**
     * 
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
    public function __construct($network_interface_id,
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
     * Converts this object into an array form that can be used for requests.
     * @return Array - assoc array of this object for a request.
     */
    public function to_array()
    {
        $privateIps = array();
        
        foreach ($this->m_privateIpAddress as $ip)
        {
            /* $ip PrivateIp */
            $privateIps[] = $ip->toArray();
        }
        
        $array_form = array(
            'NetworkInterfaceId'             => $this->m_networkInterfaceId,
            'AssociatePublicIpAddress'       => $this->m_assosciatePublicIpAddress,
            'DeviceIndex'                    => $this->m_deviceIndex,
            'SubnetId'                       => $this->m_subnetId,
            'Description'                    => $this->m_description,
            'SecurityGroupId'                => $this->m_securityGroupId,
            'SecondaryPrivateIpAddressCount' => $this->m_secondaryPrivateIpAddress,
            'DeleteOnTermination'            => $this->m_deleteOnTermination,
        );
        
        if (count($privateIps) > 0)
        {
            $array_form['PrivateIpAddresses']  = $privateIps;
        }
        
        return $array_form;
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

