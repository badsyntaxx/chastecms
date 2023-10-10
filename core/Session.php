<?php

/**
 * Sessions Library Class
 *
 * The session library logs user data based on the session user id and 
 * handles session related data.
 */ 
class Session
{
    /**
     * Users id number
     * 
     * @var int
     */
    public $id;

    /**
     * Check if the user id session is set. If the session is set access
     * the user model user the session user id and get the user data 
     * related to the session.
     * 
     * @return bool - True if a session is set, false if not.
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
     * @param string - $session_name - A name for the session var.
     * @param mixed - $session_value - A mixed value depending on what's needed.
     */
    public function createSession($session_name, $session_value)
    {
        if (!isset($_SESSION[$session_name])) {
            $_SESSION[$session_name] = $session_value;
        }
    }

    /**
     * Create a cookie.
     * 
     * @param string - $name - A name for the cookie.
     * @param mixed - $value - A mixed value depending on what's needed.
     * @param int - $expire - How long before the cookie expires. 
     */
    public function createCookie($name, $value = null, $expire = null, $path = '/', $domain = null, $secure = null, $httponly = null)
    {
        setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
    }

    /**
     * Cookie exists
     * 
     * @param string - Name of the cookie.
     * @return bool - True if cookie is set, false if not.
     */
    public function cookieExists($name)
    {
        if (isset($_COOKIE[$name])) {
            return true;
        } else {
            return false;
        }
    }
}