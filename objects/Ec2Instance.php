<?php

namespace iRAP\AwsWrapper\Objects;

/* 
 * This object represents an ec2 instance as described from a describeInstances request.
 */

class Ec2Instance
{
    private $m_instance_id;
    private $m_image_id;
    private $m_instance_state_code;
    private $m_instance_state_name;
    private $m_private_dns_name;
    private $m_dns_name;
    private $m_reason;
    private $m_key_name;
    private $m_ami_launch_index;
    private $m_product_codes;
    private $m_instance_type; # # e.g. t1.micro
    private $m_launch_time; # unix timestamp
    private $m_placement;
    private $m_kernel_id;
    private $m_monitoring;
    private $m_subnet_id;
    private $m_vpc_id;
    private $m_private_ip_address;
    private $m_ip_address;
    private $m_source_dest_check;
    private $m_group_set;
    private $m_architecture;
    private $m_root_device_type;
    private $m_root_device_name;
    private $m_block_device_mapping;
    private $m_instance_lifecycle;
    private $m_spot_instance_request_id; # may or may not be set
    private $m_virtualization_type;
    private $m_client_token;
    private $m_tag_set;
    private $m_hypervisor;
    private $m_network_interface_set;
    private $m_ebs_optimized; # flag
    
    private function __construct() {}
    
    
    /**
     * Creates an Ec2Instance object from the $item stdObject returned from an amazon request.
     * Unfortunately without casting, all values get set as simplexml objects.
     * @param type $ec2Instance
     * @return Ec2Instance
     */
    public static function create_from_aws_item($item)
    {
        $ec2Instance = new Ec2Instance();
        
        $ec2Instance->m_instance_id                 = (string)$item->instanceId;
        $ec2Instance->m_image_id                    = (string)$item->imageId;
        $ec2Instance->m_instance_state_code         = intval($item->instanceState->code); # e.g 16 (running)
        $ec2Instance->m_instance_state_name         = @(string)$item->instanceState->name; # e.g. running
        $ec2Instance->m_private_dns_name            = @(string)$item->privateDnsName;
        $ec2Instance->m_dns_name                    = @(string)$item->dnsName;
        $ec2Instance->m_reason                      = $item->reason; #unknown object
        $ec2Instance->m_key_name                    = @(string)$item->keyName;
        $ec2Instance->m_ami_launch_index            = intval($item->amiLaunchIndex);
        $ec2Instance->m_product_codes               = $item->productCodes; # unknown object
        $ec2Instance->m_instance_type               = @(string)$item->instanceType;
        $ec2Instance->m_launch_time                 = strtotime((string)$item->launchTime); # e.g. 2014-01-13T13:37:09.000Z
        $ec2Instance->m_placement                   = Placement::create_from_aws_api($item->placement);
        $ec2Instance->m_kernel_id                   = @(string)$item->kernelId;
        $ec2Instance->m_monitoring                  = $item->monitoring; # this is an object that needs to be created
        $ec2Instance->m_subnet_id                   = @(string)$item->subnetId;
        $ec2Instance->m_vpc_id                      = @(string)$item->vpcId;
        $ec2Instance->m_private_ip_address          = @(string)$item->privateIpAddress;
        $ec2Instance->m_ip_address                  = @(string)$item->ipAddress;
        $ec2Instance->m_source_dest_check           = ("true" == $item->sourceDestCheck);
        $ec2Instance->m_group_set                   = $item->groupSet; # this is an stdobject that needs defining.
        $ec2Instance->m_architecture                = @(string)$item->architecture;
        $ec2Instance->m_root_device_type            = @(string)$item->rootDeviceType;
        $ec2Instance->m_root_device_name            = @(string)$item->rootDeviceName;
        $ec2Instance->m_block_device_mapping        = $item->blockDeviceMapping; # this is an object that needs defining
        $ec2Instance->m_instance_lifecycle          = @(string)$item->instanceLifecycle;
        
        $ec2Instance->m_spot_instance_request_id    = @(string)$item->spotInstanceRequestId;
        
        $ec2Instance->m_virtualization_type         = @(string)$item->virtualizationType;
        $ec2Instance->m_client_token                = @(string)$item->clientToken;
        $ec2Instance->m_tag_set                     = @$item->tagSet; # this is an object that needs defining.
        $ec2Instance->m_hypervisor                  = @(string)$item->hypervisor;
        $ec2Instance->m_network_interface_set       = @$item->networkInterfaceSet; #  this is an object that needs def
        
        $ec2Instance->m_ebs_optimized = ("true" == $item->ebsOptimized); # convert string to bool
        
        return $ec2Instance;
    }
    
    
    public function get_instance_id()     { return $this->m_instance_id; }
    public function get_state_string()    { return $this->m_instance_state_name; }
    public function get_deployment_time() { return $this->m_launch_time; }
    
    # These accessors may not have a value.
    public function get_spot_instance_request_id() { return $this->m_spot_instance_request_id; }
}

