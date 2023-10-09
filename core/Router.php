<?php

class Router
{
    /**
     * Store the single instance of the framework.
     * @var object
     */
    private static $instance;

    /**
     * This function will start the program by trying to match a route from the`routes` table 
     * to the route in the URL. If a match is made the controller class that matches that route 
     * will be called, along with any possible method or parameters. If no match is found, or 
     * the route leads to the admin area, the fallback function will be called.
     */
    public function __construct()
    {
        $url = isset($_GET['url']) ? trim($_GET['url']) : null;
        $url_array = isset($url) ? explode('/', filter_var(trim($url, '/'), FILTER_SANITIZE_URL)) : [];

        if (isAdmin()) {
            return $this->fallback();
        }
        
        $con = Database::getInstance();

        if (isset($url_array[0])) {
            $route_data = $this->getRouteData($con, '/' . $url_array[0]);
        } else {
            $route_data = '';
        }

        if (!$route_data) {
            return $this->fallback();
        }

        $route_anchor = $route_data[0];
        $controller = $this->getControllerData($con, $route_anchor);
        $class = implode($controller);
        $method = 'index';
        
        $controller = new $class();
        unset($url_array[0]);

        if (isset($url_array[1])) {
            $possible_method = preg_replace('/[^A-Za-z0-9]/', '', $url_array[1]);
            if (method_exists($controller, $possible_method)) {
                $method = $possible_method;
                unset($url_array[1]);
            }

            $method_type = new ReflectionMethod($controller, $method);
            if (!$method_type->isPublic()) {
                $method = 'index';
            }
        }

        $params = isset($url_array) ? array_values($url_array) : [];

        if (!is_callable([$controller, $method])) {
            return $this->fallback();
        }

        return call_user_func_array([$controller, $method], $params);
        
    }

    /** */
    public function getRouteData($con, $route)
    {
        $query = $con->mysqli->query('SELECT `route_anchor`, `route` FROM `routes` WHERE `route` = "' . $route . '"');
        $data = $query->fetch_array();

        return $data;
    }

    public function getControllerData($con, $route_anchor)
    {
        $query = $con->mysqli->query('SELECT `controller_file` FROM `pages` WHERE `pages_id` = "' . $route_anchor . '"');
        $data = $query->fetch_row();
        $controller = str_replace('.php', '', $data);

        return $controller;
    }
    
    /**
     * This function will parse the url and use it to make a controller, method and params.
     * Unlike start, it will not compare against the routes table. Instead it will compare
     * against controller files. If the controller portion of the URL cannot find a 
     * controller file to match, it will default to the not found controller.
     */
    public function fallback()
    {
        $this->url = isset($_GET['url']) ? explode('/', filter_var(trim($_GET['url'], '/'), FILTER_SANITIZE_URL)) : null;

        $this->determineController();

        $this->determineMethod();

        $this->determineParams();

        $this->dispatch();
    }

    private function determineController()
    {
        $controller = 'home';

        if (isset($this->url[0])) {
            $controller = $this->url[0];
            if (isAdmin()) {
                $this->url = $this->makeAdminUrl($this->url);
                $controller = isset($this->url[0]) ? $this->url[0] : 'Dashboard';
            } 

            unset($this->url[0]);
        }
        
        if (strpos($controller, '-')) {
            $keys = explode('-', $controller);
            foreach ($keys as $key) {
                $array[] = ucfirst($key);
            }
            $controller = implode($array);
        }
        
        if (isAdmin()) {
            $controller = 'Admin' . ucfirst($controller) . 'Controller';
        } else {
            $controller = ucfirst($controller) . 'Controller';
        }

        if (!class_exists($controller)) {
            $controller = 'NotFoundController';
        }
        
        $this->controller = new $controller();
    }

    private function determineMethod()
    {
        $this->method = 'index';

        if (isset($this->url[1])) {
            $possible_method = preg_replace('/[^A-Za-z0-9]/', '', $this->url[1]);
            if (method_exists($this->controller, $possible_method)) {
                $this->method = $possible_method;
                unset($this->url[1]);
            }

            $method_type = new ReflectionMethod($this->controller, $this->method);
            if (!$method_type->isPublic()) {
                $this->method = 'index';
            }
        }
    }

    private function determineParams()
    {
        $this->params = isset($this->url) ? array_values($this->url) : [];
    }

    private function dispatch()
    {
        if (is_callable([$this->controller, $this->method])) {
            call_user_func_array([$this->controller, $this->method], $this->params);
        }
    }

    /**
     * This function will unset the first index in the parsed URL array and rebase its index. 
     * This function should be called after using the isAdmin() function.
     * Starts as: /admin/controller/method/param
     * Becomes: /controller/method/param
     * 
     * @param array - $parsed_url - The current URL in an array.
     * @return array - The URL stripped of its first intex.
     */
    public function makeAdminUrl($parsed_url)
    {
        unset($parsed_url[0]);
        $parsed_url = array_values($parsed_url);

        return $parsed_url;
    }

    /**
     * Create a static instance of this class.
     * 
     * @return object
     */
    public static function getInstance() 
    {
        if (!isset(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }
}
