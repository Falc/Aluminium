<?php
/**
 * This file contains Aluminium's main class.
 *
 * @author		Aitor García <aitor.falc@gmail.com>
 * @copyright	2012 Aitor García <aitor.falc@gmail.com>
 * @license		https://github.com/Falc/Aluminium/blob/master/LICENSE Simplified BSD License
 * @package		Aluminium
 * @subpackage	Core
 */

/**
 * Main class.
 *
 * @package		Aluminium
 * @subpackage	Core
 */
class Aluminium {

	public $conf;

	/**
	 * Aluminium's constructor.
	 *
	 * @param	string	$path	The app's root folder where the app is located, usually dirname(__FILE__).
	 */
	public function __construct($path) {
		// Define Aluminium's main paths
		define('ALUMINIUM_PATH',		dirname(__FILE__).'/');
		define('ALUMINIUM_CORE',		ALUMINIUM_PATH.'core/');
		define('ALUMINIUM_COMPONENTS',	ALUMINIUM_PATH.'components/');

		// Define the app's main paths
		define('APP_PATH',		$path.'/');
		define('APP_CONF',		APP_PATH.'conf/');
		define('APP_LOGS',		APP_PATH.'logs/');

		// The core classes are required
		require_once(ALUMINIUM_CORE.'functions.php');

		// Set a default timezone
		date_default_timezone_set('UTC');

		// Load app's main configuration
		$this->conf = require_once(APP_CONF.'main_conf.php');

		// Default values for $debug_mode and $base_path
		$debug_mode = FALSE;
		$base_path = '/';

		// Process the configuration options
		foreach($this->conf as $key=>$value) {
			switch($key) {
				// Set the base path
				case 'base_path':
					$base_path = !empty($value) ? $value : '/';
					define('APP_BASE_PATH', $base_path);
					break;
				// Set the debug mode
				case 'debug_mode':
					$debug_mode = ($value === TRUE);
					define('DEBUG_MODE', $debug_mode);
					break;
				// Set the app name, if defined
				case 'name':
					$name = !empty($value) ? $value : 'Untitled';
					define('APP_NAME', $name);
					break;
				// Every other configuration option will be defined as constant too
				default:
					define(strtoupper($key), $value);
					break;
			}
		}

		// Start the debug mode, if required
		if(DEBUG_MODE === TRUE) {
			error_reporting(E_ALL);
			ini_set('display_errors', 1);

			if(!defined('DEBUG_FILE')) {
				$tmpfile = stream_get_meta_data(tmpfile());
				define('DEBUG_FILE', $tmpfile['uri']);

				// Create the file by writing something in it
				file_put_contents(DEBUG_FILE, "ALUMINIUM DEBUG FILE\n", FILE_APPEND);
			}
		}
	}
}

?>
