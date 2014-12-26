<?php

require 'vendor/autoload.php';

use OpenCloud\Rackspace;

// Instantiate a Rackspace client.
$client = new Rackspace(Rackspace::US_IDENTITY_ENDPOINT, array(
    'username' => 'dotstudiopro',
    'apiKey'   => '62d1c91260cc8da35ad26340906bc4bf'
));

// Obtain an Object Store service object from the client.
$objectStoreService = $client->objectStoreService(null, 'DFW');

// Create a container for your objects (also referred to as files).
//$container = $objectStoreService->createContainer('mrpvision_folder');
$container = $objectStoreService->getContainer('mrpvision_folder');

$container->enableCdn();

// Upload an object to the container.
$localFileName  = __DIR__ . '/splash.mp4';
$remoteFileName = 'splash.mp4';

$handle = fopen($localFileName, 'r');
$object = $container->uploadObject($remoteFileName, $handle);