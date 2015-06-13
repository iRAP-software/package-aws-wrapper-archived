<?php

namespace iRAP\AwsWrapper\Requests;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class RequestDescribeInstances extends Ec2RequestAbstract
{
    private $m_region; 
    private $m_filter = null;
    private $m_instance_ids = array();
    private $m_instances = array(); # array for holding returned instances.
    private $m_returned_instance_ids = array();
    
    
    /**
     * Create a request for fetching information about instances.
     * @param AmazonRegion $region - the region that you want the instances for. Unfortunately it
     *                               is not possible to get instances for all regions in one request
     * @param Array $instance_ids - optionally specify an array of instance ids or a string 
     *                             representing a single one.
     * @return RequestDescribeInstances
     */
    public function __construct(\Irap\AwsWrapper\Enums\AmazonRegion $region, $instance_ids=array())
    {
        $this->m_region = $region;
        
        if (is_array($instance_ids))
        {
            foreach ($instance_ids as $instanceId)
            {
                $this->m_instance_ids[$instanceId] = 1;
            }
        }
        else
        {
            $this->m_instance_ids = array($instance_ids => 1);
        }
    }
    
    protected function get_options_array() 
    {
        $options = array();
        
        if ($this->m_filter != null)
        {
            $options['Filter'] = $this->m_filter->to_array();
        }
        
        if (count($this->m_instance_ids) > 0)
        {
            $options['InstanceId'] = array_keys($this->m_instance_ids);
        }
                
        return $options;
    }

    
    
    /**
     * Sends the request to AWS. Note that this function is not public. You need to call "send" 
     * instead which leads to this being called.
     * @param AmazonEC2 $ec2
     * @param array $opt - the optional parameters to be sent.
     * @return CFResponse $response
     */
    protected function send_request(\AmazonEC2 $ec2, array $opt) 
    {
        $ec2->set_region((String)$this->m_region);
        $response = $ec2->describe_instances($opt);
        
        if ($response->isOK())
        {            
            foreach($response->body->reservationSet->item as $item)
            {
                foreach ($item->instancesSet->item as $instanceSetItem)
                {
                    $ec2Instance = \Irap\AwsWrapper\Objects\Ec2Instance::create_from_aws_item($instanceSetItem);
                    $this->m_instances[] = $ec2Instance;
                    $this->m_returned_instance_ids[] = $ec2Instance->get_instance_id();     
                }
            }
        }
        
        return $response;
    }
    
    
    /**
     * Add an instance to the list of instance you wish to have described. Note that if you do not
     * use this method at least once, then all instances will be considered.
     * @param String $instanceId - the ID of an instance we wish to have described.
     */
    public function add_instance_id($instanceId)
    {
        $this->m_instance_ids[$instanceId] = 1;
    }
    
    
    /**
     * Set a filter for the instances we wish to retrieve.
     * @param \Irap\AwsWrapper\Objects\AmazonFilter $filter
     * @return void.
     */
    public function set_filter(\Irap\AwsWrapper\Objects\AmazonFilter $filter)
    {
        $this->m_filter = $filter;
    }
    
    
    
    /**
     * Returns the instances that were fetched with this request. Note that this will always be
     * empty until send has been called at least once. Note that multiple calls to send will 
     * result in a "stacking" of results.
     * @param void
     * @return Array - array list of instances.
     */
    public function get_instances() { return $this->m_instances; }
    
    
    /**
     * Fetch just the array list of instance ids that hare being described.
     * @return Array<String> - list of instance ids being described.
     */
    public function get_instance_ids() { return $this->m_returned_instance_ids; }

}

