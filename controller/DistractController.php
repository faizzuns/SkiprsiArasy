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
        $selectedImage = $this->request->post('selectedImage');

        if($selectedImage==[1,4,6,8]){

        }
        else{
            $message = "Jawaban anda salah";
            echo "<script type='text/javascript'>alert('$message');</script>";
            $vars = [];
            View::render("distract", $vars);
        }


    }
}

