<?php

class Authenticate
{
    /**
     * Store the single instance of the framework.
     * @var object
     */
    private static $instance;

    public function __construct($session, $load)
    {   
        $url = isset($_GET["url"]) ? filter_var(trim($_GET["url"]), FILTER_SANITIZE_URL) : "dashboard";
        $routes = $this->getProtectedRoutes();
        
        foreach ($routes as $r) {
    
            if (preg_match("/(^(" . $r["route"] . ").*?$)/", $url)) {

                if ($r["level"] == 0) return false;

                if ($session->isLogged()) {
                    $user = $load->model("users")->getUser("key", $session->id);
                    if ($user["group"] >= $r["level"]) {
                        return true;
                    }
                }
                
                if ($r["redirect"]) {
                    $session->createCookie("desired_route", $url, time() + 60);
                    $load->route($r["redirect"]);
                } 
                
                return $load->controller("NotFound")->index();
            }
        }  

        return false;
    }

    public function getProtectedRoutes()
    {
        $routes = [
            ["route" => "account\/getAccountData", "level" => 2, "redirect" => false],
            ["route" => "account\/uploadAvatar", "level" => 2, "redirect" => false],
            ["route" => "account\/validate", "level" => 2, "redirect" => false],
            ["route" => "account\/activate", "level" => 0, "redirect" => false],
            ["route" => "account\/send", "level" => 2, "redirect" => false],
            ["route" => "account\/forgot", "level" => 0, "redirect" => false],
            ["route" => "account\/sendRecoveryMail", "level" => 0, "redirect" => false],
            ["route" => "account\/reset", "level" => 0, "redirect" => false],
            ["route" => "account\/saveResetPassword", "level" => 0, "redirect" => false],
            ["route" => "account\/checkUsername", "level" => 0, "redirect" => false],
            ["route" => "account\/checkEmail", "level" => 0, "redirect" => false],
            ["route" => "account", "level" => 2, "redirect" => "/login"],
            ["route" => "logout\/destroySession", "level" => 1, "redirect" => false],
            ["route" => "signup\/validateFirstname", "level" => 2, "redirect" => false],
            ["route" => "signup\/validateLastname", "level" => 2, "redirect" => false],
            ["route" => "signup\/validateEmail", "level" => 2, "redirect" => false],
            ["route" => "signup\/validatePassword", "level" => 2, "redirect" => false],
            ["route" => "signup\/registerUser", "level" => 2, "redirect" => false],
            ["route" => "signup\/sendActivationMail", "level" => 2, "redirect" => false],
            ["route" => "admin", "level" => 3, "redirect" => '/login'],
            // This app specific routes
        ];

        return $routes;
    }

    /**
     * Create a static instance of this class.
     * 
     * @return object
     */
    public static function getInstance($session, $load) 
    {
        if (!isset(static::$instance)) {
            static::$instance = new Authenticate($session, $load);
        }
        return static::$instance;
    }
}
