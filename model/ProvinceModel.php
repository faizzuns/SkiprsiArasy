<?php
/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 1/12/2019
 * Time: 3:35 PM
 */

require_once "model/BaseModel.php";

class ProvinceModel extends BaseModel
{

    protected $id;
    protected $name;

    /**
     * ProvinceModel constructor.
     */
    public function __construct()
    {
        parent::__construct("provincies");
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

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }




}