<?php

/*
 *
 */

namespace iRAP\AwsWrapper\S3;

class StorageClass 
{
    private function __construct($string) { $this->m_string = $string; }
    
    /**
     * Store your objects with a redundancy level. 
     * 
     * In order to reduce storage costs, you can use reduced redundancy storage for noncritical, 
     * reproducible data at lower levels of redundancy than Amazon S3 provides with standard 
     * storage. The lower level of redundancy results in less durability and availability, but in 
     * many cases, the lower costs can make reduced redundancy storage an acceptable storage 
     * solution. For example, it can be a cost-effective solution for sharing media content that is 
     * durably stored elsewhere. It can also make sense if you are storing thumbnails and other 
     * resized images that can be easily reproduced from an original image.
     * https://docs.aws.amazon.com/AmazonS3/latest/dev/UsingRRS.html
     * ATM reduced redundancy costs 2.4 cents per GB instead of standard's 3 and has an average 
     * loss of 1 in 10,000 objects per year (0.01%).
     * @return \StorageClass
     */
    public static function createReducedRedundancy() { return new StorageClass('REDUCED_REDUNDANCY');}
    
    
    /**
     * Store your objects with the standard (highest on offer) redundancy level. 
     * For more info about the difference between the two types, refer to the 
     * createReducedRedundancy method.
     * @return \StorageClass
     */
    public static function createStandard() { return new StorageClass('STANDARD');}
    
    public function __toString() { return $this->m_string;}
}
