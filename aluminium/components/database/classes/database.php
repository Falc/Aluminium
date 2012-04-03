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
	 * Database driver name.
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
	 * Sets the configuration option from $conf_file, if specified.
	 *
	 * @param	string	$conf_file	Name of the configuration file.
	 */
	public function __construct($conf_file = null) {
		// Default values
		$this->db_host = '127.0.0.1';
		$this->db_port = null;
		$this->db_name = 'default';
		$this->db_user = 'nouser';
		$this->db_pass = '';

		// Load the configuration file, if any
		if(!is_null($conf_file)) {
			$this->load_configuration_from_file($conf_file);
		}

	}

	/**
	 * Sets all the properties from a configuration file.
	 *
	 * @param	string	$conf_file	Name of the configuration file.
	 */
	public function load_configuration_from_file($conf_file) {
		// Load the configuration file
		$conf = require($conf_file);

		// If no driver was set, stop the process
		if(empty($conf['driver'])) {
			trigger_error('No PDO driver was defined.', E_USER_ERROR);
		}

		$this->driver_name = $conf['driver'];

		// If the specified driver is not valid, stop the process
		$available_drivers = PDO::getAvailableDrivers();
		if(!in_array($this->driver_name, $available_drivers)) {
			trigger_error('The selected driver is not valid. Available drivers: '.implode(', ', $available_drivers).'.', E_USER_ERROR);
		}

		// Set the host, if defined
		if(!empty($conf['host'])) {
			$this->db_host = $conf['host'];
		}

		// Set the port, if defined
		if(!empty($conf['port'])) {
			$this->db_port = $conf['port'];
		}

		// Set the database name, if defined
		if(!empty($conf['name'])) {
			$this->db_name = $conf['name'];
		}

		// Set the user, if defined
		if(!empty($conf['user'])) {
			$this->db_user = $conf['user'];
		}

		// Set the password, if defined
		if(!empty($conf['pass'])) {
			$this->db_pass = $conf['pass'];
		}
	}

	/**
	 * Loads a DatabaseDriver instance.
	 *
	 * It can load the DatabaseDriver from $driver_name. When a $driver_name is not passed as parameter,
	 * the method will rely on $this->driver_name.
	 *
	 * @param	string	Name of the driver to instance.
	 */
	public function load_driver($driver_name = null) {
		// Overwrite $this->driver_name if a driver name has been specified
		if(!is_null($driver_name)) {
			$this->driver_name = $driver_name;
		}

		// If driver name is empty, stop the process
		if(empty($this->driver_name)) {
			trigger_error('No DatabaseDriver was defined.', E_USER_ERROR);
		}

		// If the specified driver file does not exist, stop the process
		$driver_file = dirname(__FILE__).'/drivers/'.$this->driver_name.'_driver.php';
		if(!file_exists($driver_file)) {
			trigger_error('File '.$driver_file.' does not exist or cannot be loaded.', E_USER_ERROR);
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
