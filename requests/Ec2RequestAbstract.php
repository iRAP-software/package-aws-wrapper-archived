<?php

namespace iRAP\AwsWrapper\Requests;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

abstract class Ec2RequestAbstract
{
    private $m_return_curl_handle = null;
    private $m_curl_opts = array();
    
    protected abstract function getOptionsArray();
    
    /**
     * 
     * @param Array $curlOpts - A set of values to pass directly into curl_setopt(), where the key 
     *                          is a pre-defined CURLOPT_* constant.
     */
    public function setCurlOpts(Array $curlOpts)
    {
        $this->m_curl_opts = $curlOpts;
    }
    
    
    /**
     * This is the call that actually runs the request. It can should only be called from this 
     * abstract object in the send function as this needs to add its own properties.
     */
    protected abstract function sendRequest(\Aws\Ec2\Ec2Client $ec2, Array $options);


    /**
     * Set the curl handle to true in order to return the cURL handle when sending the request 
     * rather than actually completing the request. This toggle is useful for manually managed 
     * batch requests
     * @param bool $return_curl_handle - (optional) flag of whether to set to true, you would only
     *                                  need to set this to false if you setReturnCurlHandle to 
     *                                  true earlier as it is defaulted to false. 
     * @return void
     */
    public function setReturnCurlHandle($return_curl_handle=true)
    {
        $this->m_return_curl_handle=true;
    }
    
    
    /**
     * This is the public function which builds the array form that goes into the sending of the
     * request. 
     * @return CFResponse
     */
    public final function send(\Aws\Ec2\Ec2Client $ec2Client)
    {        
        $opts = static::getOptionsArray();
        
        if (count($this->m_curl_opts) > 0)
        {
            $opts['curlopts'] = $this->m_curl_opts;
        }
        
        # Pretty sure this is a 'toggle' so if "false" we need to leave it out otherwise it will 
        # switch on (e.g. be considered true)
        if ($this->m_return_curl_handle)
        {
            $opts['returnCurlHandle'] = $this->returnCurlHandle;
        }
        
        // Get the response from a call to the DescribeImages operation.
        
        $response = static::sendRequest($ec2Client, $opts);
        
        $debugMsg = "response for " . get_called_class() . ' request: ' . PHP_EOL .
                    print_r($response, true);
        
        \iRAP\CoreLibs\Core::debugPrintln($debugMsg);
        
        return $response;
    }
}

