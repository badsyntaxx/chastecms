<?php 

/**
 * This function will start the program by trying to match a route from the`routes` table 
 * to the route in the URL. If a match is made the controller class that matches that route 
 * will be called, along with any possible method or parameters. If no match is found, or 
 * the route leads to the admin area, the fallback function will be called.
 */
function startup()
{
    $url = isset($_GET['url']) ? trim($_GET['url']) : null;
    $routes = getRouteData('`route`', true);
    $controllers = getRouteData('`route`, `controller`', false);
    $route = getRoute($url, $routes);
    $class = getController($controllers, $route);
    $method = 'init';
    $parsed_url = isset($url) ? explode('/', filter_var(trim($url, '/'), FILTER_SANITIZE_URL)) : [];

    if (isAdmin()) {
        return fallback();
    }

    if (!class_exists($class ?? '')) {
        return fallback();
    }

    $controller = new $class();
    unset($parsed_url[0]);

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

    if (!is_callable([$controller, $method])) {
        return fallback();
    }

    return call_user_func_array([$controller, $method], $params);
}

/**
 * This function will query the database for data from the `routes` table. This function comes
 * with an implode parameter to prevent multidimensional arrays where none are needed.
 * 
 * @param string - $select - Which columns to select from the table.
 * @param boolean - $implode - A choice to implode a row from the database.
 * @return array - Route data from the `routes` table.
 */
function getRouteData($select, $implode = false)
{
    $con = Database::getInstance();
    $query = $con->mysqli->query('SELECT ' . $select . ' FROM `routes`');

    while ($col = $query->fetch_assoc()) {
        if ($implode) {
            $data[] = implode($col);
        } else {
            $data[] = $col;
        }
    }

    return $data;
}

/**
 * This function will search through the `routes` table in the database and compare them against the URL. 
 * If a match is found, it will be returned. If no match is found, an empty string will be returned.
 * 
 * @param string - $url - The current URL.
 * @param array - $data - An array of routes from the database.
 * @return string - The route.
 */
function getRoute($url = '', $routes = [])
{
    $arr = explode('/', $url ?? '');
    $arr_count = count($arr);
    $potentials = [];
    $route = '';

    for ($i = 0; $i < $arr_count; $i++) { 
        $route .= '/' . $arr[$i];
        array_push($potentials, $route);     
    }

    if (isset($potentials[1])) {
        if (strpos($potentials[1], $potentials[0]) !== false) {
            unset($potentials[0]);
        }
    }

    $result = array_intersect($routes, $potentials);

    return implode($result);
}

/**
 * This function will search through routes from the `routes` table, and compare them to the route that
 * was returned from the getRoute() function. If a match is found, it will return the controller that
 * matches that route.
 * 
 * @param array - $data - A multidimensional array of data from the `routes` table.
 * @param string - $route - The route from the getRoute() function.
 * @return string - A controller class name.
 */
function getController($data, $route)
{
    foreach ($data as $d) {
        if ($route === $d['route']) {
            $controller = $d['controller'];
            return $controller;
        }
    }
}

/**
 * This function will check if the first index in the parsed_urld URL array is the string admin.
 * 
 * @return boolean - Simple true or false.
 */
function isAdmin()
{
    if (isset($_GET['url'])) {
        $parsed_url = explode('/', filter_var(trim($_GET['url'], '/'), FILTER_SANITIZE_URL));
    }

    if (isset($parsed_url[0])) {
        if ($parsed_url[0] == 'admin') {
            return true;
        } else {
            return false;
        }
    }
}