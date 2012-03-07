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
 * Returns a component instance.
 *
 * This method searches and processes the component's init.php file. An object instance could be returned.
 *
 * @param	string	$component	Name of the component to load.
 * @return	mixed	A component instance or null.
 */
function load_component($component) {
	$init_file = ALUMINIUM_COMPONENTS.$component.'/init.php';

	// If the init file does not exist, stop the process
	if(!file_exists($init_file)) {
		die('Error: File "'.ALUMINIUM_COMPONENTS.$component.'/init.php" does not exist or cannot be loaded.');
	}

	// Include the init file
	$instance = include($init_file);

	// If the init file returned an object, return it
	if(is_object($instance)) {
		return $instance;
	}

	// Else, return null
	return null;
}

/**
 * Returns an array containing instances of the specified components.
 *
 * This method relies on load_component($component) to instance every component in $components.
 *
 * @param	array	$components	An array containing a list of components to load.
 * @return	array	An array containing instances of components.
 */
function load_components(array $components) {
	$instances = array();

	foreach($components as $component) {
		$instances[] = load_component($component);
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
		die('Error: "'.$log_directory.'" is not writable.');
	}

	// Check whether an existent file is writable
	if(file_exists($log_file) && !is_writable($log_file)) {
		die('Error: "'.$log_filename.'.log" is not writable.');
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
