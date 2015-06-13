<?php

namespace iRAP\AwsWrapper\Requests;

/*
 * Class for spot instance requests.
 * Please refer to the documentation at:
 * http://docs.aws.amazon.com/AWSSDKforPHP/latest/#m=AmazonEC2/request_spot_instances
 */

class RequestRunInstances extends Ec2RequestAbstract
{
    private $m_region; # the region the request is sent to.
    private $m_image_id;
    private $m_max_count;
    private $m_min_count;
    private $m_disable_api_termination= null;
    private $m_launch_specification;
    private $m_licence_pools = array();
    private $m_client_token = null;
    
    # Array list of any generated instances created when request(s) sent.
    private $m_generated_instances = array();
    
    
    /**
     * Create the RunInstancesRequest
     * 
     * @param int maxCount - Maximum number of instances to launch. If the value is more than 
     *                       Amazon EC2 can launch, the largest possible number above minCount will
     *                       be launched instead. Between 1 and the maximum number allowed for your
     *                       account (default: 20).
     * @param int minCount - The minimum number of instances to launch. If the value is more than
     *                       Amazon EC2 can launch, no instances are launched at all.
     * @param LaunchSpecification $launch_specification - the launch specification of the request
     *                                                   refer to that object for details.
     */
    public function __construct(\Irap\AwsWrapper\Enums\AmazonRegion $region,
                                \Irap\AwsWrapper\Objects\LaunchSpecification $launch_specification,
                                $maxCount, 
                                $minCount)
    {
        if ($minCount <= 0)
        {
            # AWS itself throws an error if you set minimum to 0 which is odd IMO. I would figure
            # 0 was a legitimate request to spawn as many as possible but dont throw an error if
            # cant even fill one.
            throw new \Exception('minCount for RunInstancesRequest must be greater than 0');
        }
        
        $this->m_region    = $region;
        $this->m_image_id  = $launch_specification->getImageId();
        $this->m_max_count = $maxCount;
        $this->m_min_count = $minCount;
        $this->m_launch_specification = $launch_specification;
    }
    
    
    /**
     * Disable the possibility for these instances to be terminated from an API request.
     * @param bool $flag - optionally set to false if you want to disable which is already 
     *                     default setting.
     */
    public function disableApiTermination($flag = true)
    {
        $this->m_disable_api_termination = $flag;
    }
    
    
    /**
     * Add a license pool from which to take a license when starting Amazon EC2 instances in the 
     * associated RunInstances request.
     * @param String $licencePool - the pool representing where licences can be used.
     * @return void
     */
    public function add_licence_pool($licencePool)
    {
        $this->m_licence_pools[] = $licencePool;
    }
    
    
    /**
     * Set a token to prevent the accidental launch of multiple instances
     * Please refer to:
     * http://docs.aws.amazon.com/AWSEC2/latest/UserGuide/Run_Instance_Idempotency.html
     */
    public function set_client_token($token)
    {
        $this->m_client_token = $token;
    }
    
    
    /**
     * Set the instances to terminate their EBS volumes when they are terminated. If you do not
     * run this, then the ebs volumes are kept.
     * You can refer to here:
     * http://alestic.com/2010/01/ec2-instance-locking
     * @param type $flag - optionally set to false to set the settings back to the default of only
     *                     stopping ebs volumes on ec2 termination.
     * @return void.
     */
    public function terminate_ebs_on_termination($flag=true)
    {
        if ($flag)
        {
            $this->m_terminateEbsOnTermination = "terminate";
        }
        else
        {
            $this->m_terminateEbsOnTermination = "stop";
        }
    }
    
    
    /**
     * Sends the request off to amazon API
     * The majority this functions body is sorting out the differences between this requests
     * options and the LaunchSpecification in spot_instance_request.
     * @return Array $options - the options for the request.
     */
    public function get_options_array()
    {
        $options = $this->m_launch_specification->to_array();
        
        # ImageId was moved to the run_instances method rather than the options, so we unset it 
        # here.
        unset($options['ImageId']);
        
        if (isset($options['GroupSet']))
        {
            unset($options['GroupSet']);
        }
        
        # S.P - Dont ask me why the run_instances request has a different key name for the exact  
        # same set of configuration vars.
        if (isset($options['NetworkInterface']))
        {
            $options['NetworkInterface'] = $options['NetworkInterfaceSet'];
            unset($options['NetworkInterfaceSet']);
        }
        
        if (isset($this->m_disable_api_termination))
        {
            $options['DisableApiTermination'] = $this->m_disable_api_termination;
        }
        
        if (count($this->m_licence_pools) > 0)
        {
            $licencePools = array();
            
            foreach($this->m_licence_pools as $pool)
            {
                # AWS format is crazy huh?
                $licencePools[] = array('Pool' => $pool);
            }
            
            $options['License'] = $licencePools;
        }
        
        if (isset($this->m_instanceInitiatedShutdownBehavior))
        {
            $options['InstanceInitiatedShutdownBehavior'] = $this->m_terminateEbsOnTermination;
        }
        
        if (isset($this->m_client_token))
        {
            $options['ClientToken'] = $this->m_client_token;
        }
        
        return $options;
    }
    
    
    /**
     * Send the request to spawn instances!
     * @param AmazonEC2 $ec2 - the ec2 client (from sdk) that actaully makes the requst
     * @param array $opt - the optional array to put into the request generated from this object.
     */
    protected function send_request(\AmazonEC2 $ec2, array $opt) 
    {
        $ec2->set_region($this->m_region);
        
        /* @var $response CFResponse */
        $response = $ec2->run_instances($this->m_image_id, 
                                        $this->m_min_count, 
                                        $this->m_max_count, 
                                        $opt);
        
        if ($response->isOK())
        {
            $ec2InstanceStdObjs = $response->body->instancesSet->item;
            
            foreach ($ec2InstanceStdObjs as $ec2StdObj)
            {
                $this->m_generated_instances[] = \Irap\AwsWrapper\Objects\Ec2Instance::create_from_aws_item($ec2StdObj);
            }
        }
        
        return $response;
    }
    
    
    /**
     * Fetch the instances that have been launched from this request. 
     * @param void 
     * @return Array<Ec2Instance> - array list of ec2Instance objects.
     */
    public function get_spawned_instances() { return $this->m_generated_instances; }
    

}


