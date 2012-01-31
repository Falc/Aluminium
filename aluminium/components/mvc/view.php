<?php
/**
 * This file contains the View class.
 *
 * @author		Aitor García <aitor.falc@gmail.com>
 * @copyright	2012 Aitor García <aitor.falc@gmail.com>
 * @license		https://github.com/Falc/Aluminium/blob/master/LICENSE New BSD License
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
	private $_vars;

	/**
	 * The name of the theme, if using any. It is used to compose the path when looking for a template to load.
	 *
	 * @var string
	 */
	private $_theme;

	/**
	 * The name of the template. It is used to compose the path when looking for a template to load.
	 *
	 * @var string
	 */
	private $_template;

	/**
	 * The content of the view after being built.
	 *
	 * @var string
	 */
	private $_content;

	/**
	 * View constructor.
	 *
	 * @param	mixed	$template	The template's name or null.
	 * @param	mixed	$theme		The theme's name or null.
	 */
	public function __construct($template = null, $theme = null) {
		$this->_vars = array();
		$this->_theme = $theme;
		$this->_template = $template;
		$this->_content = null;
	}

	/**
	 * Magic get.
	 *
	 * @param	int		$index	Index of the element in the array.
	 * @return	mixed
	 */
	public function __get($index) {
		return $this->_vars[$index];
	}

	/**
	 * Magic set.
	 *
	 * @param	int		$index	Index of the element in the array.
	 * @param	mixed	$value	Data to store in the array.
	 */
	public function __set($index, $value) {
		$this->_vars[$index] = $value;
	}

	/**
	 * Builds the view pushing the vars into the specified template.
	 *
	 * This method does not display the view, just builds it and stores the content into the $_content
	 * property for reusing it later, when desired.
	 */
	public function build() {
		if(is_null($this->_template)) {
			die('Template not asigned.');
		}

		$template_name = !is_null($this->_theme) ? '' : $this->_theme;
		$template_name .= $this->_template.'.php';

		if(!file_exists(APP_VIEWS.$template_name)) {
			die('Template not found: "'.$template_name.'".');
		}

		foreach ($this->_vars as $name => $value) {
			$$name = $value;
		}

		ob_start();
		include(APP_VIEWS.$template_name);
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

	/**
	 * Gets the view content.
	 *
	 * @return	The content of the view after being built.
	 */
	public function get_content() {
		return $this->_content;
	}

}

?>
