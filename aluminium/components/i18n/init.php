<?php

// Required files
require_once(dirname(__FILE__).'/classes/i18n.php');
require_once(dirname(__FILE__).'/classes/drivers/translate_driver.php');

// Constant definitions
// APP_PATH (the root directory) must be defined
if(!defined('APP_LANG')) {
	define('APP_LANG', APP_PATH.'lang/');
}

?>