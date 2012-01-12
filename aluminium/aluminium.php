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

	/**
	 * Aluminium's constructor.
	 *
	 * @param	string	The app's root folder where the app is located, usually dirname(__FILE__).
	 */
	public function __construct($path) {
		// Define the app's full and relative paths
		define('APP_PATH',			$path.'/');
		define('APP_BASE_PATH',		basename($path).'/');

		// Define Aluminium's main paths
		define('ALUMINIUM_PATH',		dirname(__FILE__).'/');
		define('ALUMINIUM_CORE',		ALUMINIUM_PATH.'core/');
		define('ALUMINIUM_COMPONENTS',	ALUMINIUM_PATH.'components/');

		// The core classes are required
		require_once(ALUMINIUM_CORE.'component_loader.php');

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
