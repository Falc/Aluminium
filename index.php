<?php

// Define the main paths
define('ALUMINIUM_PATH', dirname(__FILE__).'/aluminium/');
define('APP_PATH',		 dirname(__FILE__).'/app/');
define('CONF_PATH',		 APP_PATH.'conf/');
define('LOGS_PATH',		 APP_PATH.'logs/');

// Include the Application classes
require(ALUMINIUM_PATH.'application.php');
require(APP_PATH.'application.php');

// Create an instance and run!
$app = new App\Application();
$app->run();

?>
