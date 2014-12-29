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
}
?>