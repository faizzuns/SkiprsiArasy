<?php
/**
 * Created by PhpStorm.
 * User: Gabriel B.R
 * Email: gabriel.bentara@gmail.com
 * Date: 13/10/18
 * Time: 4:40 PM
 */

class StaticFile
{
    public static function is_static() {
        $uri = $_SERVER["REQUEST_URI"];
        $uri = substr($uri, 1);
        if (strpos($uri, "static") != 0) {
            return false;
        }


        return is_file($uri) && realpath($uri);
    }
}