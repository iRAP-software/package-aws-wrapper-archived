<?php

namespace iRAP\AwsWrapper\Requests;

/* 
 * Wrappar around the describe_subnets ec2 call.
 * Please refer to:
 * http://docs.aws.amazon.com/AWSSDKforPHP/latest/index.html#m=AmazonEC2/describe_subnets
 */

class RequestDescribeSubnets extends Ec2RequestAbstract
{
    private $m_region;
    private $m_filter = null;
    private $m_subnet_ids = array();
    
    /**
     * Create the request for describing spot instances. Dont forget to call the send() method 
     * after having fully configured this object.
     * @param AmazonRegion $region - the region this request will be made to.
     * @param mixed $subnetIds - optionally specify the subnet ID(s) you wish to have described.
     *                           if not specified, then all subnet ids will be fetched,
     *                           alternatively specify a string representing one id, or an array
     *                           of subnet ID strings.
     * @return void
     */
    public function __construct(\iRAP\AwsWrapper\Enums\AwsRegion $region, $subnetIds = null)
    {
        $this->m_region = $region;
        
        if ($subnetIds != null)
        {
            if (is_array($subnetIds))
            {
                $this->m_subnet_ids = $subnetIds;
            }
            else
            {
                # single subnet id specified.
                $this->m_subnet_ids[] = $subnetIds;
            }
        }
    }
    
    
    /**
     * Add a subnet to the list of subnets you wish to have described. If no subnets have been
     * specified then all subnets will be described in the request.
     * @param String $id - the id of the subnet you wish to add to the list of subnets to describe.
     */
    public function add_subnet_id($id)
    {
        $this->m_subnet_ids[] = $id;
    }
    
    
    /**
     * Optionally set the filter for the request. A filter does not need to be set and is
     * completely optional.
     * @param \iRAP\AwsWrapper\Objects\AmazonFilter $filter - the filter you wish to apply.
     */
    public function set_filter(\iRAP\AwsWrapper\Objects\AmazonFilter $filter)
    {
        $this->m_filter = $filter;
    }
    
    
    /**
     * Build the options array that goes into the AWS API request.
     * @param void
     * @return Array $options - name/value pairs for the request.
     */
    protected function getOptionsArray() 
    {    
        $opt = array();
        
        if ($this->m_filter != null)
        {
            /* @var $filter \iRAP\AwsWrapper\Objects\AmazonFilter */
            $filter = $this->m_filter;
            $opt['Filter'] = $filter->to_array();
        }
        
        if ($this->m_subnet_ids != null && count($this->m_subnet_ids) > 0)
        {
            $opt['SubnetId'] = $this->m_subnet_ids;
        }
        
        return $opt;
    }

    
    /**
     * Sends the request to the AWS API
     * @param AmazonEC2 $ec2 - the $ec2 client object from the AWS SDK
     * @param array $opt
     * @return type
     */
    protected function sendRequest(\Aws\Ec2\Ec2Client $ec2, array $opt) 
    {
        $response = $ec2->describe_subnets($opt);
        print("subnets: " . PHP_EOL . print_r($response, true));
        die('Subnet request object has not been complted.');
        return $response;
    }
}

