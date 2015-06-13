<?php

namespace iRAP\AwsWrapper\Objects;

/* 
 * A filter for requests such as describe regions in order to reduce the nubmer of things that are
 * returned. 
 */

class AmazonFilter
{
    private $m_parts = array();
    
    /**
     * Create a filter for requests such as describe regions. Note that if you want to filter by
     * multiple names, please use the add() function with the additional names.
     * @param String $name - Specifies the name of the filter.
     * @param mixed $value - string or array of strings for allowed values for the name.
     */
    public function __construct($name, $value)
    {
        $this->m_parts[$name] = $value;
    }
    
    
    /**
     * Adds to the filter. Note that if you have already provided an item with the same name, this
     * will REPLACE that. If you want multiple values for the same name, provide value as an array.
     * @param String $name - Specifies the name of the filter.
     * @param mixed $value - string or array of strings for allowed values for the name.
     */
    public function add($name, $value)
    {
        $this->m_parts[$name] = $value;
    }
    
    
    public function to_array()
    {
        $arrayForm = array();
        
        foreach ($this->m_parts as $name => $values)
        {
            $arrayForm[] = array($name, $values);
        }
        
        return $arrayForm;
    }
}
