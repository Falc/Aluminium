<?php
/**
 * This file contains the View class.
 *
 * @author		Aitor García <aitor.falc@gmail.com>
 * @copyright	2012 Aitor García <aitor.falc@gmail.com>
 * @license		https://github.com/Falc/Aluminium/blob/master/LICENSE Simplified BSD License
 * @package		Aluminium
 * @subpackage	Components
 */

/**
 * The View class allows to load templates and bind data from a controller.
 *
 * @package		Aluminium
 * @subpackage	Components
 */
class View {
	/**
	 * Contains data that can be displayed in the view.
	 *
	 * @var array
	 */
	protected $_vars;

	/**
	 * The name of the template to load.
	 *
	 * @var string
	 */
	protected $_template;

	/**
	 * The content of the view after being built.
	 *
	 * @var string
	 */
	protected $_content;

	/**
	 * View constructor.
	 *
	 * @param	mixed	$template	Name of the template to load.
	 */
	public function __construct($template) {
		$this->_vars = array();
		$this->_content = null;

		$template_file = APP_VIEWS.$template.'.php';

		if(!file_exists($template_file)) {
			trigger_error('Template '.$template_file.' does not exist or cannot be loaded.', E_USER_ERROR);
		}

		$this->_template = $template;
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
	 * Relies on the load() method.
	 *
	 * @param	mixed	$name	Name of the variable to load.
	 * @param	mixed	$value	Content.
	 */
	public function __set($name, $value) {
		$this->load($name, $value, FALSE);
	}

	/**
	 * Loads a variable or a View.
	 *
	 * If $filtered is set to TRUE, the content of the variable will be filtered.
	 * Views will NOT be filtered.
	 *
	 * @param	mixed	$name		Name of the variable to load.
	 * @param	mixed	$value		Content.
	 * @param	bool	$filtered	Determines whether $value should be filtered.
	 */
	public function load($name, $value, $filtered = FALSE) {
		if(!($value instanceof View)) {
			if($filtered === TRUE) {
				$value = htmlspecialchars($value);
			}
		}

		$this->_vars[$name] = $value;
	}

	/**
	 * Loads a variable or a View.
	 *
	 * This is a convenience method for load(), specifying $filtered as TRUE.
	 *
	 * @param	mixed	$name		Name of the variable to load.
	 * @param	mixed	$value		Content.
	 */
	public function load_filtered($name, $value) {
		$this->load($name, $value, TRUE);
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
	 * Builds the view pushing the vars into the specified template.
	 *
	 * This method does not display the view, just builds it and stores the content into $_content for
	 * reusing it later, when desired.
	 */
	public function build() {
		$template_file = APP_VIEWS.$this->_template.'.php';

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
		include($template_file);
		$this->_content = ob_get_clean();
	}

	/**
	 * Displays the view.
	 */
	public function display() {
		// Build the view if not built yet
		if(is_null($this->_content)) {
			$this->build();
		}

		echo $this->_content;
	}
}

?>
