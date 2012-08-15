<?php
/**
 * This file contains Aluminium's main class.
 *
 * @author		Aitor García <aitor.falc@gmail.com>
 * @copyright	2012 Aitor García <aitor.falc@gmail.com>
 * @license		https://github.com/Falc/Aluminium/blob/master/LICENSE Simplified BSD License
 */
namespace Aluminium;

/**
 * Main class.
 */
class Application {
	/**
	 * Configuration options.
	 *
	 * @var array
	 */
	private $conf = array(
		'app_base_path' => '/',
		'debug_mode'	=> FALSE
	);

	/**
	 * Constructor.
	 *
	 * Defines some constants and loads the required files.
	 */
	public function __construct() {
		// Define Aluminium's main paths
		if(!defined('ALUMINIUM_PATH')) {
			define('ALUMINIUM_PATH',		dirname(__FILE__).'/');
		}

		define('ALUMINIUM_CORE',		ALUMINIUM_PATH.'core/');
		define('ALUMINIUM_COMPONENTS',	ALUMINIUM_PATH.'components/');

		// The core classes are required
		require_once(ALUMINIUM_CORE.'functions.php');

		// Set a default timezone
		date_default_timezone_set('UTC');
	}

	/**
	 * Initializes the application.
	 */
	protected function init() {
		// Process the configuration options
		foreach($this->conf as $option=>$value) {
			switch($option) {
				// Set the base path
				case 'app_base_path':
					$app_base_path = !empty($value) ? $value : '/';
					define('APP_BASE_PATH', $app_base_path);
					break;
				// Set the debug mode
				case 'debug_mode':
					$debug_mode = ($value === TRUE);
					define('DEBUG_MODE', $debug_mode);
					break;
				// Every other configuration option will be defined as constant too
				default:
					define(strtoupper($option), $value);
					break;
			}
		}

		// Start the debug mode, if required
		if(DEBUG_MODE === TRUE) {
			error_reporting(E_ALL);
			ini_set('display_errors', 1);

			if(!defined('DEBUG_FILE')) {
				$tmpfile = stream_get_meta_data(tmpfile());
				define('DEBUG_FILE', $tmpfile['uri']);

				// Create the file by writing something in it
				file_put_contents(DEBUG_FILE, "ALUMINIUM DEBUG FILE\n", FILE_APPEND);
			}
		}
	}

	/**
	 * Loads a component.
	 *
	 * This function searches and processes the component's init.php file.
	 *
	 * @param	string	$component	Name of the component to load.
	 */
	function load_component($component) {
		$init_file = ALUMINIUM_COMPONENTS.$component.'/init.php';

		// If the init file does not exist, stop the process
		if(!file_exists($init_file)) {
			trigger_error('File "'.$init_file.'" does not exist or cannot be loaded.', E_USER_ERROR);
		}

		include($init_file);
	}

	/**
	 * Loads the specified components.
	 *
	 * This function relies on load_component().
	 *
	 * @param	array	$components	An array containing a list of components to load.
	 */
	function load_components(array $components) {
		foreach($components as $component) {
			load_component($component);
		}
	}

	/**
	 * Returns a component instance.
	 *
	 * This function searches and processes the component's instance.php file.
	 *
	 * @param	string	$component Name of the component to instance.
	 * @return	mixed	A component instance.
	 */
	function instance_component($component) {
		$instance_file = ALUMINIUM_COMPONENTS.$component.'/instance.php';

		// If the instance file does not exist, stop the process
		if(!file_exists($instance_file)) {
			trigger_error('File "'.$instance_file.'" does not exist or cannot be loaded.', E_USER_ERROR);
		}

		$instance = include($instance_file);

		return $instance;
	}

	/**
	 * Returns an array containing instances of the specified components.
	 *
	 * This function relies on instance_component() to instance every component in $components.
	 *
	 * @param	array	$components	An array containing a list of components to instance.
	 * @return	array	An array containing instances of components.
	 */
	function instance_components(array $components) {
		$instances = array();

		foreach($components as $component) {
			$instances[] = instance_component($component);
		}

		return $instances;
	}

	/**
	 * Gets a configuration option.
	 *
	 * @param	string	$option	The configuration option to get.
	 * @return	mixed			The configuration option value.
	 */
	protected function get_conf_option($option) {
		return $this->conf[$option];
	}

	/**
	 * Sets a configuration option.
	 *
	 * @param	string	$option	The configuration option to set.
	 * @param	mixed	$value	The configuration option value.
	 */
	protected function set_conf_option($option, $value) {
		$this->conf[$option] = $value;
	}

	/**
	 * Sets a list of configuration options.
	 *
	 * @param	array	$options	An associative array containing configuration options.
	 */
	protected function set_conf_options($options) {
		foreach($options as $option=>$value) {
			$this->set_conf_option($option, $value);
		}
	}

	/**
	 * Sets configuration options from a file.
	 *
	 * @param	string	$file	A file that returns an associative array containing configuration options.
	 */
	protected function set_conf_options_from_file($file) {
		$options = require($file);
		$this->set_conf_options($options);
	}
}

?>
