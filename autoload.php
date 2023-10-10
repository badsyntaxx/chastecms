<?php 

/**
 * Autoload Cores
 *
 * Load core classes from the core directory.
 * 
 * Route
 * - /root/cores/
 */
function autoloadCores($class)
{
    $core = CORES_DIR . '/' . $class . '.php';
    if (is_file($core)) {
        require_once $core;
    }
}

/**
 * Autoload Controllers
 *
 * Load controller classes from the controller directory.
 * 
 * Route
 * - /root/controllers/
 */
function autoloadControllers($class)
{
    $file = '/' . $class . '.php';
    $controller_paths = iterate(CONTROLLERS_DIR);

    foreach ($controller_paths as $cp) {
        $dirs = explode('/', $cp);
        array_pop($dirs);
        $path = implode('/', $dirs);

        if (is_file($path . $file)) {
            $controller = $path . $file;
            require_once $controller;
        }
    }
}

/**
 * Autoload Models
 *
 * Load model classes from the model directory.
 * 
 * Route
 * - /root/models/
 */
function autoloadModels($class)
{
    $model = MODELS_DIR . '/' . $class . '.php';
    if (file_exists($model)) {
        require_once $model;
    }
}

/**
 * Autoload Libraries
 *
 * Load library classes from the library directory.
 * 
 * Route
 * - /root/libraries/
 */
function autoloadLibraries($class)
{
    $lib = LIBRARIES_DIR . '/' . $class . '.php';
    if (file_exists($lib)) {
        require_once $lib;
    }
}

/**
 * Recursively interate dirs
 *
 * This function recursively iterates through a given dir and all its sub dirs.
 * It will return an array of all the files found with their absolute paths.
 * 
 * @param string $source
 * @return array
 */
function iterate($source)
{
    $dir = new RecursiveDirectoryIterator($source);

    foreach (new RecursiveIteratorIterator($dir) as $filename) {
        $files[] = ucfirst($filename->__toString());
    }

    foreach ($files as $file) {
        $file = str_replace('\\', '/', $file);
        $file = explode('/', $file);
        $file = array_diff($file, ['..', '.']);
        $file = implode('/', $file);
        if (is_file($file)) {
            $classes[] = $file;
        }
    }

    return $classes;
}