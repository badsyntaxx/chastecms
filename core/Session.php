<?php

/**
 * Sessions Core Class
 *
 * The session class logs user data based on the session user id and 
 * handles session related data.
 */ 
class Session
{
    /**
     * Users id number
     * @var int
     */
    public $id;

    /**
     * Log a user session.
     * 
     * Check if the user id session is set. If the session is set access
     * the user model user the session user id and get the user data 
     * related to the session.
     * 
     * @return array
     */
    public function isLogged()
    {
        // Check if the session is set.
        if (isset($_SESSION['id'])) { 
            $this->id = $_SESSION['id'];
            return true;
        } else {
            return false;
        }
    }

    /**
     * Create a session.
     * 
     * @param string $session_name 
     * @param mixed $session_value 
     */
    public function createSession($session_name, $session_value, $override = false)
    {
        if (!isset($_SESSION[$session_name]) || $override) {
            $_SESSION[$session_name] = $session_value;
        }
    }

    public function getSession($session_name)
    {
        $session = isset($_SESSION[$session_name]) ? $_SESSION[$session_name] : false;
        return $session;
    }

    public function deleteSession($session_name)
    {
        unset($_SESSION[$session_name]);
    }

    /**
     * Create a cookie.
     * 
     * @param string $name
     * @param mixed $value
     * @param int $expire 
     */
    public function createCookie($name, $value, $expire)
    {
        setcookie($name, $value, $expire, '/');
    }

    public function getCookie($cookie_name)
    {
        $cookie = isset($_COOKIE[$cookie_name]) ? $_COOKIE[$cookie_name] : false;
        return $cookie;
    }

    public function deleteCookie($cookie_name)
    {
        unset($_COOKIE[$cookie_name]);
    }
}