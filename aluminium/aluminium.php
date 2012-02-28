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

	public $main_conf;

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

		// Define the app's full path
		define('APP_PATH',		$path.'/');
		define('APP_CONF',		APP_PATH.'conf/');
		define('APP_LOGS',		APP_PATH.'logs/');

		// The core classes are required
		require_once(ALUMINIUM_CORE.'functions.php');

		// Set a default timezone
		date_default_timezone_set('UTC');

		// Load app's main configuration
		$this->main_conf = require_once(APP_CONF.'main_conf.php');

		// Check the debug mode configuration option and start it if required
		$debug_mode = ($this->main_conf['debug_mode'] === TRUE);
		define('DEBUG_MODE', $debug_mode);

		if($debug_mode === TRUE) {
			start_debug_mode();
		}

		// Define the app's base_path
		$base_path = isset($this->main_conf['base_path']) ? $this->main_conf['base_path'] : '/';
		define('APP_BASE_PATH', $base_path);
	}

    /**
	 * This method starts Aluminium's processes.
	 */
	public function run() {
		echo '<p>Aluminium is working!</p>';
	}

}

?>
