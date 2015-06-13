<?php

namespace iRAP\AwsWrapper\Requests;

/* 
 * A request to terminate a single or multiple instances.
 */

class RequestTerminateInstance extends Ec2RequestAbstract
{
    private $m_instance_ids = array();
    private $m_region;
    
    
    /**
     * Create a request to terminate one or more ec2 instances.
     * @param AmazonRegion $region - the region the instances are located in.
     * @param mixed $instance_ids - a single instance id or an array of instance ids
     */
    public function __construct(\Irap\AwsWrapper\Enums\AmazonRegion $region, $instance_ids)
    {
        $this->m_region = $region;
        
        if (is_array($instance_ids))
        {
            $this->m_instance_ids = $instance_ids;
        }
        else
        {
            if (!is_string($instance_ids) || $instance_ids == '')
            {
                $errMsg = 'TerminateInstanceRequest: instance_ids needs to be an array of ' .
                          'instance ids or a string representing a single instance id.';
                throw new \Exception($errMsg);
            }
            
            $this->m_instance_ids[] = $instance_ids;
        }
        
    }
    
    protected function get_options_array()
    {
        return array();
    }
    
    /**
     * Add another instance to list of instances to terminate.
     * @param type $instance_id - the unique ID of the instance we wish to terminate.
     */
    public function add_instance($instance_id)
    {
        $this->m_instance_ids[] = $instance_id;
    }

    
    protected function send_request(\AmazonEC2 $ec2, array $opt)
    {
        $ec2->set_region((string)$this->m_region);
        $response = $ec2->terminate_instances($this->m_instance_ids, $opt);
        return $response;
    }

}

