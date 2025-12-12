<?php
/**
 * Database Connection using PDO (Singleton Pattern)
 * File: /config/database.php
 */

class Database {
    private static $instance = null;
    private $connection;
    
    /**
     * Private constructor để implement Singleton
     */
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
            
        } catch (PDOException $e) {
            // Log error
            error_log('Database Connection Error: ' . $e->getMessage());
            
            // Show user-friendly error
            if (ENVIRONMENT === 'development') {
                die('Lỗi kết nối database: ' . $e->getMessage());
            } else {
                die('Không thể kết nối đến cơ sở dữ liệu. Vui lòng thử lại sau.');
            }
        }
    }
    
    /**
     * Get Database instance (Singleton)
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Get PDO connection
     */
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Prevent cloning
     */
    private function __clone() {}
    
    /**
     * Prevent unserializing
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
    
    /**
     * Close connection
     */
    public function closeConnection() {
        $this->connection = null;
    }
    
    /**
     * Test connection
     */
    public function testConnection() {
        try {
            $stmt = $this->connection->query('SELECT 1');
            return true;
        } catch (PDOException $e) {
            error_log('Test Connection Error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get database info
     */
    public function getDatabaseInfo() {
        try {
            $stmt = $this->connection->query('SELECT DATABASE() as dbname, VERSION() as version');
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('Get Database Info Error: ' . $e->getMessage());
            return null;
        }
    }
}