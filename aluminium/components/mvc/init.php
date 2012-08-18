<?php

// Required files
require_once(dirname(__FILE__).'/classes/mvc.php');
require_once(dirname(__FILE__).'/classes/model.php');
require_once(dirname(__FILE__).'/classes/controller.php');
require_once(dirname(__FILE__).'/classes/view.php');

// Constant definitions
// APP_PATH (the root directory) must be defined
if(!defined('MODELS_PATH')) {
	define('MODELS_PATH', APP_PATH.'models/');
}

if(!defined('CONTROLLERS_PATH')) {
	define('CONTROLLERS_PATH', APP_PATH.'controllers/');
}

if(!defined('VIEWS_PATH')) {
	define('VIEWS_PATH',	APP_PATH.'views/');
}

?>
