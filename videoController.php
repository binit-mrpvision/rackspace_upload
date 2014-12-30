<?php
require_once 'library/video_model.php';
require 'vendor/autoload.php';
use OpenCloud\Rackspace;

/**
 * Class is been develop for video related functionality
 */
class videoController{
    
    private $objVideo;
    private $rackUsername='dotstudiopro';
    private $rackAPIkey='62d1c91260cc8da35ad26340906bc4bf';
    private $clientID_V1=23;
    private $clientID_V2='54987ed197f8156178fdbc2d';
    
    /** Construct the exception */
    
    function __construct() {
        $this->objVideo = new video_model();
    }
    
    public function ShiftAction(){
        $returnData = array();
        try {

           /** Step1: 
            *  Get list of video exits for client 
            */
           $returnData = $this->objVideo->fetch_all_records();
           
           /** Step2: 
            *  Generate their version2 Id.
            *  Call API https://api.dotstudiopro.com/v2/json/videos/migrate
            */

           /** Step3: 
            *  Shift all videos file to rack server
            *  Rackserver Connectivity
            */
           $this->shift_video($returnData);
           
           
        } catch(PDOException $ex) {
           //handle me.
        }
    }
    
    /**
     * Shift all videos file to rack server
     * @param $config
     */
    public function shift_video($videoDetail)
    {
        /** Step3.1 : Shift master file and rename */
        $videoDetail['iphone_path'] = 'http://media.dotstudiopro.com/vod/284/284.m3u8';
        $ArrVideoPath = explode('media.dotstudiopro.com', $videoDetail['iphone_path']);   // get directory path
        $master_m3u8_path = '/public_html'.$ArrVideoPath[1];     
        $this->upload_to_rackServer($master_m3u8_path,$destinationFile);
        
        /** Step3.2 : Shift rest of the file */
        
    }
    public function upload_to_rackServer($sourceFile,$destinationFile)
    {
        
    
        // Instantiate a Rackspace client.
        $client = new Rackspace(Rackspace::US_IDENTITY_ENDPOINT, array(
            'username' => $this->rackUsername,
            'apiKey'   => $this->rackAPIkey
        ));

        // Obtain an Object Store service object from the client.
        $objectStoreService = $client->objectStoreService(null, 'DFW');

        // Create a container for your objects (also referred to as files).
        //$container = $objectStoreService->createContainer('mrpvision_folder');
        $container = $objectStoreService->getContainer('mrpvision_folder');

        $container->enableCdn();

        // Upload an object to the container.
        $localFileName  = $sourceFile; 
        $remoteFileName = 'splash.mp4';

        $handle = fopen($localFileName, 'r');
        $object = $container->uploadObject($remoteFileName, $handle);
    }
}

$videoClassObj = new videoController();
$videoClassObj->ShiftAction();

