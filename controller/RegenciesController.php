<?php
/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 1/12/2019
 * Time: 4:48 PM
 */

require "controller/BaseController.php";
require "model/ListRegency.php";

class RegenciesController extends BaseController
{
    public function __construct($request)
    {
        parent::__construct($request);
    }

    public function getRegency() {
        $province = $this->request->get("province_name");
        $regencies = new ListRegency();
        $regencies->getRegency($province);

        header('Content-Type: application/json');
        echo json_encode($regencies->getList());
    }

}