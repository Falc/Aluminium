<?php
/**
 * This file contains the Database component class.
 *
 * @author		Aitor García <aitor.falc@gmail.com>
 * @copyright	2012 Aitor García <aitor.falc@gmail.com>
 * @license		https://github.com/Falc/Aluminium/blob/master/LICENSE Simplified BSD License
 */

namespace Aluminium\Component\Database;

use \PDO;

/**
 * The Database component enables to load a DatabaseDriver.
 *
 * A DatabaseDriver allows to communicate with a specific database by wrapping the PHP Data Objects (PDO) extension.
 */
class Database {

	/**
	 * Database driver name.
	 *
	 * It must match a PDO driver name: http://php.net/manual/en/pdo.drivers.php
	 */
	protected $driver_name;

	/**
	 * Database hostname.
	 *
	 * @var string
	 */
	protected $db_host;

	/**
	 * Database port.
	 *
	 * @var int
	 */
	protected $db_port;

	/**
	 * Database name.
	 *
	 * @var string
	 */
	protected $db_name;

	/**
	 * Database user.
	 *
	 * @var string
	 */
	protected $db_user;

	/**
	 * Database password.
	 *
	 * @var string
	 */
	protected $db_pass;

	/**
	 * Database constructor.
	 *
	 * Sets the configuration option from $conf, if specified.
	 *
	 * @param	array	$conf	An array containing configuration options.
	 */
	public function __construct($conf = null) {
		// Default values
		$this->db_host = '127.0.0.1';
		$this->db_port = null;
		$this->db_name = 'default';
		$this->db_user = 'nouser';
		$this->db_pass = '';

		// Load the configuration, if defined
		if(!empty($conf)) {
			$this->load_configuration($conf);
		}
	}

	/**
	 * Gets the driver name.
	 *
	 * @return string
	 */
	public function get_driver_name() {
		return $this->driver_name;
	}

	/**
	 * Sets the driver name.
	 *
	 * @param	string	$driver_name	Driver name.
	 */
	public function set_driver_name($driver_name) {
		// If driver name is empty, stop the process
		if(empty($driver_name)) {
			trigger_error('No DatabaseDriver was defined.', E_USER_ERROR);
		}

		// If the specified driver is not valid, stop the process
		$available_drivers = PDO::getAvailableDrivers();

		if(!in_array($driver_name, $available_drivers)) {
			trigger_error('The selected driver is not valid. Available drivers: '.implode(', ', $available_drivers).'.', E_USER_ERROR);
		}

		// If the specified driver file does not exist, stop the process
		$driver_file = dirname(__FILE__).'/drivers/'.$driver_name.'_driver.php';

		if(!file_exists($driver_file)) {
			trigger_error('File '.$driver_file.' does not exist or cannot be loaded.', E_USER_ERROR);
		}

		$this->driver_name = $driver_name;
	}

	/**
	 * Gets the hostname.
	 *
	 * @return string
	 */
	public function get_db_host() {
		return $this->db_host;
	}

	/**
	 * Sets the hostname.
	 *
	 * @param	string	$db_host	Hostname.
	 */
	public function set_db_host($db_host) {
		$this->db_host = $db_host;
	}

	/**
	 * Gets the port.
	 *
	 * @return int
	 */
	public function get_db_port() {
		return $this->db_port;
	}

	/**
	 * Sets the port.
	 *
	 * @param	int	$db_port	Port.
	 */
	public function set_port($db_port) {
		$this->db_port = is_numeric($db_port) ? intval($db_port) : null;
	}

	/**
	 * Gets the database name.
	 *
	 * @return string
	 */
	public function get_db_name() {
		return $this->db_name;
	}

	/**
	 * Sets the database name.
	 *
	 * @param	string	$db_name	Database name.
	 */
	public function set_db_name($db_name) {
		$this->db_name = $db_name;
	}

	/**
	 * Gets the user name.
	 *
	 * @return string
	 */
	public function get_db_user() {
		return $this->db_user;
	}

	/**
	 * Sets the user name.
	 *
	 * @param	string	$db_user	User name.
	 */
	public function set_db_user($db_user) {
		$this->db_user = $db_user;
	}

	/**
	 * Gets the password.
	 *
	 * @return string
	 */
	public function get_db_pass() {
		return $this->db_pass;
	}

	/**
	 * Sets the password.
	 *
	 * @param	string	$db_pass	Password.
	 */
	public function set_db_pass($db_pass) {
		$this->db_pass = $db_pass;
	}

	/**
	 * Sets properties from an array.
	 *
	 * @param	array	$conf	An array containing some configuration options.
	 */
	public function load_configuration($conf) {
		// Set the driver, if defined
		if(!empty($conf['driver'])) {
			$this->set_driver_name($conf['driver']);
		}

		// Set the host, if defined
		if(!empty($conf['host'])) {
			$this->set_db_host($conf['host']);
		}

		// Set the port, if defined
		if(!empty($conf['port'])) {
			$this->set_db_port($conf['port']);
		}

		// Set the database name, if defined
		if(!empty($conf['name'])) {
			$this->set_db_name($conf['name']);
		}

		// Set the user, if defined
		if(!empty($conf['user'])) {
			$this->set_db_user($conf['user']);
		}

		// Set the password, if defined
		if(!empty($conf['pass'])) {
			$this->set_db_pass($conf['pass']);
		}
	}

	/**
	 * Sets properties from a configuration file.
	 *
	 * @param	string	$conf_file	Name of the configuration file.
	 */
	public function load_configuration_from_file($conf_file) {
		// Load the configuration file
		$conf = require($conf_file);

		$this->load_configuration($conf);
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
			$this->set_driver_name($driver_name);
		}

		// Include the driver class file
		$driver_file = dirname(__FILE__).'/drivers/'.$this->get_driver_name().'_driver.php';
		require_once($driver_file);

		// Create the driver instance
		$driver_class = "Aluminium\\Component\\Database\\Driver\\";
		$driver_class .= $this->get_driver_name().'DatabaseDriver';

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
