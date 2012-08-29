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
abstract class Application {

	/**
	 * Configuration options.
	 *
	 * @var array
	 */
	private $conf = array(
		'base_path' => '/',
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

		// Set a default timezone
		date_default_timezone_set('UTC');
	}

	/**
	 * Initializes the application.
	 */
	final public function run() {
		// Run the application configuration() method.
		$this->configuration();

		// Set the app base path
		$base_path = !empty($this->conf['base_path']) ?
			$this->conf['base_path'] :
			'/';

		if(!defined('BASE_PATH')) {
			define('BASE_PATH', $base_path);
		}

		// Set the debug mode
		$debug_mode = !empty($this->conf['debug_mode']) ?
			($this->conf['debug_mode'] === TRUE) :
			'FALSE';

		if(!defined('DEBUG_MODE')) {
			define('DEBUG_MODE', $debug_mode);
		}

		// Get the requested uri
		$requested_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

		// If a base path other than '/' is defined, remove it from the requested uri
		if(BASE_PATH !== '/') {
			$requested_uri = '/'.preg_replace('/^'.preg_quote(BASE_PATH, '/').'/', '', $requested_uri);

			// Remove the trailing slash
			$requested_uri = rtrim($requested_uri, '/');

			// If the resulting requested uri is empty, use '/'
			if(empty($requested_uri)) {
				$requested_uri = '/';
			}
		}

		// The requested uri can be used from everywhere
		define('REQUESTED_URI', $requested_uri);

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

		// Init the application
		$this->init();
	}

	/**
	 * The configuration method is called before init() and should be used to set up the application.
	 */
	protected abstract function configuration();

	/**
	 * Inits the application.
	 */
	protected abstract function init();

	/**
	 * Loads a component.
	 *
	 * This function searches and processes the component's init.php file.
	 *
	 * @param	string	$component	Name of the component to load.
	 */
	protected function load_component($component) {
		$init_file = ALUMINIUM_COMPONENTS.$component.'/init.php';
		require_once($init_file);
	}

	/**
	 * Loads the specified components.
	 *
	 * This function relies on load_component().
	 *
	 * @param	array	$components	An array containing a list of components to load.
	 */
	protected function load_components(array $components) {
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
	protected function instance_component($component) {
		$instance_file = ALUMINIUM_COMPONENTS.$component.'/instance.php';
		$instance = require_once($instance_file);
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
	protected function instance_components(array $components) {
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

		$name = strtoupper($option);
		if(!defined($name)) {
			define($name, $value);
		}
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
