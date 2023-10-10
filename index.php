<?php 

// Start a new session.
if (!isset($_SESSION)) { session_start(); }

// Define Gusto version.
define('VERSION', '1.0.0');
define('BUILD', '19.2.14-10.05.28');

// Include Gusto config.
require_once str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']) . '/config/config.php';

// Run install script if present.
if (is_file(ROOT_DIR . '/install/startup.php')) {
    require_once ROOT_DIR . '/install/startup.php';
} else {
    // Include start files.
    require_once ROOT_DIR . '/autoload.php';
    require_once ROOT_DIR . '/startup.php';
    require_once ROOT_DIR . '/fallback.php';

    // Autoload.
    spl_autoload_register('autoloadCores');
    spl_autoload_register('autoloadControllers');
    spl_autoload_register('autoloadModels');
    spl_autoload_register('autoloadLibraries');

    // Start Gusto.
    startup();
}