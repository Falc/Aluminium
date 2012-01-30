<?php
/**
 * This file contains Aluminium's main class.
 *
 * @author		Aitor García <aitor.falc@gmail.com>
 * @copyright	2012 Aitor García <aitor.falc@gmail.com>
 * @license		https://github.com/Falc/Aluminium/blob/master/LICENSE New BSD License
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

	public $components;
	public $main_conf;

	/**
	 * Aluminium's constructor.
	 *
	 * @param	string	$path	The app's root folder where the app is located, usually dirname(__FILE__).
	 */
	public function __construct($path) {
		// Define the app's full path
		define('APP_PATH',			$path.'/');
		define('APP_CONFIG',		APP_PATH.'config/');

		// Load app's main configuration
		$this->main_conf = require_once(APP_CONFIG.'main_conf.php');

		// Define the app's base_path
		$base_path = isset($this->main_conf['base_path']) ? $this->main_conf['base_path'] : '/';
		define('APP_BASE_PATH', $base_path);

		// Define Aluminium's main paths
		define('ALUMINIUM_PATH',		dirname(__FILE__).'/');
		define('ALUMINIUM_CORE',		ALUMINIUM_PATH.'core/');
		define('ALUMINIUM_COMPONENTS',	ALUMINIUM_PATH.'components/');

		// The core classes are required
		require_once(ALUMINIUM_CORE.'component_loader.php');
		require_once(ALUMINIUM_CORE.'functions.php');

		// Instance the component loader
		$this->components = new ComponentLoader();
	}

    /**
	 * This method starts Aluminium's processes.
	 */
	public function run() {
		echo '<p>Aluminium is working!</p>';
	}

}

?>
