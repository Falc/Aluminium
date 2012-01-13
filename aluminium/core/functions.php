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
 * Triggers an event.
 *
 * This is a core function that acts as a proxy for ComponentLoader->event() method.
 * Core functions can be called from anywhere.
 *
 * @param	string	Event name.
 */
function event($event_name) {
	global $app;
	$app->components->event($event_name);
}

/**
 * Loads the specified components.
 *
 * This is a core function that acts as a proxy for ComponentLoader->load() method.
 * Core functions can be called from anywhere.
 *
 * @param	mixex	Name of the components to load (string for a single component).
 */
function load_component($component) {
	global $app;
	$app->components->load($component);
}

/**
 * Gets a component instance.
 *
 * This is a core function that acts as a proxy for ComponentLoader->get() method.
 * Core functions can be called from anywhere.
 *
 * @param	string	Name of the component to get.
 * @return	mixed	A component instance.
 */
function get_component($component) {
	global $app;
	return $app->components->get($component);
}

?>
