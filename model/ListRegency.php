<?php
/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 1/12/2019
 * Time: 4:51 PM
 */

require "model/BaseModel.php";

class ListRegency extends BaseModel
{
    protected $list;

    /**
     * ListRegency constructor.
     */
    public function __construct()
    {
        parent::__construct("regencies");
        $this->list = array();
    }

    public function getRegency($name)
    {
        $stmt = $this->conn->prepare("SELECT id FROM provinces WHERE name = :province_name");
        $stmt->bindParam(":province_name", $name);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $id_province = $result["id"];

        $stmt = $this->conn->prepare("SELECT * FROM $this->tableName WHERE province_id = :id");
        $stmt->bindParam(":id", $id_province);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $item = ["id"=> $row['id'], "name"=> $row["name"]];
            array_push($this->list, $item);
        }
    }

    /**
     * @return mixed
     */
    public function getList()
    {
        return $this->list;
    }




}