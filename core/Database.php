<?php 

/**
 * Database
 *
 * The Database class creates a new mysqli object that can be used for 
 * database connections.
 */
final class Database
{
    /**
     * Database connection.
     * @var object
     */
    public $mysqli;

    /**
     * Store the single instance of Database.
     * @var object
     */
    private static $instance;

    /**
     * Class Construct
     * 
     * Private constructor to limit object instantiation to within the class.
     */
    private function __construct() 
    {
        if (!$this->mysqli) {
            $this->mysqli = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE);
            if ($this->mysqli->connect_error) {
                exit('Failed to connect to the database <br>Error: ' . $this->mysqli->connect_error);
            }
        }

    }

    /**
     * Get DB Instance
     * 
     * Getter method for creating/returning the single instance of this class.
     * 
     * @return object
     */
    public static function getInstance() 
    {
        if (!isset(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * Generic database query.
     * 
     * @param string $query
     * @return mixed
     */
    public function query($query)
    {
        $result = mysqli_query($this->mysqli, $query);
        if (mysqli_errno($this->mysqli)) {
            echo 'PHP Could not run the query: (' . $query . ') <br>Reason: ' . mysqli_error($this->mysqli);
        }
       return $result;
    }
}