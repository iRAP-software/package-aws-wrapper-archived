<?php

namespace iRAP\AwsWrapper\Objects;

/* 
 * Class for representing Ebs volumes.
 * These are useful for feeding into the BlockDeviceMapping of a LaunchSpecification.
 * refer to:
 * http://docs.aws.amazon.com/AWSSDKforPHP/latest/#m=AmazonEC2/request_spot_instances
 */

class Ebs
{
    private $m_snapshot_id;
    private $m_volume_size;
    private $m_delete_on_termination;
    private $m_volume_type;
    private $m_iops;
    
    
    /**
     * Constructor for an Ebs Volume
     * @param type $snapshot_id - The ID of the snapshot from which the volume will be created.
     * @param int $volume_size - The size of the volume, in gigabytes.
     * @param VolumeType $type - the type of volume (e.g. standard or io optimized)
     * @param bool $delete_on_termination
     * @param int $iops - the number of iops the device should handle
     */
    public function __construct($snapshot_id, $volume_size, VolumeType $type, $delete_on_termination, $iops)
    {
         $this->m_snapshot_id = $snapshot_id;
         
         if (is_int($volume_size))
         {
             $this->m_volume_size = $volume_size;
         }
         else
         {
             throw new \Exception('volume size needs to be an integer');
         }
         
         if (is_bool($delete_on_termination))
         {
            $this->m_delete_on_termination = $delete_on_termination;
         }
         else
         {
             throw new \Exception('deleteOnTermination needs to be a boolean');
         }
         
    }
                    
    public function to_array()
    {
        return array(
            'SnapshotId'          => $this->m_snapshot_id,
            'VolumeSize'          => $this->m_volume_size,
            'DeleteOnTermination' => $this->m_delete_on_termination,
            'VolumeType'          => $this->m_volume_type,
            'Iops'                => $this->m_iops
        );
    }
}