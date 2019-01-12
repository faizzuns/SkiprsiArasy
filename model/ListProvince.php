<?php
/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 1/12/2019
 * Time: 3:49 PM
 */

require_once "model/BaseModel.php";

class ListProvince extends BaseModel
{
    protected $list;

    /**
     * ListProvince constructor.
     */
    public function __construct()
    {
        parent::__construct("provinces");
        $this->list = array();
    }

    public function loadProvincies() {
        $stmt = $this->conn->prepare("SELECT * FROM $this->tableName");
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $item = ["id"=> $row['id'], "name"=> $row["name"]];
            array_push($this->list, $item);
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