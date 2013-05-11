<?php
// Define path to application directory
defined('APPLICATION_PATH') ||
 define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/application'));

 // Define application environment
defined('APPLICATION_ENV') || define(
	'APPLICATION_ENV',
    (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production')
);

defined('APPLICATION_CONFIG_PATH') ||
 define('APPLICATION_CONFIG_PATH', APPLICATION_PATH . '/configs');

 // Define path to application directory
defined('APPLICATION_PUBLIC_PATH') ||
define(
    'APPLICATION_PUBLIC_PATH',
    '/public'
);

defined('APPLICATION_ACTION_PATH') ||
 define('APPLICATION_ACTION_PATH', '');
// Ensure library/ is on include_path
set_include_path(
    implode(
        PATH_SEPARATOR, 
        array(
            realpath(APPLICATION_PATH . '/../library'), 
            realpath(APPLICATION_PATH . '/models'), 
            get_include_path()
        )
    ).";".realpath(APPLICATION_PATH . '/models')
);

//Register configs
require_once 'Zend/Registry.php';
require_once "Zend/Config/Ini.php";
$configs = array(
    'db' => APPLICATION_CONFIG_PATH . '/db.ini',
	'config' => APPLICATION_CONFIG_PATH . '/config.ini'
);
foreach ($configs as $key => $configPath) {
    $config = new Zend_Config_Ini($configPath, APPLICATION_ENV);
    $registry = Zend_Registry::getInstance()->set($key, $config);
}

/** Zend_Application */
require_once 'Zend/Application.php';
require_once "Zend/Loader/Autoloader.php";
Zend_Loader_Autoloader::getInstance()->setFallbackAutoloader(true);
// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV, 
    APPLICATION_CONFIG_PATH . '/application.ini'
);
//$FrontController = Zend_Controller_Front::getInstance();
$application->bootstrap()->run();
