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
require "model/SessionModel.php";

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

        $session = new Session();
        $session->setSession($user->getId());

        View::redirect("/forms/fill");
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
        else if ($state == 0) $this->firstTendency($fill);
        else if ($state == 1) $this->news($fill);
        else if ($state == 2) $this->firstMeasure($fill);
        else if ($state == 3) $this->distract($fill);
        else if ($state == 4) $this->correction($fill);
        else if ($state == 5) $this->secondMeasure($fill);
        else if ($state == 6) $this->secondTendency($fill);
        else if ($state == 7) $this->dbrief($fill);

    }

    public function firstTendency($fill) {
        if (!$this->direct) {

        }
    }

    public function news($fill) {
        if (!$this->direct) {

        }
    }

    public function firstMeasure($fill) {
        if (!$this->direct) {

        }
    }

    public function distract($fill) {
        if (!$this->direct) {

        }
    }

    public function correction($fill) {
        if (!$this->direct) {

        }
    }

    public function secondMeasure($fill) {
        if (!$this->direct) {

        }
    }

    public function secondTendency($fill) {
        if (!$this->direct) {

        }
    }

    public function dbrief($fill) {
        if (!$this->direct) {

        }
    }



}