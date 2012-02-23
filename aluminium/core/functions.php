<?php
/**
 * This file contains the core functions.
 *
 * @author		Aitor García <aitor.falc@gmail.com>
 * @copyright	2012 Aitor García <aitor.falc@gmail.com>
 * @license		https://github.com/Falc/Aluminium/blob/master/LICENSE New BSD License
 * @package		Aluminium
 * @subpackage	Core
 */

/**
 * Returns an instance of the specified component.
 *
 * This method looks for the $component. If the $class_file exists, it is included and an instance is created and returned.
 *
 * @param	string	$component	Name of the component to load.
 * @return	mixed	A component instance.
 */
function load_component($component) {
	// If the class is not already included, do it
	if(!class_exists($component, FALSE)) {
		$class_file = ALUMINIUM_COMPONENTS.$component.'/'.$component.'.php';

		// If the class file does not exist, stop the process
		if(!file_exists($class_file)) {
			die('Error: The component "'.$component.'" can not be found.');
		}

		require($class_file);
	}

	return new $component();
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

function start_debug_mode() {
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}

?>
