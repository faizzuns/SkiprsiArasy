<?php
/**
 * Created by PhpStorm.
 * User: Gabriel B.R
 * Email: gabriel.bentara@gmail.com
 * Date: 13/10/18
 * Time: 2:41 PM
 */

class BaseController
{
    public $request;

    public function __construct(IRequest $request)
    {
        $this->request = $request;
    }
}