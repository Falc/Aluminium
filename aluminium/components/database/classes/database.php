<?php
/**
 * This file contains the Database component class.
 *
 * @author		Aitor García <aitor.falc@gmail.com>
 * @copyright	2012 Aitor García <aitor.falc@gmail.com>
 * @license		https://github.com/Falc/Aluminium/blob/master/LICENSE Simplified BSD License
 * @package		Aluminium
 * @subpackage	Components
 */

/**
 * The Database component enables to load a DatabaseDriver.
 *
 * A DatabaseDriver allows to communicate with a specific database by wrapping the PHP Data Objects (PDO) extension.
 *
 * @package		Aluminium
 * @subpackage	Components
 */
class Database {
	/**
	 * Database driver.
	 *
	 * It must match a PDO driver name: http://php.net/manual/en/pdo.drivers.php
	 */
	public $driver_name;

	/**
	 * Database hostname.
	 *
	 * @var string
	 */
	public $db_host;

	/**
	 * Database port.
	 *
	 * @var int
	 */
	public $db_port;

	/**
	 * Database name.
	 *
	 * @var string
	 */
	public $db_name;

	/**
	 * Database user.
	 *
	 * @var string
	 */
	public $db_user;

	/**
	 * Database password.
	 *
	 * @var string
	 */
	public $db_pass;

	/**
	 * Database constructor.
	 *
	 * Loads a configuration file and instances a driver, if needed.
	 *
	 * @param	string	$conf_file		The configuration file.
	 * @param	bool	$load_driver	Wether the constructor should instance the driver automatically.
	 */
	public function __construct($conf_file = null) {
		// Default values
		$this->db_host = '127.0.0.1';
		$this->db_port = null;
		$this->db_name = 'default';
		$this->db_user = 'nouser';
		$this->db_pass = '';

		if(!is_null($conf_file)) {
			$this->set_configuration_from_file($conf_file);
		}

		// [Debug log]
		if(function_exists('write_debug_log')) {
			write_debug_log('Database instance created successfully.', 'database');
		}
	}

	public function set_configuration_from_file($conf_file) {
		// Load the configuration file
		$conf = require($conf_file);

		// If no driver was set, stop the process
		if(!isset($conf['driver']) || empty($conf['driver'])) {
			die('Error: No PDO driver was defined. Check your app\'s database_conf.php file.');
		}

		$this->driver_name = $conf['driver'];

		// If the specified driver is not valid, stop the process
		$available_drivers = PDO::getAvailableDrivers();
		if(!in_array($this->driver_name, $available_drivers)) {
			die('Error: The selected driver is not valid. Available drivers: '.implode(', ', $available_drivers).'.');
		}

		// Set the host, if defined
		if(isset($conf['host'])) {
			$this->db_host = $conf['host'];
		}

		// Set the port, if defined
		if(isset($conf['port'])) {
			$this->db_port = $conf['port'];
		}

		// Set the database name, if defined
		if(isset($conf['name'])) {
			$this->db_name = $conf['name'];
		}

		// Set the user, if defined
		if(isset($conf['user'])) {
			$this->db_user = $conf['user'];
		}

		// Set the password, if defined
		if(isset($conf['pass'])) {
			$this->db_pass = $conf['pass'];
		}
	}

	/**
	 * Loads a DatabaseDriver.
	 *
	 * This method will instance the corresponding DatabaseDriver for $this->driver_name.
	 */
	public function load_driver($driver_name = null) {
		if(!is_null($driver_name)) {
			$this->driver_name = $driver_name;
		}

		// If the specified driver file does not exist, stop the process
		$driver_file = DB_DRIVERS_PATH.$this->driver_name.'_driver.php';
		if(!file_exists($driver_file)) {
			die('Error: File '.$driver_file.' does not exist or cannot be loaded.');
		}

		// Include the driver class file
		require_once($driver_file);

		// Create the driver instance
		$driver_class = $this->driver_name.'Driver';
		return new $driver_class(
			$this->db_host,
			$this->db_port,
			$this->db_name,
			$this->db_user,
			$this->db_pass
		);
	}
}

?>
