<?php 

// Start a new session.
if (!isset($_SESSION)) { session_start(); }

// Define Chaste version.
define('BUILD', '19.9.18-07.46.13');

// Include Chaste config.
require_once str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']) . '/config/config.php';

// Run install script if present.
if (is_file(ROOT_DIR . '/install/startup.php')) {

    require_once ROOT_DIR . '/install/startup.php';

} else {

    // Include start files.
    require_once ROOT_DIR . '/autoload.php';

    Framework::getInstance();
}

/**
 * This function will check if the first index in the url_arrayd URL array is the string admin.
 * 
 * @return boolean - Simple true or false.
 */
function isAdmin()
{
    if (isset($_GET['url'])) {
        $url_array = explode('/', filter_var(trim($_GET['url'], '/'), FILTER_SANITIZE_URL));
    }

    if (isset($url_array[0])) {
        if ($url_array[0] == 'admin') {
            return true;
        } else {
            return false;
        }
    }
}