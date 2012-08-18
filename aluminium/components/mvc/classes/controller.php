<?php
/**
 * This file contains the Controller class.
 *
 * @author		Aitor García <aitor.falc@gmail.com>
 * @copyright	2012 Aitor García <aitor.falc@gmail.com>
 * @license		https://github.com/Falc/Aluminium/blob/master/LICENSE Simplified BSD License
 */

namespace Aluminium\Component\MVC;

/**
 * The parent class for controllers.
 */
abstract class Controller {

	/**
	 * An array containing parameters defined by the user.
	 *
	 * @var array
	 */
	protected $parameters = array();

	/**
	 * An array containing dependencies.
	 *
	 * @var array
	 */
	protected $dependencies = array();

	/**
	 * Gets a dependency.
	 *
	 * @ignore
	 */
	public function __get($name) {
		return $this->dependencies[$name];
	}

	/**
	 * Sets a dependency.
	 *
	 * @ignore
	 */
	public function __set($name, $value) {
		$this->dependencies[$name] = $value;
	}

	/**
	 * Sets a parameter.
	 *
	 * @param	string	$name	The parameter name.
	 * @param	mixed	$value	The parameter value.
	 */
	public function set_parameter($name, $value) {
		$this->parameters[$name] = $value;
	}

	/**
	 * Sets a list of parameters.
	 *
	 * @param	array	$parameters	An associative array containing parameters.
	 */
	public function set_parameters($parameters) {
		if(empty($parameters)) {
			return;
		}

		foreach($parameters as $parameter=>$value) {
			$this->set_parameter($parameter, $value);
		}
	}

	/**
	 * The default action.
	 */
	abstract public function index();

	/**
	 * Loads a model.
	 *
	 * @param	string	$model_name	The name of the model to load.
	 */
	private function load_model($model_name) {
		$model_file = MODELS_PATH.$model_name.'_model.php';
		require_once($model_file);
	}

	/**
	 * Instances a model.
	 *
	 * @param	string	$model_name	The name of the model to load.
	 */
	private function instance_model($model_name) {
		// Load the model
		$this->load_model($model_name);

		// Class names don't use underscores, so remove them and add the 'Model' suffix
		$class = str_replace('_', '', $model_name);
		$class .= 'Model';

		// If the class does not exist, display an error
		if(!class_exists($class, FALSE)) {
			$message = 'Class "'.$class.'" not found.';
			trigger_error($message, E_USER_ERROR);
		}

		return new $class();
	}

}
?>
