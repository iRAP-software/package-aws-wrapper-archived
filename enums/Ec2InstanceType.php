<?php

namespace iRAP\AwsWrapper\Enums;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Ec2InstanceType
{
    private $m_instanceType;
    
    
    /**
     * Create a micro instance. This is the only instance whereby the amount of compute capability
     * is not guaranteed, and is also the only instance that is available for free/trial.
     * @param void
     * @return Ec2InstanceType
     */
    public static function createT1()
    {
        $ec2InstanceType = new Ec2InstanceType('t1.micro');
        return $ec2InstanceType;
    }
    
    
    /**
     * Create a t2 (burstable) intance
     * @param int $size - int between 1 and 4 with 1 being the smallest size for t2.micro.
     * @return Ec2InstanceType
     */
    public static function createT2($size)
    {
        $size = \iRAP\CoreLibs\Core::clampValue($size, 4, 1);
        
        $sizeMap = array(
            1 => 't2.micro',
            2 => 't2.small',
            3 => 't2.medium',
            4 => 't2.large'
        );
        
        $ec2InstanceType = $sizeMap[$size];
        return new Ec2InstanceType($ec2InstanceType);
    }
    
    
    
    /**
     * Create one of the new general purpose Instances.
     * @param int $size - integer between 1 - 2 representing the size of the instance
     *  1 - 4 vcpu 13 ECU, 15 GiB Ram, 2 x 40 SSD, $0.495 per Hour
     *  2 - 8 vcpu 26 ECU 30 GiB Ram, 2 x 80 SSD $0.990 per hour.
     * @return Ec2InstanceType
     * @throws Exception if size is not within range.
     */
    public static function createGeneralPurposeNew($size)
    {
        switch ($size)
        {        
            case 1:
            {
                $ec2InstanceType = new Ec2InstanceType('m3.xlarge');
            }
            break;
        
            case 2:
            {
                $ec2InstanceType = new Ec2InstanceType('m3.2xlarge');
            }
            break;
            
            default:
            {
                $err_msg = 'createGeneralPurpose - Unrecognized size: ' . $size . '. Please ' .
                           'provide a value between 1 and 5';
                
                throw new \Exception($err_msg);
            }
        }
        
        return $ec2InstanceType;
    }
    
    
    /**
     * Create one of the old general purpose instances.
     * @param int $size - int between 1 and 4 representing the size of instance (1 being smallest)
     *                    1 - m1 small
     *                    2 - m1 medium
     *                    3 - m1 large
     *                    4 - m1 xlarge
     * @throws Exception if size is not an integer between 1 and 4.
     */
    public static function createGeneralPurposeOld($size)
    {
        switch ($size)
        {
            case 1:
            {
                $ec2InstanceType = new Ec2InstanceType('m1.small');
            }
            break;
        
            case 2:
            {
                $ec2InstanceType = new Ec2InstanceType('m1.medium');
            }
            break;
        
            case 3:
            {
                $ec2InstanceType = new Ec2InstanceType('m1.large');
            }
            break;
        
            case 4:
            {
                $ec2InstanceType = new Ec2InstanceType('m1.xlarge');
            }
            break;
        
            default:
            {
                $err_msg = 'createGeneralPurpose - Unrecognized size: ' . $size . '. Please ' .
                           'provide a value between 1 and 4';
                
                throw new \Exception($err_msg);
            }
        }
    }
    
  
    # High Memory
    
    
    /**
     * Create an instance that is targetted towards high amounts of RAM
     * @param int $size - the size of the instance with 1 being the smallest
     *                  1 - 17.1 Gig
     *                  2 - 34.2 Gig
     *                  3 - 68.4 Gig
     *                  4 - 244 Gig
     * 
     * @throws Exception if size was not a valid number.
     */
    public static function createHighMemory($size)
    {
        switch ($size)
        {        
            case 1:
            {
                $ec2InstanceType = new Ec2InstanceType('m2.xlarge');
            }
            break;
            
            case 2:
            {
                $ec2InstanceType = new Ec2InstanceType('m2.2xlarge');
            }
            break;
            
            case 3:
            {
                $ec2InstanceType = new Ec2InstanceType('m2.4xlarge');
            }
            break;
            
            case 4:
            {
                $ec2InstanceType = new Ec2InstanceType('cr1.8xlarge');
            }
            break;
            
            default:
            {
                $err_msg = 'createHighMemory - Unrecognized size: ' . $size . '. Please ' .
                           'provide a value between 1 and 4';
                
                throw new \Exception($err_msg);
            }
        }
    }
    
    
    /**
     * Create one of the new Compute optimized instances. (e.g. c3 family)
     * @param int $size - integer between 1-5 to represent the size of the instance with 1 being
     *                    the smallest, and 4 being the largest and most expensive.
     *                    Each step in size doubles the compute capability and price.
     * @return \Ec2InstanceType
     * @throws Exception if $size provided was not an allowed value.
     */
    public static function createNewHighCpu($size)
    {
        switch ($size)
        {
            case 1:
            {
                $ec2InstanceType = new Ec2InstanceType('c4.large');
            }
            break;
            
            case 2:
            {
                $ec2InstanceType = new Ec2InstanceType('c4.xlarge');
            }
            break;
            
            case 3:
            {
                $ec2InstanceType = new Ec2InstanceType('c4.2xlarge');
            }
            break;
            
            case 4:
            {
                $ec2InstanceType = new Ec2InstanceType('c4.4xlarge');
            }
            break;
            
            case 5:
            {
                $ec2InstanceType = new Ec2InstanceType('c4.8xlarge');
            }
            break;
            
            default:
            {
                $err_msg = 'Unrecognized size: ' . $size . '. Please provide a value between ' .
                           '1 and 5';
                
                throw new \Exception($err_msg);
            }
        }
        
        return $ec2InstanceType;
    }
    
    
    /**
     * Create a previous generation compute optimized instance
     * @param int $size - int between 1 and 3 where 1 represents the smallest instance.
     * @return Ec2InstanceType
     * @throws Exception if $size is not an acceptable number
     */
    public static function createOldHighCpu($size)
    {
        switch ($size)
        {
            case 1:
            {
                $ec2InstanceType = new Ec2InstanceType('c3.large');
            }
            break;
            
            case 2:
            {
                $ec2InstanceType = new Ec2InstanceType('c3.xlarge');
            }
            break;
            
            case 3:
            {
                $ec2InstanceType = new Ec2InstanceType('c3.2xlarge');
            }
            break;
            
            case 4:
            {
                $ec2InstanceType = new Ec2InstanceType('c3.4xlarge');
            }
            break;
            
            case 5:
            {
                $ec2InstanceType = new Ec2InstanceType('c3.8xlarge');
            }
            break;
            
            default:
            {
                $err_msg = 'Unrecognized size: ' . $size . '. Please provide a value between ' .
                           '1 and 5';
                
                throw new \Exception($err_msg);
            }
        }
        
        return $ec2InstanceType;
    }
    
    
    /**
     * Create a high IO storage optimized instance (SSD local storage)
     * @param int $size - int between 1 and 4
     */
    public static function createHighIo($size)
    {
        switch ($size)
        {
            case 1:
            {
                $ec2InstanceType = new Ec2InstanceType('i2.xlarge');
            }
            break;
            
            case 2:
            {
                $ec2InstanceType = new Ec2InstanceType('i2.2xlarge');
            }
            break;
            
            case 3:
            {
                $ec2InstanceType = new Ec2InstanceType('i2.4xlarge');
            }
            break;
            
            case 4:
            {
                $ec2InstanceType = new Ec2InstanceType('i2.8xlarge');
            }
            break;
        
            default:
            {
                $err_msg = 'Unrecognized size: ' . $size . '. Please provide a value between ' .
                           '1 and 4';
                
                throw new \Exception($err_msg);
            }
        }
        
        return $ec2InstanceType;
    }
    
    
    /**
     * Create the storage instance which just has a huge amount of local storage. 24 x 2048Gig
     * drives.
     */
    public static function createStorage()
    {
        $ec2InstanceType = new Ec2InstanceType('hs1.8xlarge');
        return $ec2InstanceType;
    }
    
    
    
    /**
     * Allows the user to create one of this object from passing a string which is validated.
     * @param String $size - the size of the instance in Amazon string form.
     * @return Ec2InstanceType
     * @throws Exception if an unrecognized size/type is given.
     */
    public static function createFromString($size)
    {
        $allowedTypes = array(
            't1.micro',
            
            # burstable
            't2.micro',
            't2.small',
            't2.medium',
            't2.large',
            
            'm1.small',
            'm1.medium',
            'm1.large',
            'm1.xlarge',
            
            // High Memory
            'm2.xlarge',
            'm2.2xlarge',
            'm2.4xlarge',
            'm3.xlarge',
            'm3.4xlarge',
            
            // High CPU
            'c1.medium',
            'c1.xlarge',
            
            // Cluster
            'cc1.4xlarge',
            'cc2.8xlarge',
            'cg1.4xlarge',
            
            // High I/O
            'hi1.4xlarge',
            
            'hs1.8xlarge', // High Storage
            
            # New compute instances
            'c3.large',
            'c3.xlarge',
            'c3.2xlarge',
            'c3.4xlarge',
            'c3.8xlarge',
            
            # Latest compute instances
            'c4.large',
            'c4.xlarge',
            'c4.2xlarge',
            'c4.4xlarge',
            'c4.8xlarge'
        );
        
        if (!in_array($size, $allowedTypes))
        {
            throw new \Exception('Invalid instance size: [' . $size . ']');
        }
        
        return new Ec2InstanceType($size);
    }
    
    
    private function __construct($instanceType)
    {
        $this->m_instanceType = $instanceType;
    }
    
    
    /**
     * Define the toString method so we can place this object directly in calls without using a 
     * method.
     * @return String - the instanceType.
     */
    public function __toString() 
    {
        return $this->m_instanceType;
    }
}
