<?php

namespace iRAP\AwsWrapper\Requests;

/* 
 * The request to retrieve information about spot instances.
 * http://docs.aws.amazon.com/AWSSDKforPHP/latest/index.html#m=AmazonEC2/describe_regions
 */

class RequestDescribeSpotInstances extends Ec2RequestAbstract
{
    private $m_region;
    private $m_spot_instance_request_ids = array();
    private $m_filter = null;
    
    private $m_spot_instances = array();
    
    
    private $m_spot_instance_ids = array(); # ec2 instance ids that the requests spawned.

    
    /**
     * 
     * @param AmazonRegion $region - the region that this applies to.
     * @param mixed $spot_instance_id - optionally specify the spot instances you wish to describe. 
     *                                This can be a string representing a single instance, or an
     *                                array list of instances.
     */
    public function __construct(\iRAP\AwsWrapper\Enums\AmazonRegion $region, $spot_instance_id=array())
    {
        $this->m_region = $region;
        
        if (is_array($spot_instance_id))
        {
            $this->m_spot_instance_request_ids = $spot_instance_id;
        }
        else
        {
            $this->m_spot_instance_request_ids[] = $spot_instance_id;
        }
    }
    
    
    /**
     * Add a spotInstanceRequest Id to only describe it (and any other instances registered through
     * this function)
     * @param String $spot_instance_request_id - a spotInstanceRequestId
     * @return void.
     */
    public function addSpotRequestId($spot_instance_request_id)
    {
        $this->m_spot_instance_request_ids[] = $spot_instance_request_id;
    }
    
    
    /**
     * Set a filter for this request for a "search" rather than fetching everything.
     * @param \iRAP\AwsWrapper\Objects\AmazonFilter $filter
     */
    public function setFilter(\iRAP\AwsWrapper\Objects\AmazonFilter $filter)
    {
        $this->m_filter = $filter;
    }
    
    
    /**
     * Create the "opts" array for the request. Refer to:
     * http://docs.aws.amazon.com/AWSSDKforPHP/latest/index.html#m=AmazonEC2/describe_regions
     * @return Array $options
     */
    protected function get_options_array() 
    {
        $options = array();
        
        if (count($this->m_spot_instance_request_ids) > 0)
        {
            $options['SpotInstanceRequestId'] = $this->m_spot_instance_request_ids;
        }
        
        if ($this->m_filter != null)
        {
            $options['Filter'] = $this->m_filter->to_array();
        }
        
        return $options;
    }
    
    
    /**
     * Called by the parent after send(), this sends the request to the AWS API.
     * @param AmazonEC2 $ec2
     * @param array $opt - the options parameter for the request.
     * @return CFResponse
     */
    protected function send_request(\AmazonEC2 $ec2, array $opt) 
    {
        $ec2->set_region((string) $this->m_region);
        $response = $ec2->describe_spot_instance_requests($opt);
        
        if ($response->isOK())
        {
            $items = $response->body->item;        
            $items = $response->body->spotInstanceRequestSet->item;

            foreach ($items as $item)
            {
                $spotInstance = SpotInstance::create_from_aws_item($item);
                $this->m_spot_instances[] = $spotInstance;
                $this->m_spot_instance_ids[] = $spotInstance->getSpotInstanceId();
            }

            # remove any null elements
            $this->m_spot_instance_ids = array_filter($this->m_spot_instance_ids);
        }
        
        return $response;
    }
    
    
    public function get_instance_ids() { return $this->m_spot_instance_ids; }
    public function get_spot_instances() { return $this->m_spot_instances; }

}
