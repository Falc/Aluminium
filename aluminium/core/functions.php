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
 * Returns the request uri.
 *
 * If the base_path is passed, it is removed from the request uri.
 *
 * @param	string	$base_path		App's base path.
 * @param	boolean	$trim_slashes	Defines whether the request uri should get the trailing slashes trimmed.
 * @return	string	The request uri.
 */
function get_request_uri($base_path = null, $trim_slashes = FALSE) {
	$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

	// If the app's base path is passed, remove it from $request_uri
	if(!empty($base_path)) {
		$request_uri = '/'.preg_replace('/^'.preg_quote($base_path, '/').'/', '', $request_uri);
	}

	// Remove the trailing slashes when required
	if($trim_slashes === TRUE) {
		$request_uri = rtrim($request_uri, '/');
	}

	// If the resulting request_uri is empty, use '/'
	if(empty($request_uri)) {
		$request_uri = '/';
	}

	return $request_uri;
}

?>
