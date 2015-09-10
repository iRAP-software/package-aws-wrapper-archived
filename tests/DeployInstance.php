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
    
    $ubuntuImage = 'ami-47a23a30';
    $launchSpecification = new \iRAP\AwsWrapper\Objects\LaunchSpecification(\iRAP\AwsWrapper\Enums\Ec2InstanceType::createT2(1), $ubuntuImage);
    $request = new iRAP\AwsWrapper\Requests\RequestRunInstances($launchSpecification, 1, 1);
    $response = $ec2Client->runInstances($request);
    var_dump($response);
}

main();