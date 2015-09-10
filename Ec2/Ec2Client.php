<?php

/* 
 * Client for interfacing with AWS Ec2
 * You may find this useful:
 * http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-ec2-2015-04-15.html
 */

namespace iRAP\AwsWrapper\Ec2;

class Ec2Client
{
    private $m_client;
    
    public function __construct($apiKey, $apiSecret, \iRAP\AwsWrapper\Enums\Ec2Region $region)
    {
        $credentials = array(
            'key'    => $apiKey,
            'secret' => $apiSecret
        );
        
        $params = array(
            'credentials' => $credentials,
            'version'     => '2006-03-01',
            'region'      => (string) $region,
        );
        
        $this->m_client = new \Aws\Ec2\Ec2Client($params);
    }
    
    
    /**
     * http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-ec2-2015-04-15.html#createimage
     */
    public function createImage()
    {
        
    }
    
    
    public function createKeyPair()
    {
        
    }
    
    
    /**
     * http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-ec2-2015-04-15.html#createsnapshot
     */
    public function createSnapshot()
    {
        
    }
    
    
    public function deleteSnapshot()
    {
        
    }
    
    
    public function CancelSpotInstanceRequests()
    {
        
    }
    
    
    /**
     * 
     * http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-ec2-2015-04-15.html#describeinstances
     */
    public function DescribeInstances()
    {
        
    }
    
    
    /**
     * http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-ec2-2015-04-15.html#runinstances
     */
    
    /**
     * 
     * @param \iRAP\AwsWrapper\Objects\BlockDeviceMapping $blockDeviceMapping
     * @param type $imageId
     * @param \iRAP\AwsWrapper\Enums\Ec2InstanceType $instanceType
     * @param type $dryRun
     * @param type $kernelId
     * @param bool $disableApiTermination 
     * @param int $maxCount
     * @param int $minCount
     * @param bool $monitoringEnabled;
     */
    public function RunInstances(\iRAP\AwsWrapper\Objects\BlockDeviceMapping $blockDeviceMapping,
                                 $imageId,
                                 \iRAP\AwsWrapper\Enums\Ec2InstanceType $instanceType,
                                 \iRAP\AwsWrapper\Objects\Placement $placement,
                                 $dryRun = false,
                                 $ebsOptimized = false,
                                 $kernelId = '',
                                 $ramDiskId = '',
                                 $disableApiTermination,
                                 $maxCount,
                                 $minCount,
                                 $monitoringEnabled = false,
                                 $subnetId,
                                 $privateIpAddress = '',
                                )
    {
        $params = array(
            'BlockDeviceMappings' => $blockDeviceMapping->toArray(),
            'ClientToken' => '<string>',
            'DisableApiTermination' => $disableApiTermination,
            'DryRun' => $dryRun,
            'EbsOptimized' => $ebsOptimized,
            'IamInstanceProfile' => [
                'Arn' => '<string>',
                'Name' => '<string>',
            ],
            'ImageId' => $imageId, // REQUIRED
            'InstanceInitiatedShutdownBehavior' => 'stop|terminate',
            'InstanceType' => (string) $instanceType,
            
            'KeyName' => '<string>',
            'MaxCount' => $maxCount, // REQUIRED
            'MinCount' => $minCount, // REQUIRED
            'Monitoring' => [
                'Enabled' => $monitoringEnabled, // REQUIRED
            ],
            'NetworkInterfaces' => [
                [
                    'AssociatePublicIpAddress' => true || false,
                    'DeleteOnTermination' => true || false,
                    'Description' => '<string>',
                    'DeviceIndex' => <integer>,
                    'Groups' => ['<string>', ...],
                    'NetworkInterfaceId' => '<string>',
                    'PrivateIpAddress' => '<string>',
                    'PrivateIpAddresses' => [
                        [
                            'Primary' => true || false,
                            'PrivateIpAddress' => '<string>', // REQUIRED
                        ],
                        // ...
                    ],
                    'SecondaryPrivateIpAddressCount' => <integer>,
                    'SubnetId' => '<string>',
                ],
                // ...
            ],
            'Placement' => $placement->toArray(),
            'SecurityGroupIds' => ['<string>', ...],
            'SecurityGroups' => ['<string>', ...],
            'SubnetId' => $subnetId,
            'UserData' => '<string>',
        );
        
        if ($kernelId !== '')
        {
            $params['KernelId'] = $kernelId;
        }
        
        if ($ramDiskId !== '')
        {
            $params['RamdiskId'] = $ramDiskId;
        }
        
        if ($privateIpAddress !== '')
        {
            $params['PrivateIpAddress'] = $privateIpAddress;
        }
        
        $result = $client->runInstances($params);
    }
    
    
    /**
     * Alias for RunInstances
     */
    public function RequestOnDemandInstances()
    {
        $this->RunInstances();
    }
    
    
    public function RequestSpotInstances()
    {
        
    }
    
    
    public function RequestSpotFleet()
    {
        
    }
    
    /**
     * http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-ec2-2015-04-15.html#startinstances
     */
    public function StartInstances()
    {
        
    }
    
    
    public function StopInstances()
    {
        
    }
    
    
    public function TerminateInstances()
    {
        
    }
}