<?php
/**
 * This file contains the Database component class.
 *
 * @author		Aitor García <aitor.falc@gmail.com>
 * @copyright	2012 Aitor García <aitor.falc@gmail.com>
 * @license		https://github.com/Falc/Aluminium/blob/master/LICENSE New BSD License
 * @package		Aluminium
 * @subpackage	Components
 */

/**
 * The Database component acts as a Database Abstraction Layer.
 *
 * This component wraps the PHP Data Objects (PDO) extension.
 *
 * @package		Aluminium
 * @subpackage	Components
 */
class Database {
	/**
	 * Driver instance.
	 *
	 * @var mixed
	 */
	private $driver;

	/**
	 * Database hostname.
	 *
	 * @var string
	 */
	private $db_host;

	/**
	 * Database port.
	 *
	 * @var int
	 */
	private $db_port;

	/**
	 * Database name.
	 *
	 * @var string
	 */
	private $db_name;

	/**
	 * Database user.
	 *
	 * @var string
	 */
	private $db_user;

	/**
	 * Database password.
	 *
	 * @var string
	 */
	private $db_pass;

	/**
	 * Database constructor.
	 */
	public function __construct() {
		define('DB_DRIVERS_PATH',	dirname(__FILE__).'/drivers/');

		// Load the configuration
		$conf = require_once(APP_CONFIG.'database_conf.php');

		// If no driver was set, stop the process
		if(!isset($conf['driver']) || empty($conf['driver'])) {
			die('Error: No PDO driver was defined. Check your app\'s database_conf.php file.');
		}

		// If the specified driver is not valid, stop the process
		$available_drivers = PDO::getAvailableDrivers();
		if(!in_array($conf['driver'], $available_drivers)) {
			die('Error: The selected driver is not valid. Available drivers: '.implode(', ', $available_drivers).'.');
		}

		// If no host was set, use '127.0.0.1' as default
		$this->db_host = (isset($conf['host']) && !empty($conf['host'])) ? $conf['host'] : '127.0.0.1';

		// If no port was set or it isn't numeric, set null
		$this->db_port = (isset($conf['port']) && is_numeric($conf['port'])) ? $conf['port'] : null;

		$this->db_name = $conf['name'];
		$this->db_user = $conf['user'];
		$this->db_pass = $conf['pass'];
	}

}

?>
