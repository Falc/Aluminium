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
	 * The relative path from the controllers root.
	 *
	 * Examples:
	 * null		=> APP_CONTROLLERS/controller_name.php
	 * 'admin'	=> APP_CONTROLLERS/admin/controller_name.php
	 * 'aa/bb'	=> APP_CONTROLLERS/aa/bb/controller_name.php
	 *
	 * @var string|null
	 */
	private $controller_subdir = null;

	/**
	 * MVC constructor.
	 */
	public function __construct() {
	}

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
	 * Looks for the specified controller and loads it, if found.
	 *
	 * @param	string	$controller_name	The name of the controller to load.
	 * @return	boolean						TRUE if successfully loaded, else FALSE.
	 */
	public function load_controller($controller_name) {
		$class_file = APP_CONTROLLERS.$this->controller_subdir.$controller_name.'_controller.php';

		// If the class file does not exist, stop the process
		if(!file_exists($class_file)) {
			return FALSE;
		}

		// Include the controller class file
		require_once($class_file);

		// Remove underscores, file names use them, class names don't
		$class = str_replace('_', '', $controller_name);

		// Add the suffix controller to the class name
		$class .= 'Controller';

		// If the class does not exist, stop the process
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
	public function instance_controller($controller_name, $parameters = null) {
		$class_file = APP_CONTROLLERS.$this->controller_subdir.$controller_name.'_controller.php';
		$class = str_replace('_', '', $controller_name);
		$class .= 'Controller';

		// If the controller class does not exist, try to load it
		if(!class_exists($class, FALSE)) {
			$controller_loaded = $this->load_controller($controller_name);

			// If the controller cannot be loaded, stop the process and display an error
			if(!$controller_loaded) {
				if(!file_exists($class_file)) {
					trigger_error('File "'.$class_file.'" does not exist or cannot be loaded.', E_USER_ERROR);
				}
				else {
					trigger_error('Class "'.$class.'" not found.', E_USER_ERROR);
				}
			}
		}

		// Create the controller instance and set the parameters
		$controller = new $class();
		$controller->set_parameters($parameters);

		return $controller;
	}

}

?>
