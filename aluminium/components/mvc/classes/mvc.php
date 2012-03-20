<?php
/**
 * This file contains the MVC component class.
 *
 * @author		Aitor García <aitor.falc@gmail.com>
 * @copyright	2012 Aitor García <aitor.falc@gmail.com>
 * @license		https://github.com/Falc/Aluminium/blob/master/LICENSE Simplified BSD License
 * @package		Aluminium
 * @subpackage	Components
 */

/**
 * The MVC component enables the use of the Model View Controller pattern in the app.
 *
 * @package		Aluminium
 * @subpackage	Components
 */
class MVC {
	/**
	 * MVC constructor.
	 */
	public function __construct() {
	}

	/**
	 * Creates a new controller instance specified by $name and sets its $parameters.
	 *
	 * Since Controller is an abstract class, $name should refer to a class that extends it.
	 *
	 * @param	string	$controller_name	The name of the controller to load.
	 * @param	mixed	$parameters			A list of parameters or null if any.
	 */
	public function load_controller($controller_name, $parameters = null) {
		$class_file = APP_CONTROLLERS.$controller_name.'_controller.php';

		// If the class file does not exist, stop the process
		if(!file_exists($class_file)) {
			trigger_error('File '.$class_file.' does not exist or cannot be loaded.', E_USER_ERROR);
		}

		// Include the controller class file
		require_once($class_file);

		// Remove underscores, file names use them, class names don't
		$class = str_replace('_', '', $controller_name);

		// Add the suffix controller to the class name
		$class .= 'Controller';

		// If the class does not exist, stop the process
		if(!class_exists($class, FALSE)) {
			trigger_error('Class '.$class.' is not defined.', E_USER_ERROR);
		}

		// Create the controller instance and set the parameters
		$controller = new $class();
		$controller->set_parameters($parameters);

		return $controller;
	}

}

?>
