<?php
/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 1/12/2019
 * Time: 6:56 PM
 */

require_once "model/BaseModel.php";

class AnswersheetModel extends BaseModel
{
    protected $user_id;
    protected $tendency_1;
    protected $tendency_2;
    protected $id_news;
    protected $distract;
    protected $correction;
    protected $termo_1;
    protected $termo_2;
    protected $dbrief_1;
    protected $dbrief_2;

    /**
     * AnswersheetModel constructor.
     */
    public function __construct()
    {
        parent::__construct("answersheet");
    }

    public function loadFromUserId()
    {
        $stmt = $this->conn->prepare("SELECT * FROM $this->tableName WHERE user_id = :user_id");
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result == null) {
            return;
        }
        foreach ($result as $column => $value) {
            $this->$column = $value;
        }
    }

    public function getState() {
        if ($this->tendency_1 == NULL) return 0;
        else if ($this->id_news == NULL) return 1;
        else if ($this->termo_1 == NULL) return 2;
        else if ($this->distract == NULL) return 3;
        else if ($this->correction == NULL) return 4;
        else if ($this->termo_2 == NULL) return 5;
        else if ($this->tendency_2 == NULL) return 6;
        else if ($this->dbrief_1 == NULL) return 7;
        else return -1;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * @return mixed
     */
    public function getTendency1()
    {
        return $this->tendency_1;
    }

    /**
     * @param mixed $tendency_1
     */
    public function setTendency1($tendency_1)
    {
        $this->tendency_1 = $tendency_1;
    }

    /**
     * @return mixed
     */
    public function getTendency2()
    {
        return $this->tendency_2;
    }

    /**
     * @param mixed $tendency_2
     */
    public function setTendency2($tendency_2)
    {
        $this->tendency_2 = $tendency_2;
    }

    /**
     * @return mixed
     */
    public function getIdNews()
    {
        return $this->id_news;
    }

    /**
     * @param mixed $id_news
     */
    public function setIdNews($id_news)
    {
        $this->id_news = $id_news;
    }

    /**
     * @return mixed
     */
    public function getTermo1()
    {
        return $this->termo_1;
    }

    /**
     * @param mixed $termo_1
     */
    public function setTermo1($termo_1)
    {
        $this->termo_1 = $termo_1;
    }

    /**
     * @return mixed
     */
    public function getTermo2()
    {
        return $this->termo_2;
    }

    /**
     * @param mixed $termo_2
     */
    public function setTermo2($termo_2)
    {
        $this->termo_2 = $termo_2;
    }



    /**
     * @return mixed
     */
    public function getDbrief1()
    {
        return $this->dbrief_1;
    }

    /**
     * @param mixed $dbrief_1
     */
    public function setDbrief1($dbrief_1)
    {
        $this->dbrief_1 = $dbrief_1;
    }

    /**
     * @return mixed
     */
    public function getDbrief2()
    {
        return $this->dbrief_2;
    }

    /**
     * @param mixed $dbrief_2
     */
    public function setDbrief2($dbrief_2)
    {
        $this->dbrief_2 = $dbrief_2;
    }


}