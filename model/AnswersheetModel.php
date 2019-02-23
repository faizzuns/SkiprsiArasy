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
    protected $tendecy_2;
//    0 = netral
//    1 = positif
//    2 = negatif
    protected $id_news;
    protected $distract;
    protected $correction;
    protected $termo_1;
    protected $termo_2;
    protected $dbrief_1;
    protected $dbrief_2;
    protected $first_pick;
    protected $position;

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
        else if ($this->termo_1 == NULL) return 1;
        else if ($this->distract == NULL) return 3;
        else if ($this->correction == NULL) return 4;
        else if ($this->termo_2 == NULL) return 5;
        else if ($this->tendecy_2 == NULL) return 6;
        else if ($this->dbrief_1 == NULL) return 7;
        else return 8;
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param mixed $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
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

    /**
     * @return mixed
     */
    public function getTendecy2()
    {
        return $this->tendecy_2;
    }

    /**
     * @param mixed $tendecy_2
     */
    public function setTendecy2($tendecy_2)
    {
        $this->tendecy_2 = $tendecy_2;
    }

    /**
     * @return mixed
     */
    public function getDistract()
    {
        return $this->distract;
    }

    /**
     * @param mixed $distract
     */
    public function setDistract($distract)
    {
        $this->distract = $distract;
    }

    /**
     * @return mixed
     */
    public function getCorrection()
    {
        return $this->correction;
    }

    /**
     * @param mixed $correction
     */
    public function setCorrection($correction)
    {
        $this->correction = $correction;
    }

    /**
     * @return mixed
     */
    public function getFirstPick()
    {
        return $this->first_pick;
    }

    /**
     * @param mixed $first_pick
     */
    public function setFirstPick($first_pick)
    {
        $this->first_pick = $first_pick;
    }



    public function generateIdNews()
    {
        if ($this->id_news == NULL) {
            $stmt = $this->conn->prepare("SELECT id_news, COUNT(*) AS total FROM $this->tableName WHERE first_pick = :first_pick "
                . "AND `position` = :pos AND id_news IS NOT NULL GROUP BY id_news ORDER BY total ASC");
            $stmt->bindParam("first_pick", $this->first_pick);
            $stmt->bindParam("pos", $this->position);
            $idnews = 0;
            $check = [0, 0, 0];
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $idnews = $row['id_news'];
                $check[$idnews] = 1;
            }
            if ($check[0] == 0) $this->id_news = 0;
            else if ($check[1] == 0) $this->id_news = 1;
            else if ($check[2] == 0) $this->id_news = 2;
            else $this->id_news = $idnews;

//            if ($this->id_news == 0) {
//                $this->correction = -1;
//                $this->distract = -1;
//                $this->termo_2 = -1;
//                $this->tendecy_2 = -1;
//                $this->dbrief_1 = -1;
//                $this->dbrief_2 = -1;
//            }
        }
    }

    public function generatePosition()
    {
        $stmt = $this->conn->prepare("SELECT `position`, COUNT(*) AS total FROM $this->tableName GROUP BY `position` ORDER BY total ASC");
        $stmt->bindParam("first_pick", $this->first_pick);
        $pos = 0;
        $check = [0,0];
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pos = $row['position'];
            $check[$pos] = 1;
        }

        if ($check[0] == 0) $this->position = 0;
        else $this->position = 1;
    }


}