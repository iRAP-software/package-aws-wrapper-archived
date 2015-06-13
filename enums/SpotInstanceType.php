<?php

namespace iRAP\AwsWrapper\Enums;

/* 
 * This is a type specifier for when makeing a spot instance request. The two min types are 
 * one-time requests and "persistent"
 */

class SpotInstanceType
{
    private $m_type;
   
    private function __construct($type)
    {
        $this->m_type = $type;
    }
    
    public static function create_persistent()
    {
        $spotType = new SpotInstanceType("persistent");
        return $spotType;
    }
    
    public static function create_one_time()
    {
        $spotType = new SpotInstanceType("one-time");
        return $spotType;
    }
    
    
    /**
     * Allow this object to be used in the api calls directly by defining the tostring magic method.
     */
    public function __toString()
    {
        return $this->m_type;
    }
    
}

