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
	 * Database constructor.
	 *
	 * Defines the drivers directory.
	 */
	public function __construct() {
		if(!defined('ALUMINIUM_DATABASE')) {
			define('DB_DRIVERS_PATH',	dirname(__FILE__).'/drivers/');

			define('ALUMINIUM_DATABASE',	TRUE);

			// [Debug log]
			if(function_exists('write_debug_log')) {
				write_debug_log('Database instance created successfully.', 'database');
			}
		}
	}

	/**
	 * Loads a DatabaseDriver.
	 *
	 * This method will instance the corresponding DatabaseDriver for $driver_name. If $driver_name is not
	 * specified, it will rely on the 'driver' option from the database configuration file.
	 *
	 * The driver name must match a PDO driver name: http://php.net/manual/en/pdo.drivers.php
	 *
	 * @param	string	$driver_name	A valid PDO driver name.
	 */
	public function load_driver($driver_name = null) {
		// Load the configuration
		$conf = require(APP_CONF.'database_conf.php');

		$driver = (is_null($driver_name)) ? $conf['driver'] : $driver_name;

		// If no driver was set, stop the process
		if(!isset($driver) || empty($driver)) {
			die('Error: No PDO driver was defined. Check your app\'s database_conf.php file.');
		}

		// If the specified driver is not valid, stop the process
		$available_drivers = PDO::getAvailableDrivers();
		if(!in_array($driver, $available_drivers)) {
			die('Error: The selected driver is not valid. Available drivers: '.implode(', ', $available_drivers).'.');
		}

		// If the specified driver file does not exist, stop the process
		$driver_file = DB_DRIVERS_PATH.$driver.'_driver.php';
		if(!file_exists($driver_file)) {
			die('Error: File '.$driver.'_driver.php not found in '.DB_DRIVERS_PATH);
		}

		// Include the required driver class files
		require_once(DB_DRIVERS_PATH.'database_driver.php');
		require_once($driver_file);

		// If no host was set, use '127.0.0.1' as default
		$db_host = (isset($conf['host']) && !empty($conf['host'])) ? $conf['host'] : '127.0.0.1';

		// If no port was set or it isn't numeric, set null
		$db_port = (isset($conf['port']) && is_numeric($conf['port'])) ? $conf['port'] : null;

		$db_name = $conf['name'];
		$db_user = $conf['user'];
		$db_pass = $conf['pass'];

		// Create the driver instance
		$driver_class = $driver.'Driver';
		$driver_instance = new $driver_class($db_host, $db_port, $db_name, $db_user, $db_pass);

		// [Debug log]
		if(function_exists('write_debug_log')) {
			write_debug_log('[DatabaseDriver] '.$driver.' loaded successfully.', 'database');
		}

		return $driver_instance;
	}

}

?>
