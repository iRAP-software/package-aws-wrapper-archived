<?php

namespace iRAP\AwsWrapper\Requests;

/* 
 * Object to cancel spot instance requests.
 */

class RequestCancelSpotInstance extends Ec2RequestAbstract
{
    private $m_request_id;
    private $m_region;
    
    
    /**
     * Create a request for cancelling spot instances.
     * @param AmazonRegion $region - the region the spot requests were made to.
     * @param mixed $spot_request_id - a single spot request id, or an array list of spot request ids.
     */
    public function __construct(\iRAP\AwsWrapper\Enums\AwsRegion $region, $spot_request_id)
    {
        $this->m_region     = $region;
        $this->m_request_id = $spot_request_id;
    }
    
    
    /**
     * There are no options that we need to add. The generic ones such as curlopt are added by 
     * parent 
     * @return Array - all the options array parameters for cancel_spot_instance_requests.
     */
    protected function getOptionsArray()
    {
        return array();
    }
    
    
    protected function sendRequest(\Aws\Ec2\Ec2Client $ec2, array $opt)
    {
        $ec2->set_region((string)$this->m_region);
        $response = $ec2->cancel_spot_instance_requests($this->m_request_id, $opt);
        return $response;
    }

}