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
	 * PDO instance.
	 */
	private $db_con;

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
	 * The SQL Query to process when calling execute().
	 *
	 * @var string
	 */
	private $query;

	/**
	 * A list containing the parameters used in $query.
	 *
	 * @var array
	 */
	private $params;

	/**
	 * Database constructor.
	 *
	 * Loads the required configuration, checks it and finally instances the specified database driver.
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

		// If the specified driver file does not exist, stop the process
		$driver_file = DB_DRIVERS_PATH.$conf['driver'].'_driver.php';
		if(!file_exists($driver_file)) {
			die('Error: File '.$conf['driver'].'_driver.php not found in '.DB_DRIVERS_PATH);
		}

		// Include the required driver class files
		require_once(DB_DRIVERS_PATH.'database_driver.php');
		require_once($driver_file);

		// Create the driver instance
		$driver_class = $conf['driver'].'_Driver';
		$this->driver = new $driver_class();

		// If no host was set, use '127.0.0.1' as default
		$this->db_host = (isset($conf['host']) && !empty($conf['host'])) ? $conf['host'] : '127.0.0.1';

		// If no port was set or it isn't numeric, set null
		$this->db_port = (isset($conf['port']) && is_numeric($conf['port'])) ? $conf['port'] : null;

		$this->db_name = $conf['name'];
		$this->db_user = $conf['user'];
		$this->db_pass = $conf['pass'];
	}

	/**
	 * Establishes a database connection.
	 */
	public function connect() {
		// Don't connect if it is already done
		if(!is_null($this->db_con)) {
			return;
		}

		$conf = array(
			'host'	=> $this->db_host,
			'port'	=> $this->db_port,
			'name'	=> $this->db_name,
			'user'	=> $this->db_user,
			'pass'	=> $this->db_pass
		);

		try {
			$this->db_con = $this->driver->connect($conf);

			$this->db_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->db_con->exec('SET NAMES utf8');
		}
		catch(PDOException $error) {
			echo $error->getMessage();
			die;
		}
	}

	/**
	 * Creates a query that inserts $values into $table.
	 */
	public function insert($table, $values) {
		// The query, statement and parameters should be cleared before doing anything
		$this->clear();

		$cols = '';
		$placeholders = '';

		// Build $cols and $placeholders before using them in a prepared statement
		foreach($values as $key=>$value) {
			$cols .= $key.',';
			$placeholders .= '?,';

			// Append $value to the parameter list
			array_push($this->params, $value);
		}

		// The last comma must be removed
		$cols = substr($cols, 0, -1);
		$placeholders = substr($placeholders, 0, -1);

		$this->query = 'INSERT INTO '.$table.' ('.$cols.') VALUES ('.$placeholders.')';

		return $this;
	}

	/**
	 * Creates a query that updates $values on $table.
	 */
	public function update($table, $values) {
		// The query, statement and parameters should be cleared before doing anything
		$this->clear();

		$data = '';

		// Build $data (a comma separated list of positional placeholders) before using them in a prepared statement
		foreach($values as $key=>$value) {
			$data .= $key.' = ?,';

			// Append $value to the parameter list
			array_push($this->params, $value);
		}

		// The last comma must be removed
		$data = substr($data, 0, -1);

		$this->query = 'UPDATE '.$table.' SET '.$data;

		return $this;
	}

	/**
	 * Creates a query that deletes rows from $table.
	 */
	public function delete($table) {
		// The query, statement and parameters should be cleared before doing anything
		$this->clear();

		$this->query = 'DELETE FROM '.$table;

		return $this;
	}

	/**
	 * Sets the SELECT part of the query.
	 */
	public function select($columns = '*') {
		// The query, statement and parameters should be cleared before doing anything
		$this->clear();

		$this->query = 'SELECT '.$columns;

		return $this;
	}

	/**
	 * Sets the FROM part of a SELECT query.
	 */
	public function from($table) {
		$this->query .= ' FROM '.$table;

		return $this;
	}

	/**
	 * Sets the WHERE clauses of the query.
	 */
	public function where($clauses = null) {
		// If there are no clauses, just ignore the WHERE part
		if(is_null($clauses) || empty($clauses)) {
			return;
		}

		$where = ' WHERE ';

		// Every parameter used in clauses will be added to $params and appended to $this->params
		$params = array();

		// Process the clauses
		foreach($clauses as $clause) {
			// In a clause with 4 elements, the first one refers to the logical operator
			if(count($clause) == 4) {
				$where .= $clause[0].' ';

				// Remove the logical operator from the clause and reindex the array
				unset($clause[0]);
				$clause = array_values($clause); // Re-index the array
			}

			// Add the clause to the WHERE part
			$where .= $clause[0].' '.$clause[1].' ? ';

			// Append the clause value to the parameter list
			array_push($this->params, $clause[2]);
		}

		$this->query .= $where;

		return $this;
	}

	/**
	 * Clears the SQL query, PDO statement and parameter list
	 */
	public function clear() {
		$this->query = null;
		$this->params = array();
	}

	public function execute() {
	}

}

?>
