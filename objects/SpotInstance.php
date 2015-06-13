<?php

namespace iRAP\AwsWrapper\Objects;

/* 
 * This represents a spot instance as built from a spot instance request.
 * This is the individual spot requests that may or may not have been fulfilled, and is not 
 * the single spot request that may have spawned multiple SpotInstances.
 */

class SpotInstance
{
    private $m_spot_id;
    private $m_create_time;
    private $m_type;
    private $m_state; # "open" "active" (more to come)
    private $m_instance_type;
    private $m_subnet_id;
    
    private $m_ec2_instance_id = null; #this may not exist (eg. if pending)
    
    private function __construct() {}
    
    
    
    /**
     * Creates a SpotInstance object from an aws api response to describe_spot_instances.
     * @param $item - a single item from the describe_spot_instances.
     * @return SpotInstance - the generated object
     */
    public static function create_from_aws_item($item)
    {
        $spot_instance = new SpotInstance();        
        $spot_instance->m_spot_id        = (String)$item->spotInstanceRequestId;
        $spot_instance->m_creationTime   = strtotime((String)$item->createTime);
        $spot_instance->m_type           = (string)$item->type;
        $spot_instance->m_state          = (String)$item->state;
        
        if (isset($item->instanceType))
        {
            $spot_instance->m_instance_type = (String)$item->instanceType;
        }
        
        if (isset($item->subnetId))
        {
            $spot_instance->m_subnet_id = (String)$item->subnetId;
        }
        
        if (isset($item->instanceId))
        {
            $spot_instance->m_ec2_instance_id = (String)$item->instanceId;
            # Im surprised, but this actually works even though the string is like: 
            # "2014-01-14T15:53:51.000Z" 
            $spot_instance->m_create_time = strtotime((string)$item->createTime);
        }
        
        return $spot_instance;
    }
    
    # accessors
    public function getSpotInstanceId() { return $this->m_spot_id; }
    public function getEc2InstanceId()  { return $this->m_ec2_instance_id; }
    public function getDeploymentTime() { return $this->m_create_time; }
    public function getState()          { return $this->m_state; }
}