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

    private $public_path;

    
    /** Construct the exception */
    
    function __construct() {
        $this->objVideo = new video_model();
        $this->public_path = dirname(__FILE__).'/log_files/';
    }
    
    public function ShiftAction(){
        $returnData = array();
        try {

           /** Step1: 
            *  Get list of video exits for client 
            *$returnData = $this->objVideo->fetch_all_records();
            *  $returnData = $this->objVideo->fetch_all_records();
            *  echo "<pre>";print_r($returnData);
            */       
           $returnData = $this->objVideo->fetch_all_records_id();
           //echo "<pre>";print_r($returnData);

           
           /** Step2: 
            *  Generate their version2 Id.
            *  Call API https://api.dotstudiopro.com/v2/json/videos/migrate
            */

           $url_api='https://api.dotstudiopro.com/v2/json/videos/migrate';
           foreach($returnData as $values){
                $post_variables = array(
                    "title"                 =>$values['title'],
                    "company"               =>"54987ed197f8156178fdbc2d",
                    "migrate_company_id"    =>$values['company_id'],
                    "migrate_video_id"      =>$values['id'],
                    "migrate_source_file"   =>$values['iphone_path']
                );
                //echo json_encode($post_variables);
                /*Create Webservice Call log file.*/
                $file = fopen($this->public_path . "api_log_file.txt", "a+");
                fwrite($file, '\r\nMethod : ' . "POST" . "\r\n");
                fwrite($file, 'Request Parameter : ' . json_encode($post_variables) . "\r\n");
                fwrite($file, 'date : ' . date('Y-m-d H:i:s') . "\r\n");
                fclose($file);
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url_api);
                // tell curl you want to post something
                curl_setopt($ch, CURLOPT_POST, true);
                // define what you want to post
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_variables));
                // return the output in string format
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result_curl = curl_exec($ch);
                if(!empty($result_curl) || $result_curl != null || $result_curl != FALSE || $result_curl != ''){
                    /*Create Webservice Response log file.*/
                    $file = fopen($this->public_path . "api_log_file.txt", "a+");
                    fwrite($file, '\r\nMethod : ' . "POST" . "\r\n");
                    fwrite($file, 'Response of api : ' . ($result_curl) . "\r\n");
                    fwrite($file, 'date : ' . date('Y-m-d H:i:s') . "\r\n");
                    fclose($file);
                    
                    /**
                     * Update the version 1 video company id with the version 2 company id.
                     */
                    $response_data = json_decode($result_curl);
                    $response_data = (array)$response_data;
                    echo "Response_api<pre>".print_r($response_data);
                    $version2_company_id = $response_data['_id'];
                    $this->objVideo->update_data($values['id'],$version2_company_id);
                    
                    /**
                     * Generate one complete array which will transfer file from version 1 to version 2
                     * based on the api response.
                     */
                    $post_variables = json_decode($post_variables);
                    $post_variables = (array)$post_variables;
                    $file_transfer_details = array_merge($response_data,$post_variables);
                    echo "Step 3<pre>".print_r($file_transfer_details);
                    echo $result_curl;
                }
           }
           
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

