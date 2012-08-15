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
