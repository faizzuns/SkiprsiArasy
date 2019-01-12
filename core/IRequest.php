<?php
/**
 * Created by PhpStorm.
 * User: Gabriel B.R
 * Email: gabriel.bentara@gmail.com
 * Date: 13/10/18
 * Time: 3:23 PM
 */

interface IRequest
{
    public function param($key);
    public function get($key);
    public function post($key);
    public function validatePostNotEmpty();
}