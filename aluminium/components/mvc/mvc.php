<?php
/**
 * This file contains the MVC component class.
 *
 * @author		Aitor García <aitor.falc@gmail.com>
 * @copyright	2012 Aitor García <aitor.falc@gmail.com>
 * @license		https://github.com/Falc/Aluminium/blob/master/LICENSE New BSD License
 * @package		Aluminium
 * @subpackage	Components
 */

/**
 * The MVC component enables the use of the Model View Controller pattern in the app.
 *
 * This component includes a Router class for managing the url routing process.
 *
 * @package		Aluminium
 * @subpackage	Components
 */
class MVC {
	/**
	 * A Router instance.
	 */
	private $router;

	/**
	 * MVC constructor.
	 *
	 * Defines some constants and loads the required configuration.
	 */
	public function __construct() {
		if(!defined('ALUMINIUM_MVC')) {
			define('APP_MODELS',		APP_PATH.'models/');
			define('APP_CONTROLLERS',	APP_PATH.'controllers/');
			define('APP_VIEWS',			APP_PATH.'views/');

			define('ALUMINIUM_MVC',		TRUE);
		}
	}

}

?>
