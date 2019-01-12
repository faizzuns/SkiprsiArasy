<?php
/**
 * Created by PhpStorm.
 * User: Gabriel B.R
 * Email: gabriel.bentara@gmail.com
 * Date: 13/10/18
 * Time: 4:16 PM
 */

class View
{
    public static function render($viewName, $vars)
    {
        $file = "view/" . $viewName . ".phtml";

        if (!is_file($file)) {
            echo "View not found";
            return;
        }

        extract($vars);
        require $file;
    }

    public static function redirect($route) {
        header("Location: $route");
    }
}