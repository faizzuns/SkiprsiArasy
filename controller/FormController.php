<?php
/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 1/12/2019
 * Time: 12:34 PM
 */

require "BaseController.php";
require "core/View.php";
require "model/ListProvince.php";
require_once "core/Session.php";
require "model/UserModel.php";
require "model/AnswersheetModel.php";

class FormController extends BaseController
{
    protected $direct = false;

    public function __construct($request)
    {
        parent::__construct($request);
    }

    public function start()
    {
        $session = new Session();
        if ($session->inSession()) {
            $this->direct = true;
            $this->continueForm();
        } else {
            $provincies = new ListProvince();
            $provincies->loadProvincies();

            $vars = [
                "provinces" =>$provincies->getList()
            ];
            View::render("identity", $vars);
        }
    }

    public function register()
    {
        if (!$this->request->validatePostNotEmpty()) {
            View::render("Register", [
                "error" => "All fields must be filled"
            ]);
            return;
        }

        $user = new UserModel();
        $user->setName($this->request->post("name"));
        $user->setAge($this->request->post("age"));
        $user->setEducation($this->request->post("education"));
        $user->setPhone($this->request->post("phone"));
        $user->setProfession($this->request->post("profession"));
        $user->setProvince($this->request->post("province"));
        $user->setRegency($this->request->post("district"));
        $user->setVillage($this->request->post("village"));
        $user->insert();

        $answersheet = new AnswersheetModel();
        $answersheet->setUserId($user->getId());
        $answersheet->insert();

        $session = new Session();
        $session->setSession($user->getId());

        View::redirect("/forms");
    }

    public function continueForm() {
        $ssn = new SessionModel();
        $ssn->setSessionId($_COOKIE["session"]);
        $ssn->load();

        $fill = new AnswersheetModel();
        $fill->setUserId($ssn->getUserId());
        $fill->loadFromUserId();

        if ($this->direct) $state = $fill->getState();
        else $state = (int) $this->request->post("state");

        if ($state == -1) return;
        else if ($state == 0) $this->firstTendency();
        else if ($state == 1) $this->news($ssn->getUserId());
        else if ($state == 2) $this->firstMeasure($ssn->getUserId());
        else if ($state == 3) $this->distract($ssn->getUserId());
        else if ($state == 4) $this->correction($ssn->getUserId());
        else if ($state == 5) $this->secondMeasure($ssn->getUserId());
        else if ($state == 6) $this->secondTendency($ssn->getUserId());
        else if ($state == 7) $this->dbrief($ssn->getUserId());

    }

    public function firstTendency() {
        View::render("tendency", ["state" => 1]);
    }

    public function news($userId) {
        $fill = new AnswersheetModel();
        $fill->setUserId($userId);
        $fill->loadFromUserId();
        if (!$this->direct) {
            $score = $this->request->post("tendency");
            if ($score == 5) $pick = "Netral";
            else if ($score > 5) $pick = "Prabowo";
            else $pick = "Jokowi";
            $fill->setTendency1($score);
            $fill->setFirstPick($pick);
            $fill->generateIdNews();
            $fill->save();
        }
        $vars = [];
        View::render("news", $vars);
    }

    public function firstMeasure($userId) {
        if (!$this->direct) {

        }
    }

    public function distract($userId) {
        if (!$this->direct) {

        }
    }

    public function correction($userId) {
        if (!$this->direct) {

        }
    }

    public function secondMeasure($userId) {
        if (!$this->direct) {

        }
    }

    public function secondTendency($userId) {
        if (!$this->direct) {

        }
    }

    public function dbrief($userId) {
        if (!$this->direct) {

        }
    }



}