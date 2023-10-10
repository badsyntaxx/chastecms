<?php 

/**
 * This function will parse the url and use it to make a controller, method and params.
 * Unlike start, it will not compare against the routes table. Instead it will compare
 * against controller files. If the controller portion of the URL cannot find a 
 * controller file to match, it will default to the not found controller.
 */
function fallback()
{       
    $controller = 'home';
    $method = 'init';
    $parsed_url = isset($_GET['url']) ? explode('/', filter_var(trim($_GET['url'], '/'), FILTER_SANITIZE_URL)) : null;

    if (isset($parsed_url[0])) {
        $controller = $parsed_url[0];
        if (isAdmin()) {
            $parsed_url = makeAdminUrl($parsed_url);
            $controller = isset($parsed_url[0]) ? $parsed_url[0] : 'Overview';
        } 

        unset($parsed_url[0]);
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

    $controller = new $controller();

    if (isset($parsed_url[1])) {
        $possible_method = preg_replace('/[^A-Za-z0-9]/', '', $parsed_url[1]);
        if (method_exists($controller, $possible_method)) {
            $method = $possible_method;
            unset($parsed_url[1]);
        }

        $method_type = new ReflectionMethod($controller, $method);
        if (!$method_type->isPublic()) {
            $method = 'init';
        }
    }

    $params = isset($parsed_url) ? array_values($parsed_url) : [];

    if (is_callable([$controller, $method])) {
        call_user_func_array([$controller, $method], $params);
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
function makeAdminUrl($parsed_url)
{
    unset($parsed_url[0]);
    $parsed_url = array_values($parsed_url);

    return $parsed_url;
}