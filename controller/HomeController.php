<?php
/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 1/12/2019
 * Time: 11:28 AM
 */
require "BaseController.php";
require_once "core/Session.php";
require "core/View.php";

class HomeController extends BaseController
{
    public function __construct($request)
    {
        parent::__construct($request);
    }

    public function view()
    {
        $session = new Session();
        if ($session->inSession()) {
            View::redirect("/forms");
        } else {
            $vars = [];
            View::render("home", $vars);
        }
    }
}