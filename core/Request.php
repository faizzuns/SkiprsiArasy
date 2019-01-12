<?php
/**
 * Created by PhpStorm.
 * User: Gabriel B.R
 * Email: gabriel.bentara@gmail.com
 * Date: 13/10/18
 * Time: 3:00 PM
 */

require "IRequest.php";

class Request implements IRequest
{
    private $getData;
    private $postData;
    private $routeVar;
    public $files;
    public $url;

    public function __construct($routeVar)
    {
        $this->routeVar = $routeVar;
        $this->getData = $_GET;
        $this->postData = $_POST;
        $this->files = $_FILES;
        $this->url = $_SERVER["REQUEST_URI"];
    }

    public function get($key)
    {
        if (isset($this->getData[$key])) {
            return $this->getData[$key];
        }

        return null;
    }

    public function post($key)
    {
        if (isset($this->postData[$key])) {
            return $this->postData[$key];
        }

        return null;
    }

    public function param($key)
    {
        if (isset($this->routeVar[$key])) {
            return $this->routeVar[$key];
        }

        return null;
    }

    public function validatePostNotEmpty() {
        foreach ($_POST as $key => $value) {
            if ($value == "") {
                return false;
            }
        }

        return true;
    }

}