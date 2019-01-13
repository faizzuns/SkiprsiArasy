<?php
/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 1/13/2019
 * Time: 6:02 AM
 */

require_once "model/BaseModel.php";

class QuestionModel extends BaseModel
{
    protected $user_id;
    protected $question_number;
    protected $question_answer;

    /**
     * QuestionModel constructor.
     * @param $user_id
     * @param $question_number
     * @param $question_answer
     */
    public function __construct($user_id, $question_number, $question_answer)
    {
        parent::__construct("question");
        $this->user_id = $user_id;
        $this->question_number = $question_number;
        $this->question_answer = $question_answer;
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
    public function getQuestionNumber()
    {
        return $this->question_number;
    }

    /**
     * @param mixed $question_number
     */
    public function setQuestionNumber($question_number)
    {
        $this->question_number = $question_number;
    }

    /**
     * @return mixed
     */
    public function getQuestionAnswer()
    {
        return $this->question_answer;
    }

    /**
     * @param mixed $question_answer
     */
    public function setQuestionAnswer($question_answer)
    {
        $this->question_answer = $question_answer;
    }




}