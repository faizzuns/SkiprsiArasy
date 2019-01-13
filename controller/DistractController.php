<?php
/**
 * Created by PhpStorm.
 * User: yasyars
 * Date: 12/01/19
 * Time: 16.17
 */

require "BaseController.php";
require "core/View.php";

class DistractController extends BaseController
{
    public function __construct($request)
    {
        parent::__construct($request);
    }

    public function view()
    {
        $vars = [];
        View::render("distract", $vars);
    }

    public function abis()
    {
        echo "hehe";


    }
}

