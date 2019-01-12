<?php
/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 1/12/2019
 * Time: 9:38 AM
 */

require_once "core/StaticFile.php";
require_once "core/Router.php";

if (StaticFile::is_static()) {
    return False;
}

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

$router = new Router();

$router->get("/", "Home@view");
$router->post("/register", "Form@register");
$router->get("/regency", "Regencies@getRegency");
$router->get("/forms", "Form@start");
<<<<<<< HEAD
$router->get("/distract","Distract@view");
$router->post("/abis","Distract@abis");

=======
$router->post("/forms", "Form@continueForms");
>>>>>>> 58d59474b23574d303052fa7c3aebc2f96795174

$router->execute();