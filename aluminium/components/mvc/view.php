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
	private $vars;

	/**
	 * The name of the theme, if using any. It is used to compose the path when looking for a template to load.
	 *
	 * @var string
	 */
	private $theme;

	/**
	 * The name of the template. It is used to compose the path when looking for a template to load.
	 *
	 * @var string
	 */
	private $template;

	/**
	 * View constructor.
	 *
	 * It tries to load the given template if not null.
	 *
	 * @param	mixed	$theme		The theme's name or null.
	 * @param	mixed	$template	The template's name or null.
	 */
	public function __construct($theme = null, $template = null) {
		$this->vars = array();
		$this->theme = $theme;
		$this->template = $template;

		// If the template is not null, try to load it
		if(!is_null($this->template)) {
			$this->load_template($template);
		}
	}

	/**
	 * Loads a template into the view.
	 *
	 * @param	string	$template	The template's name.
	 */
	public function load_template($template) {
		$template_name = '';

		if(!is_null($this->theme)) {
			$template_name .= $this->theme;
		}

		if(!is_null($template)) {
			$template_name .= $template;
		}

		if(!file_exists(APP_VIEWS.$this->template_name)) {
			die('Template not found:"'.$this->template_name.'".');
		}

		// Load the content
	}

}

?>
