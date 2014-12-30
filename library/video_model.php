<?php

require_once 'db_connection.php';

class video_model extends DatabaseConnection {
    
    public $db;

    /**
     * Constructor of DatabaseConnection
     * @param $config  // Contains credential of DB connection
     */
    function __construct() {
        // Process the config file and dump the variables into $config
        parent::__construct();
    }
    
    /**
     * fetch all records from Video table
     */
    function fetch_all_records()
    {
        $return_array = array();
        try {
            $sql_query ='select `id`,`iphone_path` from videos';
            $return_array = $this->fetch_query($sql_query);
        } catch (Exception $e) {
            var_dump($e->getMessage());
        }
        return $return_array;
    }
    
    /**
     * fetch all records from Video table
     */
    function fetch_record_by_id($id)
    {
        $return_array = array();
        try {
            $sql_query ='select * from videos where id='.$id;
            $return_array = $this->fetch_single_record($sql_query);
        } catch (Exception $e) {
            var_dump($e->getMessage());
        }
        return $return_array;
    }
    
    /**
     * Function to fetch the company id 23 records and file extension .m3u8
     * @return type
     */
    public function fetch_all_records_id(){
        $return_array = array();
        try{
            $sql_query ="select `id`,`iphone_path`,`title`,`company_id` from videos  WHERE `company_id` =23 &&  `iphone_path` LIKE  '%.m3u8%' && `version2_company_id` =''";
            $return_array = $this->fetch_query($sql_query);
            /**
             * Code to fetch files which are present on the server.
             */
            $video_array=array();
            $index_cnt=0;
            foreach($return_array as $value){
                $file_headers = @get_headers($value['iphone_path']);
                if($file_headers[0] == 'HTTP/1.1 200 OK') {
                    $video_array[$index_cnt]['id']=$value['id'];
                    $video_array[$index_cnt]['iphone_path']=$value['iphone_path'];
                    $video_array[$index_cnt]['title']=$value['title'];
                    $video_array[$index_cnt]['company_id']=$value['company_id'];
                    $video_array[$index_cnt]['http_code']=$file_headers[0];
                    return $video_array;
                }
            }
        } catch (Exception $ex) {
            var_dump($e->getMessage());
        }
        return $return_array;
    }
    /**
     * Update the version2 company id when the api returns the response.
     * @param type $id
     * @return boolean|array
     */
    public function update_data($id,$version2_company_id){
        $return_array = array();
        try {
            $sql_query ="update `videos` SET `version2_company_id`=:version2_company_id WHERE ID=:ID";
            $stmt = $this->db->prepare($sql_query);                                  
            $stmt->bindParam(':version2_company_id', $version2_company_id, PDO::PARAM_STR);
            $stmt->bindParam(':ID', $id, PDO::PARAM_STR);
            $stmt->execute();
            return true;
        } catch (Exception $e) {
            var_dump($e->getMessage());
        }
        return $return_array;
    }
}
?>