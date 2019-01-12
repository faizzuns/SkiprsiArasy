<?php
/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 1/12/2019
 * Time: 11:01 AM
 */

class NotFillFormMiddleware
{
    public static function execute(IRequest $request)
    {
        $session = new Session();
        if (!$session->inSession()) {
            header("Location: /");
            return True;
        }

        return False;
    }
}