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
	 * List of all the debugging values stored into a multi-level array:
	 * core => array(
	 *     key1 => value1,
	 *     key2 => value2
	 * ),
	 * componentN => array(
	 *     keyA => array(
	 *         ...
	 *     ),
	 *     ...
	 * )
	 *
	 * @var array
	 */
	protected $data;

	/**
	 * Determines whether the debug process is running.
	 *
	 * @var bool
	 */
	protected $is_running;

	/**
	 * Debug constructor.
	 *
	 * Defines some constants and loads the required configuration.
	 */
	public function __construct() {
		$this->data = array();
		$this->is_running = FALSE;
	}

	/**
	 * Determines whether the debug process is running.
	 */
	public function is_running() {
		return $this->is_running;
	}

	/**
	 * Starts the process.
	 */
	public function start() {
		// Do not start the process if it is running
		if($this->is_running === TRUE) {
			return;
		}

		$start_time = microtime(true);
		$this->data['core']['time']['start'] = $start_time;

		$this->data['core']['memory']['usage'] = memory_get_usage();

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
			$this->data['core']['memory']['real_usage'] = memory_get_usage(true).' bytes.';
		}

		$stop_memory_usage = memory_get_usage();
		$start_memory_usage = $this->data['core']['memory']['usage'];

		$memory_usage = $stop_memory_usage - $start_memory_usage;
		$this->data['core']['memory']['usage'] = $memory_usage.' bytes.';

		$stop_time = microtime(true);
		$this->data['core']['time']['stop'] = $stop_time;

		$start_time = $this->data['core']['time']['start'];
		$execution_time = $stop_time - $start_time;
		$this->data['core']['time']['execution'] = $execution_time.' seconds.';

		$this->is_running = FALSE;
	}

	/**
	 * Processes an array of strings that contain debug info.
	 *
	 * A valid debug string will match the following format:
	 * [key1->key2->key3] Some message text or value.
	 *
	 * It can contain any number of keys as long as they are "connected" with '->' like a class instance:
	 * [singlekey] Some value.
	 * [you->age] 32
	 * [core->time->execution] 0.074459075927734 seconds.
	 *
	 * @param	array	$data	An array of strings that contain debug info.
	 */
	public function process_data($data) {
		$processed_data = array();

		foreach($data as $string) {
			$matches = array();

			// Process only the lines that match the format
			if(preg_match('/\[(\w+(->\w+)*)\][ \t]*(.+)/', $string, $matches)) {
				$key = trim($matches[1]);
				$value = trim($matches[3]);
				$value = is_null($value) ? null : $value;

				$key_names = explode('->', $key);

				// By reversing the key names, it gets easier to build the multilevel associative array
				$key_names = array_reverse($key_names);
				$array = $value;

				foreach($key_names as $key_name) {
					$array = array(
						$key_name => $array
					);
				}

				// Merge the resulting array with $processed_data
				$processed_data = $this->merge_data($processed_data, $array);
			}
		}

		return $processed_data;
	}

	/**
	 * Processes a file containing debug info.
	 *
	 * @param	string	$file_name	Name of the file to process.
	 */
	public function process_file($file_name) {
		// If the file does not exist, stop the process
		if(!file_exists($file_name)) {
			trigger_error('File "'.$file_name.'" does not exist or cannot be loaded.', E_USER_ERROR);
		}

		// Get the log file contents
		$file = file($file_name, FILE_SKIP_EMPTY_LINES);

		$processed_data = $this->process_data($file);

		// Merge the resulting array with $this->data
		$this->data = $this->merge_data($this->data, $processed_data);
	}

	/**
	 * Merges two arrays containing data.
	 *
	 * If both arrays have the same keys, their values are merged together into an array.
	 *
	 * @param	array	$array1	An array.
	 * @param	array	$array2	Another array.
	 */
	public function merge_data($array1, $array2) {
		return array_merge_recursive($array1, $array2);
	}

	/**
	 * Formats the debugging data for displaying it properly.
	 *
	 * This is a recursive method.
	 *
	 * @param	string	$name	Name of the current block.
	 * @param	mixed	$data	An array of elements or a single element.
	 */
	public function format_data($name, $data) {
		$output = '';

		// If $data is a single element, return it
		if(!is_array($data)) {
			return $name.': '.$data;
		}
		// If $data is an array, run format_data() recursively
		else {
			if(!is_null($name)) {
				$output .= $name.':'."\n";
			}

			$output .= '<ul>'."\n";
			foreach($data as $name=>$content) {
				$output .= '<li>'.$this->format_data($name, $content).'</li>'."\n";
			}
			$output .= '</ul>'."\n";

			return $output;
		}
	}

	/**
	 * Displays the debug info.
	 */
	public function display() {
		$output = $this->format_data(null, $this->data);
		echo $output;
	}

}

?>
