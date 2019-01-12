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

class FormController extends BaseController
{
    public function __construct($request)
    {
        parent::__construct($request);
    }

    public function start()
    {
        $session = new Session();
        if ($session->inSession()) {
            echo "MANTAP";
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
        echo "HALO";
    }

}