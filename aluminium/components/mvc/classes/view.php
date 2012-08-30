<?php
/**
 * This file contains the View class.
 *
 * @author		Aitor García <aitor.falc@gmail.com>
 * @copyright	2012 Aitor García <aitor.falc@gmail.com>
 * @license		https://github.com/Falc/Aluminium/blob/master/LICENSE Simplified BSD License
 */

namespace Aluminium\Component\MVC;

/**
 * Views are responsible for displaying information.
 */
class View {

	/**
	 * Contains data that can be displayed in the view.
	 *
	 * @var array
	 */
	protected $_vars;

	/**
	 * The view file to load.
	 *
	 * @var string
	 */
	protected $_view_file;

	/**
	 * The content of the view after being built.
	 *
	 * @var string
	 */
	protected $_content;

	/**
	 * View constructor.
	 *
	 * @param	mixed	$view_file	The view file to load.
	 * @param	array	$conf		An array containing configuration options.
	 */
	public function __construct($view_file, $conf = null) {
		$this->_vars = array();
		$this->_content = null;

		if(!file_exists($view_file)) {
			trigger_error('View "'.$view_file.'" does not exist or cannot be loaded.', E_USER_ERROR);
		}

		// Load the configuration, if defined
		if(!empty($conf)) {
			// If $conf is an array, process it
			if(is_array($conf)) {
				$this->load_configuration($conf);
			}
			// If $conf is a string, assume it is a file
			elseif(is_string($conf)) {
				$this->load_configuration_from_file($conf);
			}
		}

		$this->_view_file = $view_file;
	}

	/**
	 * Magic get.
	 *
	 * @param	mixed	$name	Name of the variable to get.
	 * @return	mixed
	 */
	public function __get($name) {
		return $this->_vars[$name];
	}

	/**
	 * Magic set.
	 *
	 * Relies on the set() method.
	 *
	 * @param	mixed	$name	Name of the variable to set.
	 * @param	mixed	$value	Content.
	 */
	public function __set($name, $value) {
		$this->set($name, $value, FALSE);
	}

	/**
	 * Sets variables from an array.
	 *
	 * @param	array	$conf	An array containing the variables to set.
	 */
	public function load_configuration($conf) {
		foreach($conf as $index=>$var) {
			$this->set($index, $var);
		}
	}

	/**
	 * Sets variables from a configuration file.
	 *
	 * @param	string	$conf_file	Name of the configuration file.
	 */
	public function load_configuration_from_file($conf_file) {
		// If the specified file does not exist, stop the process
		if(!file_exists($conf_file)) {
			trigger_error('File '.$conf_file.' does not exist or cannot be loaded.', E_USER_ERROR);
		}

		// Load the configuration file and process its content
		$conf = include($conf_file);
		$this->load_configuration($conf);
	}

	/**
	 * Sets a variable or a View.
	 *
	 * If $filtered is set to TRUE, the content of the variable will be filtered.
	 * Views will NOT be filtered.
	 *
	 * @param	string	$name		Name of the variable to set.
	 * @param	mixed	$value		Content.
	 * @param	bool	$filtered	Determines whether $value should be filtered.
	 */
	public function set($name, $value, $filtered = FALSE) {
		if(!($value instanceof View)) {
			if($filtered === TRUE) {
				$value = htmlspecialchars($value);
			}
		}

		$this->_vars[$name] = $value;
	}

	/**
	 * Sets a variable or a View.
	 *
	 * This is a convenience method for set(), specifying $filtered as TRUE.
	 *
	 * @param	string	$name		Name of the variable to set.
	 * @param	mixed	$value		Content.
	 */
	public function set_filtered($name, $value) {
		$this->set($name, $value, TRUE);
	}

	/**
	 * Tells whether the view is built or not.
	 *
	 * @return	bool	TRUE if the view is built, else FALSE.
	 */
	public function is_built() {
		return !is_null($this->get_content());
	}

	/**
	 * Gets the view content.
	 *
	 * @return	string|null	The content of the view. Returns null if the view is not built.
	 */
	public function get_content() {
		return $this->_content;
	}

	/**
	 * Builds the view pushing the vars into the specified view.
	 *
	 * This method does not display the view, just builds it and stores the content into $_content for
	 * reusing it later, when desired.
	 */
	public function build() {
		// Process the _vars array
		foreach($this->_vars as $name => $value) {
			// If the var is a View, build it
			if($value instanceof View) {
				$value->build();
				$$name = $value->get_content();
			}
			else {
				$$name = $value;
			}
		}

		ob_start();
		include($this->_view_file);
		$this->_content = ob_get_clean();
	}

	/**
	 * Renders the view.
	 */
	public function render() {
		// Build the view if not built yet
		if(is_null($this->_content)) {
			$this->build();
		}

		echo $this->get_content();
	}

}
?>
