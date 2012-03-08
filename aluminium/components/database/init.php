<?php

// Required files
require_once(dirname(__FILE__).'/classes/database.php');
require_once(dirname(__FILE__).'/classes/drivers/database_driver.php');

// Constant definitions
if(!defined('DB_DRIVERS_PATH')) {
	define('DB_DRIVERS_PATH',	dirname(__FILE__).'/classes/drivers/');
}

?>
