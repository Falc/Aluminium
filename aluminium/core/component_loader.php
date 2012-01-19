<?php
/**
 * This file contains the component loader.
 *
 * @author		Aitor García <aitor.falc@gmail.com>
 * @copyright	2012 Aitor García <aitor.falc@gmail.com>
 * @license		https://github.com/Falc/Aluminium/blob/master/LICENSE New BSD License
 * @package		Aluminium
 * @subpackage	Core
 */

/**
 * The component loader allows to load and unload the needed components.
 *
 * @package		Aluminium
 * @subpackage	Core
 */
class ComponentLoader {

	private $loaded_components = array();

	/**
	 * Loads the specified components.
	 *
	 * @param	mixed	$components	Name of the components to load (string for a single component).
	 */
	public function load($components) {
		$components = (array) $components;

		foreach($components as $component_name) {
			// Do not load the component if it is already loaded
			if(!isset($this->loaded_components[$component_name])) {
				include(ALUMINIUM_COMPONENTS.$component_name.'/'.$component_name.'.php');

				$class_name = str_replace('_', '', $component_name);
				$this->loaded_components[$component_name] = new $class_name();
			}
		}
	}

	/**
	 * Gets a component instance.
	 *
	 * The component will be loaded if it is not included in the loaded components list.
	 *
	 * @param	string	$component_name	Name of the component to get.
	 * @return	mixed	A component instance.
	 */
	public function get($component_name) {
		// Load the component if it is not already loaded
		if(!isset($this->loaded_components[$component_name])) {
			$this->load($component_name);
		}

		return $this->loaded_components[$component_name];
	}

	/**
	 * Triggers an event.
	 *
	 * Every loaded component will be notified, so those with an appropiate handler will react.
	 *
	 * @param	string	$event_name	Event name.
	 * @param	array	$params	List of params that will be passed to the event handler.
	 */
	public function event($event_name, $params = null) {
		$params = (array) $params;

		foreach($this->loaded_components as $component_name => $component) {
			$event_handler = $event_name.'_event';

			// Check wether the component can react to this event
			if(method_exists($component, $event_handler) && is_callable($component->$event_handler($params))) {
				$component->$event($params);
			}
		}
	}

}

?>
