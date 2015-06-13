<?php

namespace iRAP\AwsWrapper\Enums;

/* 
 * This is an 'enum' representing the regions in amazon for ec2. (do not know if there are regions
 * for other systems that are not available for ec2?)
 */

class AmazonRegion
{
    private $m_region = null;
    
    
    # Define the constants for easily specifying regions.
    public static function create_US_E1()
    {
        $region = new AmazonRegion('ec2.us-east-1.amazonaws.com');
        return $region;
    }
    
	public static function create_virginia()
    {
        return self::create_US_E1();
    }
    
	public static function create_US_W1()
    {
        $region = new AmazonRegion('ec2.us-west-1.amazonaws.com');
        return $region;
    }
    
	public static function create_california()
    {
        return self::create_US_W1();
    }
    
	public static function create_US_W2()
    {
        $region = new AmazonRegion('ec2.us-west-2.amazonaws.com');
        return $region;
    }
    
	public static function create_oregon()
    {
        return self::create_US_W2();
    }
    
	public static function create_EU_W1()
    {
        $region = new AmazonRegion('ec2.eu-west-1.amazonaws.com');
        return $region;
    }
    
	public static function create_ireland()
    {
        return self::create_EU_W1();
        return $region;
    }
    
	public static function create_APAC_SE1()
    {
        $region = new AmazonRegion('ec2.ap-southeast-1.amazonaws.com');
        return $region;
    }
    
	public static function create_singapore()
    {
        return self::create_APAC_SE1();
    }
    
    public static function create_APAC_SE2()
    {
        $region = new AmazonRegion('ec2.ap-southeast-2.amazonaws.com');
        return $region;
    }
    
    public static function create_sydney()
    {        
        return self::create_APAC_SE2();
    }
    
	public static function create_APAC_NE1()
    {
        $region = new AmazonRegion('ec2.ap-northeast-1.amazonaws.com');
        return $region;
    }
    
	public static function create_tokyo()
    {
        return self::create_APAC_NE1();
    }
    
	public static function create_US_GOV1()
    {
        $region = new AmazonRegion('ec2.us-gov-west-1.amazonaws.com');
        return $region;
    }
    
	public static function create_SA_E1()
    {
        $region = new AmazonRegion('ec2.sa-east-1.amazonaws.com');
        return $region;
    }
    
	public static function create_sao_paulo()
    {
        return self::create_SA_E1();
    }
    
    private function __construct($region)
    {
        $this->m_region = $region;
    }
    
    
    public function __toString() 
    {
        return $this->m_region;
    }
}
