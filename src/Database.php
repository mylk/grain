<?php

namespace Grain;

class Database
{
    private $config;
    private $connection;
    
    public function __construct($config)
    {
        $this->config = $config;
    }
    
    public function __destruct()
    {
        $this->disconnect();
    }
    
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
            die($ex->getMessage());
        }
    }
    
    public function disconnect()
    {
        // close the PDO connection
        $this->connection = null;
    }
    
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
            die($ex->getMessage());
        }
    }
    
    public function find($query, $parameters)
    {
        return $this->execute($query, $parameters);
    }
    
    public function findOne($query, $parameters)
    {
        return $this->execute($query, $parameters, $one = true);
    }
}
