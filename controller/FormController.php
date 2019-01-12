<?php
/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 1/12/2019
 * Time: 12:34 PM
 */

require "BaseController.php";
require "core/View.php";

class FormController extends BaseController
{
    public function __construct($request)
    {
        parent::__construct($request);
    }

    public function start()
    {



        $vars = [];
        View::render("identity", $vars);
    }

}