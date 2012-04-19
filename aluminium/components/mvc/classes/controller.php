<?php
/**
 * This file contains the Controller class.
 *
 * @author		Aitor García <aitor.falc@gmail.com>
 * @copyright	2012 Aitor García <aitor.falc@gmail.com>
 * @license		https://github.com/Falc/Aluminium/blob/master/LICENSE Simplified BSD License
 * @package		Aluminium
 * @subpackage	Components
 */

/**
 * The parent class for controllers.
 *
 * @package		Aluminium
 * @subpackage	Components
 */
abstract class Controller {
	/**
	 * A list of parameters defined by the user.
	 *
	 * @var array
	 */
	protected $parameters;

	/**
	 * Controller constructor.
	 */
	public function __construct() {
	}

	/**
	 * Sets $parameters.
	 *
	 * @param	array	$parameters	A list of parameters defined by the user.
	 */
	public function set_parameters($parameters = array()) {
		$this->parameters = (array) $parameters;
	}

	/**
	 * The default action.
	 */
	abstract public function index();

}

?>
