<?php
/**
 * This file contains the MVC component class.
 *
 * @author		Aitor García <aitor.falc@gmail.com>
 * @copyright	2012 Aitor García <aitor.falc@gmail.com>
 * @license		https://github.com/Falc/Aluminium/blob/master/LICENSE Simplified BSD License
 */

namespace Aluminium\Component\MVC;

/**
 * The MVC component enables the use of the Model View Controller pattern in the app.
 */
class MVC {

	/**
	 * The relative path from the controllers root.
	 *
	 * Examples:
	 * null		=> CONTROLLERS_PATH/controller_name.php
	 * 'admin'	=> CONTROLLERS_PATH/admin/controller_name.php
	 * 'aa/bb'	=> CONTROLLERS_PATH/aa/bb/controller_name.php
	 *
	 * @var string|null
	 */
	private $controller_subdir = null;

	/**
	 * Sets the controllers subdirectory.
	 *
	 * @param	string|null	$controller_subdir	The relative path from the controllers root.
	 */
	public function set_controller_subdir($controller_subdir) {
		// Use null as the default "empty" value
		if(empty($controller_subdir)) {
			$this->controller_subdir = null;
			return;
		}

		$controller_subdir = trim($controller_subdir);

		// Add a trailing slash if needed
		if(substr($controller_subdir, -1) !== '/') {
			$controller_subdir .= '/';
		}

		$this->controller_subdir = $controller_subdir;
	}

	/**
	 * Checks whether a controller is loadable.
	 *
	 * @param	string	$controller_name	The controller name.
	 * @return	boolean						TRUE if it can be loaded, else FALSE.
	 */
	public function controller_is_loadable($controller_name) {
		// Build the class file
		$class_file = CONTROLLERS_PATH.$this->controller_subdir.$controller_name.'_controller.php';

		// If the class file does not exist, return FALSE
		if(!file_exists($class_file)) {
			return FALSE;
		}

		// Include the controller class file
		require_once($class_file);

		// Class names don't use underscores, so remove them and add the 'Controller' suffix
		$class = str_replace('_', '', $controller_name);
		$class .= 'Controller';

		// If the class does not exist, return FALSE
		if(!class_exists($class, FALSE)) {
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Creates a new controller instance specified by $controller_name and sets its $parameters.
	 *
	 * Since Controller is an abstract class, $name should refer to a class that extends it.
	 *
	 * @param	string	$controller_name	The name of the controller to instance.
	 * @param	mixed	$parameters			A list of parameters or null if any.
	 */
	public function load_controller($controller_name, $parameters = null) {
		// Build the class file
		$class_file = CONTROLLERS_PATH.$this->controller_subdir.$controller_name.'_controller.php';

		// Class names don't use underscores, so remove them and add the 'Controller' suffix
		$class = str_replace('_', '', $controller_name);
		$class .= 'Controller';

		// If the controller cannot be loaded, stop the process and display an error
		if(!$this->controller_is_loadable($controller_name)) {
			if(!file_exists($class_file)) {
				$message = 'File "'.$class_file.'" does not exist or cannot be loaded.';
			}
			else {
				$message = 'Class "'.$class.'" not found.';
			}

			trigger_error($message, E_USER_ERROR);
		}

		// Create the controller instance and set the parameters
		$controller = new $class();
		$controller->set_parameters($parameters);

		return $controller;
	}

}
?>
