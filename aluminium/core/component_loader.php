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
	 * Loads the specified component.
	 *
	 * @param	string	Name of the component to load.
	 */
	public function load($component_name) {
		// Do not load the component if it is already loaded
		if(!isset($this->loaded_components[$component_name])) {
			include(ALUMINIUM_COMPONENTS.$component_name.'/'.$component_name.'.php');

			$class_name = str_replace('_', '', $component_name);
			$this->loaded_components[$component_name] = new $class_name;
		}
	}

	/**
	 * Loads every component specified in the array.
	 *
	 * @param	array	List containing names of components to load.
	 */
	public function load_array($components) {
		if(!empty($components)) {
			foreach($components as $component) {
				$this->load($component);
			}
		}
	}

	/**
	 * Gets a component instance.
	 *
	 * The component will be loaded if it is not included in the loaded components list.
	 *
	 * @param	string	Name of the component to get.
	 * @return	mixed	A component instance.
	 */
	public function get($component_name) {
		// Load the component if it is not already loaded
		if(!isset($this->loaded_components[$component_name])) {
			$this->load($component_name);
		}

		return $this->loaded_components[$component_name];
	}

}
?>
