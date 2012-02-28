<?php
/**
 * This file contains the Debug component class.
 *
 * @author		Aitor García <aitor.falc@gmail.com>
 * @copyright	2012 Aitor García <aitor.falc@gmail.com>
 * @license		https://github.com/Falc/Aluminium/blob/master/LICENSE Simplified BSD License
 * @package		Aluminium
 * @subpackage	Components
 */

/**
 * The Debug component enables the use of some simple methods for debugging purposes.
 *
 * @package		Aluminium
 * @subpackage	Components
 */
class Debug {
	/**
	 * List of all the debugging values related to the core.
	 *
	 * Format:
	 * key => value
	 *
	 * @var array
	 */
	public $core_data;

	/**
	 * List of all the debugging values related to each component.
	 *
	 * Format:
	 * component1 => array(
	 *     key1 => value1,
	 *     key2 => value2
	 * ),
	 * component2 => array(
	 *     ...
	 * )
	 *
	 * @var array
	 */
	public $components_data;

	/**
	 * Tells whether the debug process is running or not.
	 *
	 * @var bool
	 */
	public $is_running;

	/**
	 * Debug constructor.
	 *
	 * Defines some constants and loads the required configuration.
	 */
	public function __construct() {
		$this->core_data = array();
		$this->components_data = array();
		$this->is_running = FALSE;
	}

	/**
	 * Starts the process.
	 */
	public function start() {
		// Do not start the process if it is running
		if($this->is_running === TRUE) {
			return;
		}

		$this->core_data['memory_usage'] = memory_get_usage();

		$start_time = microtime(true);
		$this->core_data['start_time'] = $start_time;

		$this->is_running = TRUE;
	}

	/**
	 * Stops the process.
	 */
	public function stop() {
		// Do not stop the process and store the info if it is not running
		if($this->is_running === FALSE) {
			return;
		}

		// The real_memory_usage info only works for PHP >= 5.2.0
		if(version_compare(phpversion(), '5.2.0') >= 0) {
			$this->core_data['real_memory_usage'] = memory_get_usage(true).' bytes.';
		}

		$stop_memory_usage = memory_get_usage();
		$start_memory_usage = $this->core_data['memory_usage'];

		$stop_time = microtime(true);
		$this->core_data['stop_time'] = $stop_time;

		$memory_usage = $stop_memory_usage - $start_memory_usage;
		$this->core_data['memory_usage'] = $memory_usage.' bytes.';

		$start_time = $this->core_data['start_time'];
		$execution_time = $stop_time - $start_time;
		$this->core_data['execution_time'] = $execution_time.' seconds.';

		$this->is_running = FALSE;
	}

	/**
	 * Adds the key name to every value found in $source.
	 *
	 * @param	array	$source	An associative array of debug values.
	 */
	public function get_data_from(array $source) {
		$output = '';

		foreach($source as $key=>$data) {
			$output .= $key.': '.$data.'<br />';
		}

		return $output;
	}

	/**
	 * Displays the debug info.
	 */
	public function display() {
		echo $this->get_data_from($this->core_data).'<br />';

		// If the component data is empty, stop the process
		if (!empty($this->components_data)) {
			return;
		}

		foreach($this->components_data as $component=>$data) {
			if(!empty($data)) {
				echo '<strong>'.$component.'</strong><br />';
				echo $this->get_data_from($data).'<br />';
			}
		}
	}
}

?>
