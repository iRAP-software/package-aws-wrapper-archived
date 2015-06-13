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
    private $m_network_interface_id;
    private $m_device_index;
    private $m_subnet_id;
    private $m_description;
    private $m_security_group_id; # - string|array - Pass a string for a single value, or an indexed array for multiple values.
    private $m_delete_on_termination; # - boolean - Optional -
    private $m_private_ip_address = array();
    private $m_secondary_private_ip_address_count;
    
    
    /**
     * 
     * @param type $network_interface_id - the id of the network interface to attach to.
     * @param type $device_index - ???
     * @param type $subnet_id
     * @param Array<PrivateIp> $private_ip_addresses
     * @param type $security_group_id
     * @param bool $delete_on_termination - flag indicating whether this interface should be destroyed
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
                                $device_index,
                                $subnet_id,
                                $private_ip_addresses,
                                $security_group_id,
                                $delete_on_termination,
                                $secondary_private_ip_address_count,
                                $description='')
    {
        self::validate_secondary_ip_addresss_count($secondary_private_ip_address_count);
        self::validate_ip_addresses($private_ip_addresses, $secondary_private_ip_address_count);
        
        $this->m_private_ip_address = $private_ip_addresses;
        $this->m_network_interface_id = $network_interface_id;
        $this->m_device_index = $device_index;
        $this->m_subnet_id = $subnet_id;
        
        $this->m_delete_on_termination = $delete_on_termination;
        $this->m_secondary_private_ip_address_count = $secondary_private_ip_address_count;
        $this->m_security_group_id = $security_group_id;
        $this->m_description = $description;
    }
    
    
    /**
     * Converts this object into an array form that can be used for requests.
     * @return Array - assoc array of this object for a request.
     */
    public function to_array()
    {
        $privateIps = array();
        
        foreach ($this->m_private_ip_address as $ip)
        {
            /* $ip PrivateIp */
            $privateIps[] = $ip->to_array();
        }
        
        $array_form = array(
            'NetworkInterfaceId'             => $this->m_network_interface_id,
            'DeviceIndex'                    => $this->m_device_index,
            'SubnetId'                       => $this->m_subnet_id,
            'Description'                    => $this->m_description,
            'SecurityGroupId'                => $this->m_security_group_id,
            'SecondaryPrivateIpAddressCount' => $this->m_secondary_private_ip_address_count
        );
        
        if (count($privateIps) > 0)
        {
            $array_form['PrivateIpAddresses']  = $privateIps;
        }
        
        if ($this->m_delete_on_termination)
        {
            $array_form['DeleteOnTermination'] = true;
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

