<?php
/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 3/12/2019
 * Time: 1:19 AM
 */

require_once "model/BaseModel.php";

class Questions extends BaseModel
{

    protected $list;

    public function __construct()
    {
        parent::__construct("question");
        $this->list = array();
    }

    public function loadUsersQuestion1($userId) {
        $stmt = $this->conn->prepare("SELECT * FROM $this->tableName WHERE user_id = :userId AND question_number <= 23 LIMIT 23");
        $stmt->bindParam(":userId", $userId);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($this->list, $row);
        }
    }

    public function loadUsersQuestion2($userId) {
        $stmt = $this->conn->prepare("SELECT * FROM $this->tableName WHERE user_id = :userId AND question_number > 23 LIMIT 23");
        $stmt->bindParam(":userId", $userId);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($this->list, $row);
        }
    }

    /**
     * @return array
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * @param array $list
     */
    public function setList($list)
    {
        $this->list = $list;
    }

}