<?php
require_once 'library/video_model.php';

/**
 * Class is been develop for video related functionality
 */
class videoController{
    
    private $objVideo;
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
            *  $returnData = $this->objVideo->fetch_all_records();
            *  echo "<pre>";print_r($returnData);
            */       
           $returnData = $this->objVideo->fetch_all_records_id();
           echo "<pre>";print_r($returnData);
           
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
                echo json_encode($post_variables);
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

           /** Step3.1 : Shift master file and rename */

           /** Step3.2 : Shift rest of the file */
           
        } catch(PDOException $ex) {
           //handle me.
        }
    }
}

$videoClassObj = new videoController();
$videoClassObj->ShiftAction();

