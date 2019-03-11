<?php
/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 3/12/2019
 * Time: 12:13 AM
 */

require_once "model/BaseModel.php";

class Users extends BaseModel
{
    protected $list;

    public function __construct()
    {
        parent::__construct("user");
        $this->list = array();
    }

    public function loadAllUsers() {
        $stmt = $this->conn->prepare("SELECT * FROM $this->tableName");
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