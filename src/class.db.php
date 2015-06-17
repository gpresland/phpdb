<?php
/**
 * PHPDB
 *
 * Provides a wrapper for the PHP PDO class to help make MySQL
 * connections and transactions simpler. Currently only accepts
 * MySQL type databases.
 *
 * @author      Greg Presland
 * @date        17 June 2015
 * @version     0.2.0
 */

class DB
{
    /**
     * Holds if the last query was successful or not
     * @type bool
     */
    public $executed = false;
    
    /**
     * If we are connected to the database
     * @type bool
     */
    private $isConnected = false;

    /**
     * Last insert ID
     * @type int
     */
    public $lastInsertId = null;

    /**
     * Holds the number of rows affected from the last query
     * @type int
     */
    public $rowCount = 0;

    /**
     * Database Handler
     * @type object
     */
    private $_dbh;

    /**
     * Connects to server and database
     * @param string $db_host MySQL hostname
     * @param string $db_name MySQL database name
     * @param string $db_username MySQL username
     * @param string $sb_password MySQL password
     * @return true if successful, otherwise the error message
    */
    public function __construct($db_host, $db_name, $db_username, $db_password)
    {
        if (empty($db_host) || empty($db_name) || empty($db_username) || empty($db_password)) {
            return;
        }

        // Connect to database and return result
        return $this->_connect($db_host, $db_name, $db_username, $db_password);
    }

    /**
     * Executes a query
     * @param string $query the query to run
     * @param mixed $binds any binds to the query
     * @return an index and associated array of the results
     * @return mixed if results, otherwise void
     */
    public function execute($query, $binds = null)
    {
        if ($this->isConnected === false) {
            return;
        }

        if ($stmt = $this->_dbh->prepare($query)) {

            // Bind values to the query
            if (!empty($binds)) {
                foreach ($binds as &$bind) {
                    // Get bind type
                    if (array_key_exists('type', $bind) === false) {
                        $bind['type'] = $this->_getpdotype($bind['value']);
                    }
                    // Bind value to SQL statement
                    $stmt->bindValue(":{$bind['bind']}", $bind['value'], $bind['type']);
                }
                unset($bind);
            }

            // Execute the query, storing if it was successful or not
            $this->executed = $stmt->execute();
                
            // Update last insert ID
            $this->lastInsertId = $this->_dbh->lastInsertId();

            // Update total rows affected
            $this->rowCount = $stmt->rowCount();

            // If there's data to be returned
            if ($stmt->rowCount()) {

                // Return SQL results as an associative array
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }
    }

    /**
     * Connect to the MySQL server using PDO
     * @param string $db_host MySQL hostname
     * @param string $db_name MySQL database name
     * @param string $db_username MySQL username
     * @param string $sb_password MySQL password
     * @return true if successful, otherwise the error message
     */
    private function _connect($db_host, $db_name, $db_username, $db_password)
    {
        try { 
            $this->_dbh = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_username, $db_password);
            $this->isConnected = true;
        }
        catch(PDOException $e) {
            return $e->getMessage();
        }

        return true;
    }

    /**
     * Gets the PDO equivalent of a primitive
     * @param mixed $value the value to be inserted into the database
     * @return PDO::PARAM_* type equivalent of primitive
     */
    public function _getpdotype($value)
    {
        switch (gettype($value)) {
            case 'boolean': return PDO::PARAM_BOOL;
            case 'integer': return PDO::PARAM_INT;
            case 'double': return PDO::PARAM_STR;
            case 'string': return PDO::PARAM_STR;
            case 'object': return PDO::PARAM_LOB;
            case 'resource': return PDO::PARAM_LOB;
            case 'NULL': return PDO::PARAM_NULL;
        }
    }
}