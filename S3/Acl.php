<?php

/*
 * ACL stands for Access Control List.
 * Amazon S3 Access Control Lists (ACLs) enable you to manage access to buckets and objects. 
 * Each bucket and object has an ACL attached to it as a subresource. It defines which AWS accounts 
 * or groups are granted access and the type of access. When a request is received against a 
 * resource, Amazon S3 checks the corresponding ACL to verify the requester has the necessary 
 * access permissions.
 * 
 * https://docs.aws.amazon.com/AmazonS3/latest/dev/acl-overview.html
 */

namespace iRAP\AwsWrapper\S3;


class Acl
{
    private $m_string;
    
    /**
     * Please use one of the public "create" methods to create one of these objects.
     */
    private function __construct($string)
    {
        $this->m_string = $string;
    }
    
    
    /**
     * Create an ACL that provides no public access.
     * @return Acl
     */
    public static function createPrivate()
    {
        return new Acl('private');
    }
    
    
    /**
     * Create an ACL that provides public access to read the data, but not to change it.
     * @return Acl
     */
    public static function createPublicRead()
    {
        return new Acl('public-read');
    }
    
    
    /**
     * Create an ACL that provides completely public read/write access. Anybody can do anything
     * with your data.
     * @return Acl
     */
    public static function createPublicReadWrite()
    {
        return new Acl('public-read-write');
    }
    
    
    /**
     * Create an ACL that provides read access to authenticated users.
     * @return Acl
     */
    public static function createAuthenticatedRead()
    {
        return new Acl('authenticated-read');
    }
    
    
    /**
     * Convert this object to a string.
     * @return String
     */
    public function __toString() { return $this->m_string; }
    
}
