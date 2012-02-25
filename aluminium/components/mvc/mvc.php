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

		// Include the class files needed by the component
		require_once(ALUMINIUM_COMPONENTS.'mvc/classes/model.php');
		require_once(ALUMINIUM_COMPONENTS.'mvc/classes/view.php');
		require_once(ALUMINIUM_COMPONENTS.'mvc/classes/controller.php');
	}

	/**
	 * Creates a new controller instance specified by $name and sets its $parameters.
	 *
	 * Since Controller is an abstract class, $name should refer to a class that extends it.
	 *
	 * @param	string	$name		The name of the class to instance.
	 * @param	mixed	$parameters	A list of parameters or null if any.
	 */
	public function load_controller($class, $parameters) {
		$class_file = APP_CONTROLLERS.$class.'.php';

		// If the class file does not exist, stop the process
		if(!file_exists($class_file)) {
			die('Error: File "'.$class_file.'" not found.');
		}

		// Include the controller class file
		require_once($class_file);

		// If the class does not exist, stop the process
		if(!class_exists($class, FALSE)) {
			die('Error: Class "'.$class.'" not found.');
		}

		// Create the controller instance and set the parameters
		$controller = new $class();
		$controller->set_parameters($parameters);

		return $controller;
	}

}

?>
