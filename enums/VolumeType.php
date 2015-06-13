<?php

namespace iRAP\AwsWrapper\Enums;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class VolumeType
{
    const STANDARD = 'standard';
    const IO1 = 'io1';
    
    private $m_type;
    
    private static $s_allowed_types = array(
        self::STANDARD => 1,
        self::IO1 => 1
    );
    
    public function __construct($type)
    {
        if (!isset(self::$s_allowed_types[$type]))
        {
            throw new \Exception('Unrecognized volume type: ' . $type);
        }
        
        $this->m_type = $type;
    }
    
    
    public function __toString()
    {
        return $this->m_type;
    }
}