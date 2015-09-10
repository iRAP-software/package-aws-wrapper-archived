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
    private $m_maxCount;
    private $m_minCount;
    private $m_disableApiTermination = null;
    private $m_launchSpecification;
    private $m_clientToken = null;
    private $m_dryRun = false;
    private $m_instanceInitiatedShutdownBehavior = 'stop';
    
    # Array list of any generated instances created when request(s) sent.
    private $m_generatedInstances = array();
    
    
    /**
     * Create the RunInstancesRequest
     * 
     * @param int maxCount - Maximum number of instances to launch. If the value is more than 
     *                       Amazon EC2 can launch, the largest possible number above minCount will
     *                       be launched instead. Between 1 and the maximum number allowed for your
     *                       account (default: 20).
     * @param int minCount - The minimum number of instances to launch. If the value is more than
     *                       Amazon EC2 can launch, no instances are launched at all.
     * @param LaunchSpecification $launchSpecification - the launch specification of the request
     *                                                   refer to that object for details.
     */
    public function __construct(\iRAP\AwsWrapper\Enums\Ec2Region $region,
                                \iRAP\AwsWrapper\Objects\LaunchSpecification $launchSpecification,
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
        
        $this->m_region   = $region;
        $this->m_image_id = $launchSpecification->getImageId();
        $this->m_maxCount = $maxCount;
        $this->m_minCount = $minCount;
        $this->m_launchSpecification = $launchSpecification;
    }
    
    
    /**
     * Disable the possibility for these instances to be terminated from an API request.
     * @param bool $flag - optionally set to false if you want to disable which is already 
     *                     default setting.
     */
    public function disableApiTermination($flag = true)
    {
        $this->m_disableApiTermination = $flag;
    }
    
    
    /**
     * Set a token to prevent the accidental launch of multiple instances
     * Please refer to:
     * http://docs.aws.amazon.com/AWSEC2/latest/UserGuide/Run_Instance_Idempotency.html
     */
    public function setClientToken($token)
    {
        $this->m_clientToken = $token;
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
    public function terminateEbsOnTermination($flag=true)
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
    public function getOptionsArray()
    {
        $options = $this->m_launchSpecification->toArray();
        
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
        
        if (isset($this->m_disableApiTermination))
        {
            $options['DisableApiTermination'] = $this->m_disableApiTermination;
        }
        
        $options['InstanceInitiatedShutdownBehavior'] = $this->m_instanceInitiatedShutdownBehavior;
        
        if (isset($this->m_clientToken))
        {
            $options['ClientToken'] = $this->m_clientToken;
        }
        
        if ($this->m_dryRun === true)
        {
            $options['DryRun'] = $this->m_dryRun;
        }
        
        return $options;
    }
    
    
    /**
     * Send the request to spawn instances!
     * @param \Aws\Ec2\Ec2Client $ec2Client - the ec2 client (from sdk) that actaully makes the requst
     * @param array $options - the optional array to put into the request generated from this object.
     */
    protected function sendRequest(\Aws\Ec2\Ec2Client $ec2Client, array $options) 
    {        
        /* @var $response CFResponse */
        $response = $ec2Client->runInstances($options);
        
        if ($response->isOK())
        {
            $ec2InstanceStdObjs = $response->body->instancesSet->item;
            
            foreach ($ec2InstanceStdObjs as $ec2StdObj)
            {
                $this->m_generatedInstances[] = \iRAP\AwsWrapper\Ec2\Ec2Instance::createFromAwsItem($ec2StdObj);
            }
        }
        
        return $response;
    }
    
    
    public function setDryRun($dryRun = true)
    {
        $this->m_dryRun = $dryRun;
    }
    
    
    /**
     * Run this method to have the instance terminate when it is shutdown, rather than just 
     * being held in a stopped state. Note that you are not charged for the instance in the stopped
     * state and this is the default behaviour when shutdown. 
     * If termination on shutdown is enabled, the API can essentially terminate by shutting it down
     * even if DisableApiTermination is set
     */
    public function terminateInstanceOnShutdown()
    {
        $this->m_instanceInitiatedShutdownBehavior = 'terminate';
    }
    
    
    /**
     * Fetch the instances that have been launched from this request. 
     * @param void 
     * @return Array<Ec2Instance> - array list of ec2Instance objects.
     */
    public function getSpawnedInstances() { return $this->m_generatedInstances; }
}


