<?php
class DatabaseConnection {
    private $dbHost;
    private $dbUser;
    private $dbPass;
    private $dbName;
    public $db;

    /**
     * Constructor of DatabaseConnection
     * this function will generate mysql DB connection
     */
    function __construct() {
        // Process the config file and dump the variables into $config
        $this->dbHost = 'localhost';
        $this->dbName = 'dotstudio_version1';
        $this->dbUser = 'root';
        $this->dbPass = '';

        try {
            $this->db = new PDO('mysql:host='.$this->dbHost.';dbname='.$this->dbName.';charset=utf8', $this->dbUser, $this->dbPass);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        }
        catch(PDOException $e) {
            echo $e->getMessage();
        }
    }
    function fetch_query($qry)
    {
        $stmt = $this->db->query($qry);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>