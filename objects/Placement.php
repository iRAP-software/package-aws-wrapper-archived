<?php

namespace iRAP\AwsWrapper\Objects;

/* 
 * An object to represent the Placement of an EC2 instance
 * http://docs.aws.amazon.com/AWSSDKforPHP/latest/#m=AmazonEC2/request_spot_instances
 */

class Placement
{
    private $m_availability_zone = null;
    private $m_group_name = null;
    private $m_tenancy;
    
    /**
     * Private constructor to prevent outside creation. To generate a Placement object please use
     * one of the static create methods.
     */
    private function __construct(){}
    
    
    /**
     * Creates and returns a Placement object with the region specified.
     * @param AmazonRegion $region - the region that we are placing in.
     * @return Placement $placement - the generated Placement object.
     */
    public static function createWithAvailabilityZone(Ec2Region $region)
    {
        $placement = new Placement();
        $placement->m_availability_zone = $region;
        return $placement;
    }
    
    
    
    /**
     * Create one of these objects by parsing the stdobject returned from an AWS api request
     * @param stdObject $std_object_placement - the stdobject from aws api.
     * @return Placmenet $placement;
     */
    public static function createFromAwsApi($std_object_placement)
    {
        $placement = new Placement();
        $placement->m_availability_zone = (string)$std_object_placement->availabilityZone;
        $placement->m_group_name = (string)$std_object_placement->groupName;
        $placement->m_tenancy = (string)$std_object_placement->tenancy;
        return $placement;
    }
    
    
    /**
     * Creates and returns a Placement object with the groupname specified.
     * 
     * @param String $group_name - The name of the PlacementGroup in which an Amazon EC2 instance 
     *                            runs. Placement groups are primarily used for launching High 
     *                            Performance Computing instances in the same group to ensure fast 
     *                            connection speeds.
     */
    public function createWithGroupName($group_name)
    {
        $placement = new Placement();
        $placement->m_availability_zone = $region;
        return $placement;
    }
    
    
    /**
     * Convert this objeect into an array form that can be used by the aws sdk.
     * @return type
     */
    public function toArray()
    {
        $arrayForm = array();
        
        if ($this->m_availability_zone != null)
        {
            $arrayForm['AvailabilityZone'] = $this->m_availability_zone;
        }
        
        if ($this->m_group_name != null)
        {
            $arrayForm['GroupName'] = $this->m_group_name;
        }
        
        if ($this->m_tenancy != null)
        {
            $arrayForm['Tenancy'] = $this->m_tenancy;
        }
        
        return $arrayForm;
    }
}