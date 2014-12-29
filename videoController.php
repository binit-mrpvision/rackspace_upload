<?php
require_once 'library/video_model.php';

/**
 * Class is been develop for video related functionality
 */
class videoController{
    
    private $objVideo;
    
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

           /** Step3.1 : Shift master file and rename */

           /** Step3.2 : Shift rest of the file */
           
        } catch(PDOException $ex) {
           //handle me.
        }
    }
}

$videoClassObj = new videoController();
$videoClassObj->ShiftAction();

