<?php 

// Directories
define('ROOT_DIR', str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']));
define('HOST', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https://' . $_SERVER['HTTP_HOST'] : 'http://' . $_SERVER['HTTP_HOST']);
define('CONTROLLERS_DIR', ROOT_DIR . '/controllers');
define('CORES_DIR', ROOT_DIR . '/core');
define('LANGUAGE_DIR', ROOT_DIR . '/language');
define('LIBRARIES_DIR', ROOT_DIR . '/libraries');
define('MODELS_DIR', ROOT_DIR . '/models');
define('PLUGINS_DIR', ROOT_DIR . '/plugins');
define('STORAGE_DIR', ROOT_DIR . '/storage');
define('VIEWS_DIR', ROOT_DIR . '/views'); 

// Timezone
date_default_timezone_set('Pacific/Honolulu');

// Errors
error_reporting(E_ALL);
ini_set('display_errors', 'On');

// Prevent site from being loaded by iframes.
header('X-Frame-Options: SAMEORIGIN'); // DENY or SAMEORIGIN
