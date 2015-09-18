<?php

namespace iRAP\AwsWrapper\Requests;

/*
 * Class for spot instance requests.
 * Please refer to the documentation at:
 * http://docs.aws.amazon.com/AWSSDKforPHP/latest/#m=AmazonEC2/request_spot_instances
 */

class RequestSpotInstance extends Ec2RequestAbstract
{
    private $m_price;
    private $m_num_instances;
    private $m_instance_type;
    private $m_imageId;
    private $m_availability_zone;    
    private $m_security_group;
    private $m_launch_specification;
    private $m_valid_from = null; # This does not have to ever be set e.g. optional
    private $m_valid_until = null; # This does not have to ever be set e.g. optional
    private $m_launch_group = null; # This does not have to ever be set e.g. optional
    private $m_generatedSpotRequestIds = array();
    
    
    /**
     * 
     * @param AmazonRegion $region
     * @param SpotInstanceType $spot_instance_type - the type of ec2 instance we wish to launch.
     * @param float $price - the bidding price for the spot request of the instances. If the 
     *                       current price is higher than that, they will not be deployed. Also,
     *                       if the bidding price is exceeded during the lifetime of the instance
     *                       they will be terminated.
     * @param LaunchSpecification $launch_specification - the launch specification of the request
     *                                                   refer to that object for details.
     * @param int $num_instances - the number of spot instances you wish to launch.
     */
    public function __construct(\iRAP\AwsWrapper\Enums\AwsRegion $availability_zone, 
                                \iRAP\AwsWrapper\Enums\SpotInstanceType $spot_instance_type, 
                                $price,
                                \iRAP\AwsWrapper\Objects\LaunchSpecification $launch_specification,
                                $num_instances)
    {
        self::validate_price($price);
        
        $this->m_availability_zone    = $availability_zone;
        $this->m_spotInstanceType     = $spot_instance_type;
        $this->m_price                = $price;
        $this->m_launch_specification = $launch_specification;
        $this->m_num_instances        = $num_instances;
    }
    
    
    /**
     * Generates the $opts parameter for the request. Note that the returnCurlHandle and $curlopts
     * options are automatically added from our parent.
     * @param void
     * @return Array $options - the $opts parameter of the request.
     */
    public function getOptionsArray()
    {
        $options = array(
            'InstanceCount' => $this->m_num_instances,
            'Type'          => (string)$this->m_spotInstanceType,
        );
        
        if ($this->m_valid_from != null)
        {
            $options['ValidFrom'] = $this->m_valid_from;
        }
        
        if ($this->m_valid_until != null)
        {
            $options['ValidUntil'] = $this->m_valid_until;
        }
        
        if ($this->m_launch_group != null)
        {
            $options['LaunchGroup'] = $this->m_launch_group;
        }
        
        $options['AvailabilityZoneGroup'] = (String)$this->m_availability_zone;
        
        $options['LaunchSpecification'] = $this->m_launch_specification->toArray();
        
        return $options;
    }
    
    
    /**
     * Specify the end date of the request. If this is a one-time request, the request remains 
     * active until all instances launch, the request is canceled, or this date is reached. If the 
     * request is persistent, it remains active until it is canceled or this date and time is 
     * reached. May be passed as a number of seconds since UNIX Epoch, or any string compatible 
     * with strtotime().
     * @param string $date_time - a string that represents when the spot request is valid from
     * @return void
     */
    public function set_valid_from($date_time)
    {
        self::validate_time_string($date_time);
        $this->m_valid_from = $date_time;
    }
    
    
    /**
     * Specify the end date of the request. If this is a one-time request, the request remains 
     * active until all instances launch, the request is canceled, or this date is reached. If the 
     * request is persistent, it remains active until it is canceled or this date and time is 
     * reached. May be passed as a number of seconds since UNIX Epoch, or any string compatible 
     * with strtotime().
     * @param string dateTime - a string that represents when the spot request is valid until
     * @return void
     */
    public function set_valid_until($dateTime)
    {
        self::validate_time_string($dateTime);
        $this->m_valid_until = $dateTime;
    }
    
    
    /**
     * Set the optional launch group. 
     * Launch groups are Spot Instances that launch and terminate together.
     * @param string $group - the group the spot instances should be part of.
     * @return void.
     */
    public function set_launch_group($group)
    {
        print "launch group validation has yet to be implemented" . PHP_EOL;
        $this->m_launch_group = $group;
        
    }
    
    
    /**
     * Validate that a datetime string is in an acceptable format that it will be recognized by aws.
     * Note: Even if the user fed in an epoch integer, it would still return TRUE, although in 
     * a strtotime() conversion it would return something else.
     * @throws Exception
     */
    private static function validate_time_string()
    {
        if (strtotime($dateTime) === FALSE)
        {
            $errMsg = 
                'Time strings need to be compatible with strToTime for spot instnac requests';
            
            throw new \Exception($errMsg);
        }
    }
    
    
    /**
     * Helper function to validate price. 
     * @param float $price - the price to be validated.
     * @throws Exception if the input variable is not a valid price.
     */
    private static function validate_price($price)
    {
        if (!is_float($price))
        {
            throw new \Exception('Price in spot instance request must be a float');
        }
        
        if ($price <= 0)
        {
            throw new \Exception('Price must be a positive value!');
        }
    }
    
    
    /**
     * Sends the request to the AWS api. This is called from our parent.
     * @param \Aws\Ec2\Ec2Client $ec2 - the ec2 aws client
     * @param array $options - all the parameter/specifications.
     * @return CFResponse $response - response from the AWS API
     */
    protected function sendRequest(\Aws\Ec2\Ec2Client $ec2, array $options) 
    {
        $ec2->set_region($this->m_availability_zone); # Ami ids wont be recognized without this for some reason
        $response = $ec2->request_spot_instances($this->m_price, $options);
        
        foreach ($response->body->item() AS $item)
        {
            $this->m_generatedSpotRequestIds[] = $item->spotInstanceRequestId;
        }
        
        # For some reason you usually end up with an empty element from previous operation.
        $this->m_generatedSpotRequestIds = array_filter($this->m_generatedSpotRequestIds);
        
        return $response;
    }
    
    
    /**
     * Return all the spot request ids that have been generated by this object.
     * Note that this will be empty until "send" has been called at least once.
     * @param void
     * @return Array - array list of all the spot request ids. (not the spot request itself, but
     *                 one per instance)
     */
    public function get_generated_request_ids()
    {
        return $this->m_generatedSpotRequestIds;
    }
}

