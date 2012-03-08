<?php

// Required files
require_once(dirname(__FILE__).'/classes/mvc.php');
require_once(dirname(__FILE__).'/classes/model.php');
require_once(dirname(__FILE__).'/classes/controller.php');
require_once(dirname(__FILE__).'/classes/view.php');

// Constant definitions
if(!defined('APP_MODELS')) {
	define('APP_MODELS', APP_PATH.'models/');
}

if(!defined('APP_CONTROLLERS')) {
	define('APP_CONTROLLERS', APP_PATH.'controllers/');
}

if(!defined('APP_VIEWS')) {
	define('APP_VIEWS',	APP_PATH.'views/');
}

?>
