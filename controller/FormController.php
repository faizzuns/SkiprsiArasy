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

class FormController extends BaseController
{
    public function __construct($request)
    {
        parent::__construct($request);
    }

    public function start()
    {
        $provincies = new ListProvince();
        $provincies->loadProvincies();

        $vars = [
            "provinces" =>$provincies->getList()
        ];
        View::render("identity", $vars);
    }

}