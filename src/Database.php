<?php

namespace Grain;

class Database
{
    private $config;
    private $connection;

    /**
     * Stores the database configuration in a private variable.
     * 
     * @param array $config
     * 
     * @return void
     */
    public function __construct($config)
    {
        $this->config = $config;
    }
    
    /**
     * Invokes the closing of the connection to the database.
     * 
     * @return void
     */
    public function __destruct()
    {
        $this->disconnect();
    }
    
    /**
     * Does the actual connection to the database.
     * 
     * @return \Grain\Database | null
     */
    public function connect()
    {
        try {
            $this->connection = new \PDO(
                "mysql:host={$this->config["hostname"]};dbname={$this->config["database"]}",
                $this->config["username"],
                $this->config["password"]
            );
            $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            
            return $this;
        } catch (\PDOException $ex) {
            return null;
        }
    }
    
    /**
     * Closes the connection to the database.
     * 
     * @return void
     */
    public function disconnect()
    {
        $this->connection = null;
    }
    
    /**
     * Executes queries to the database
     * 
     * @param string $query The actual SQL query
     * @param array $parameters The query parameters
     * @param boolean $one Indicates that query returns one or more results
     * 
     * @return array | object | null
     */
    public function execute($query, $parameters, $one = false)
    {
        try {
            $stmt = $this->connection->prepare($query);
            
            foreach ($parameters as $parameterKey => $parameterValue) {
                $stmt->bindParam($parameterKey, $parameterValue);
            }
            
            $stmt->setFetchMode(\PDO::FETCH_OBJ);
            $stmt->execute();
            
            if ($one) {
                $result = $stmt->fetch();
            } else {
                $result = $stmt->fetchAll();
            }
            
            return $result;
        } catch (\PDOException $ex) {
            return null;
        }
    }
    
    /**
     * Invokes the execute() to return multiple results.
     * 
     * @param string $query The actual SQL query
     * @param array $parameters The query parameters
     * 
     * @return array | null
     */
    public function find($query, $parameters = array())
    {
        return $this->execute($query, $parameters);
    }
    
    /**
     * Invokes the execute() to return a single result.
     * 
     * @param string $query The actual SQL query
     * @param array $parameters The query parameters
     * 
     * @return object | null
     */
    public function findOne($query, $parameters = array())
    {
        return $this->execute($query, $parameters, $one = true);
    }
}
