<?php
/**
 * Created by PhpStorm.
 * User: Gabriel B.R
 * Email: gabriel.bentara@gmail.com
 * Date: 23/10/18
 * Time: 8:02 AM
 */

require "model/SessionModel.php";

class Session
{
    private $session;

    public function __construct()
    {
        $this->session = new SessionModel();
        date_default_timezone_set('Asia/Jakarta');
    }

    public function generateSessionId() {
        $sessionId = "";
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        for ($i = 0; $i < 30; $i++) {
            $sessionId .= $chars[rand(0, strlen($chars)-1)];
        }

        return $sessionId;
    }

    public function setSession($userId) {
        if ($this->inSession()) {
            return;
        }
        $sessionId = $this->generateSessionId();
        $this->session->setSessionId($sessionId);
        $this->session->setUserId($userId);
        $this->session->setExpire(date('Y-m-d H:i:s', strtotime("+1 days")));
        $this->session->insert();

        setcookie("session", $sessionId);
    }

    public function inSession() {
        if (!isset($_COOKIE["session"])) {
            return False;
        }

        $sessionId = $_COOKIE["session"];
        $this->session->setSessionId($sessionId);
        $this->session->load();

        if ($this->session->getUserId() == null) {
            $this->session->delete();
            return False;
        }

        if ($this->session->getExpire() < date('Y-m-d H:i:s')) {
            $this->session->delete();
            return False;
        }

        return $this->session->getUserId();
    }

    public function destroy() {
        if (!isset($_COOKIE["session"])) {
            return;
        }

        $sessionId = $_COOKIE["session"];
        $this->session->setSessionId($sessionId);
        $this->session->load();
        $this->session->delete();

        unset($_COOKIE["session"]);
    }
}