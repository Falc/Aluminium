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
	public $data;

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
		$this->data = array();
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

		$this->data['core']['memory_usage'] = memory_get_usage();

		$start_time = microtime(true);
		$this->data['core']['start_time'] = $start_time;

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
			$this->data['core']['real_memory_usage'] = memory_get_usage(true).' bytes.';
		}

		$stop_memory_usage = memory_get_usage();
		$start_memory_usage = $this->data['core']['memory_usage'];

		$stop_time = microtime(true);
		$this->data['core']['stop_time'] = $stop_time;

		$memory_usage = $stop_memory_usage - $start_memory_usage;
		$this->data['core']['memory_usage'] = $memory_usage.' bytes.';

		$start_time = $this->data['core']['start_time'];
		$execution_time = $stop_time - $start_time;
		$this->data['core']['execution_time'] = $execution_time.' seconds.';

		$this->process_logs();

		$this->is_running = FALSE;
	}

	/**
	 * Processes a single log file.
	 *
	 * This method gets only the relevant log lines and adds them to the $data array.
	 * A relevant line will match the following format:
	 * [Key name] Some message text
	 *
	 * @param	string	$log_file_name	Name of the log file to process.
	 */
	public function process_log($log_file_name) {
		$component_data = array();

		// Get the log file contents
		$log_file = file(APP_LOGS.$log_file_name, FILE_SKIP_EMPTY_LINES);

		foreach($log_file as $line) {
			// Split each line into two parts: timestamp and message
			$log_content = explode(' | ', $line);

			// Split the timestamp into "normal date" and "miliseconds"
			$log_timestamp = array();
			$log_timestamp[] = substr($log_content[0], 0, -5); // i.e: Feb 22 22:38:35
			$log_timestamp[] = substr($log_content[0], -4);    // i.e: 0.323

			// Build the complete timestamp, including miliseconds
			$timestamp = floatval(strtotime($log_timestamp[0]).$log_timestamp[1]);

			// Process the lines with a timestamp greater than the start time
			if($timestamp > $this->data['core']['start_time']) {
				$log_message = array();

				// Process the relevant lines
				if(preg_match('/\[([^]]*)\] (.*)/', $log_content[1], $log_message)) {
					// Convert from 'Key name' to 'key_name'
					$key = strtolower(str_replace(' ', '_', $log_message[1]));

					// Add the content to the $component_data array
					if(!array_key_exists($key, $component_data)) {
						$component_data[$key] = $log_message[2];
					}
					else {
						// If a value was already added, store all of them as an array
						if(!is_array($component_data[$key])) {
							$value = $component_data[$key];
							$component_data[$key] = array();
							$component_data[$key][1] = $value;
						}

						$i = count($component_data[$key]) + 1;
						$component_data[$key][] = $log_message[2];
					}
				}
			}
		}

		// Add the processed $component_data to the $data array
		if(!empty($component_data)) {
			$component_name = substr($log_file_name, 0, -4);
			$this->data[$component_name] = $component_data;
		}
	}

	/**
	 * Processes all the .log files in the logs directory
	 */
	public function process_logs() {
		if($dir_handle = opendir(APP_LOGS)) {
			// Run process_log() for every .log file
			while(($file = readdir($dir_handle)) !== FALSE) {
				if(pathinfo($file, PATHINFO_EXTENSION) === 'log') {
					$this->process_log($file);
				}
			}
			closedir($dir_handle);
		}
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
