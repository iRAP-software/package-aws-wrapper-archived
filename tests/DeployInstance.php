<?php

require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/Settings.php');

function main()
{
    $apiKey = API_KEY;
    $secret = API_SECRET;
    $region = \iRAP\AwsWrapper\Enums\AwsRegion::create_EU_W1();
        
    $awsWrapper = new iRAP\AwsWrapper\AwsWrapper($apiKey, $secret, $region);
    $ec2Client = $awsWrapper->getEc2Client();
    
    $ec2Client->runInstances($request);
    
}

main();