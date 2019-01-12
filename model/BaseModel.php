<?php
/**
 * Created by PhpStorm.
 * User: Gabriel B.R
 * Email: gabriel.bentara@gmail.com
 * Date: 13/10/18
 * Time: 4:39 PM
 */

require "core/config.php";

class BaseModel
{
    protected $tableName;
    protected $id;
    protected $conn;
    private $saveStmt;
    private $insertStmt;
    private $deleteStmt;

    public function __construct($tableName)
    {
        $this->conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASS);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Set error mode to exceptions
        $this->tableName = $tableName;
        $this->saveStmt = $this->prepareSaveStatement();
        $this->insertStmt = $this->prepareInsertStatement();
        $this->deleteStmt = $this->prepareDeleteStatement();
    }

    public function __call($name, $arguments)
    {
        $this->{$name . "Stmt"}->execute();
        if ($name == "insert") {
            $this->id = $this->conn->lastInsertId();
        }
    }

    public function load()
    {
        $stmt = $this->conn->prepare("select * from `$this->tableName` where id = :id limit 1");
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        foreach ($result as $column => $value) {
            $this->$column = $value;
        }
    }

    private function prepareDeleteStatement()
    {
        $stmt = $this->conn->prepare("delete from `$this->tableName` where id = :id limit 1");
        $stmt->bindParam(":id", $this->id);

        return $stmt;
    }

    private function prepareInsertStatement()
    {
        $stmt = $this->conn->prepare($this->buildInsertQuery());
        foreach ($this->getThisVars() as $name => $value) {
            $stmt->bindParam(":$name", $this->$name);
        }

        return $stmt;
    }

    private function buildInsertQuery() {
        $query = "insert into `$this->tableName` (";
        $query .= join(', ', array_keys($this->getThisVars()));
        $query .= ") values (";
        $array = [];
        foreach (array_keys($this->getThisVars()) as $name) {
            array_push($array, ":$name");
        }
        $query .= join(', ', $array);
        $query .= ");";

        return $query;
    }

    private function prepareSaveStatement()
    {
        $stmt = $this->conn->prepare($this->buildUpdateQueryString());
        foreach ($this->getThisVars() as $name => $value) {
            $stmt->bindParam(":$name", $this->$name);
        }
        $stmt->bindParam(":id", $this->id);

        return $stmt;
    }

    private function buildUpdateQueryString()
    {
        $query = "update `$this->tableName` set ";
        foreach ($this->getThisVars() as $name => $value) {
            $query .= "$name = :$name, ";
        }

        $query = substr($query, 0, -2);
        $query .= " where id = :id";
        return $query;
    }

    private function getThisVars()
    {
        $vars = get_object_vars($this);
        $exceptVars = ["id", "conn", "tableName", "saveStmt", "insertStmt", "deleteStmt"];
        foreach ($exceptVars as $exception) {
            unset($vars[$exception]);
        }

        return $vars;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    public function getLastInsertId() {
        return $this->conn->lastInsertId();
    }
}
