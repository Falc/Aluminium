<?php
/**
 * This file contains the core functions.
 *
 * @author		Aitor García <aitor.falc@gmail.com>
 * @copyright	2012 Aitor García <aitor.falc@gmail.com>
 * @license		https://github.com/Falc/Aluminium/blob/master/LICENSE Simplified BSD License
 * @package		Aluminium
 * @subpackage	Core
 */

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
 * Starts the debug mode.
 */
function start_debug_mode() {
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}

/**
 * Appends a message to the specified log file.
 *
 * The default log directory is "{APP_PATH}/logs/" but it can be customized through $log_directory.
 * When the log filename is not specified, the default one "others.log" will be used.
 * If the log file doesn't exist, it will be created.
 *
 * write_log($message, 'somefile')	-> Logs to {APP_PATH}/logs/somefile.log
 * write_log($message)				-> Logs to {APP_PATH}/logs/others.log
 *
 * @param	string	$message		The message to log.
 * @param	string	$log_filename	Name of the log file.
 * @param	string	$log_directory	Name of the directory.
 */
function write_log($message, $log_filename = 'others', $log_directory = APP_LOGS) {
	$log_file = $log_directory.$log_filename.'.log';

	// Check whether the directory is writable
	if(!is_writable($log_directory)) {
		trigger_error('Directory "'.$log_directory.'" does not exist or is not writable.', E_USER_ERROR);
	}

	// Check whether an existent file is writable
	if(file_exists($log_file) && !is_writable($log_file)) {
		trigger_error('File "'.$log_filename.'.log" does not exist or is not writable.', E_USER_ERROR);
	}

	$output = "\n";
	$output .= date('M d H:i:s ').substr(microtime(), 0, 5).' | ';
	$output .= $message;

	// Append the log message
	file_put_contents($log_file, $output, FILE_APPEND);
}

/**
 * Appends a message to the specified log file only if the DEBUG_MODE is enabled.
 *
 * This is a simple helper function that saves a DEBUG_MODE check.
 *
 * @param	string	$message		The message to log.
 * @param	string	$log_filename	Name of the log file.
 * @param	string	$log_directory	Name of the directory.
 */
function write_debug_log($message, $log_filename = 'others', $log_directory = APP_LOGS) {
	if(DEBUG_MODE === TRUE) {
		write_log($message, $log_filename, $log_directory);
	}
}

?>
