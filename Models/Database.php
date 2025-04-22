<?php

namespace Models;

class Database
{
    protected static $_dbInstance = null;
    protected $_dbHandle;

    // Static method to get the database instance (Singleton access point)
    public static function getInstance() {
        // Check if instance doesn't exist yet
        if (self::$_dbInstance === null) {
            // Load database configuration from external file
            $config = require 'config.php';

            // Create new instance with configuration values this call the constructor
            self::$_dbInstance = new self(
                $config['database']['username'],
                $config['database']['password'],
                $config['database']['host'],
                $config['database']['dbname']
            );
        }
        // Return the existing or newly created instance
        return self::$_dbInstance;
    }

    // Private constructor (can only be called from within the class)
    private function __construct($username, $password, $host, $database) {
        try {
            // Create PDO connection with provided credentials
            $this->_dbHandle = new PDO("mysql:host=$host;dbname=$database", $username, $password);
            echo "Connected";
        }
        catch (PDOException $e) {
            // Handle connection errors
            echo $e->getMessage(); // Output error message (in production, log this instead)
        }
    }
    // 7. Method to get the database connection handle
    public function getdbConnection() {
        return $this->_dbHandle; // Returns the PDO object for database operations
    }

    // 8. Destructor - automatically called when object is destroyed
    public function __destruct() {
        // 9. Clean up by closing the database connection
        $this->_dbHandle = null;
    }
}
