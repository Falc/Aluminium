<?php
/**
 * This file contains the DatabaseDriver class.
 *
 * @author		Aitor García <aitor.falc@gmail.com>
 * @copyright	2012 Aitor García <aitor.falc@gmail.com>
 * @license		https://github.com/Falc/Aluminium/blob/master/LICENSE Simplified BSD License
 * @package		Aluminium
 * @subpackage	Components
 */

/**
 * The parent class for DatabaseDrivers.
 *
 * A DatabaseDriver allows to communicate with a specific database by wrapping the PHP Data Objects (PDO) extension.
 *
 * @package		Aluminium
 * @subpackage	Components
 */
abstract class DatabaseDriver {
	/**
	 * PDO instance.
	 */
	protected $db_con;

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
	 * The SQL query to process when calling execute().
	 *
	 * @var string
	 */
	protected $query;

	/**
	 * Type of the last SQL query.
	 *
	 * @var int
	 */
	protected $query_type;

	/**
	 * PDOStatement instance.
	 *
	 * @var PDOStatement
	 */
	protected $statement;

	/**
	 * A list containing the parameters used in $query.
	 *
	 * @var array
	 */
	protected $params;

	/**
	 * Number of rows affected by the last INSERT, UPDATE or DELETE query.
	 *
	 * @var int
	 */
	protected $row_count;

	/**
	 * DatabaseDriver constructor.
	 *
	 * Loads the required configuration and checks it.
	 *
	 * @param	string	$db_host	Database hostname.
	 * @param	int		$db_port	Database port.
	 * @param	string	$db_name	Database name.
	 * @param	string	$db_user	Database user.
	 * @param	string	$db_pass	Database password.
	 */
	public function __construct($db_host, $db_port, $db_name, $db_user, $db_pass) {
		$this->db_host = $db_host;
		$this->db_port = $db_port;
		$this->db_name = $db_name;
		$this->db_user = $db_user;
		$this->db_pass = $db_pass;
	}

	/**
	 * Creates a PDO instance.
	 */
	abstract protected function create_pdo_instance();

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
			$this->db_con = $this->create_pdo_instance();

			$this->db_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->db_con->exec('SET NAMES utf8');

			// [Debug log]
			if(defined('DEBUG_FILE')) {
				$output = '[database->connection->driver] '.$this->db_con->getAttribute(PDO::ATTR_DRIVER_NAME)."\n";
				$output .= '[database->connection->host] '.$this->db_host."\n";
				$output .= '[database->connection->port] '.$this->db_port."\n";
				$output .= '[database->connection->name] '.$this->db_name."\n";
				$output .= '[database->connection->user] '.$this->db_user."\n";
				file_put_contents(DEBUG_FILE, $output, FILE_APPEND);
			}

		}
		catch(PDOException $error) {
			trigger_error($error->getMessage(), E_USER_ERROR);
		}
	}

	/**
	 * Creates a query that inserts $values into $table.
	 *
	 * The query will not be executed until execute() is called.
	 *
	 * @param	string	$table	The name of the table where $values will be inserted.
	 * @param	array	$values	An associative array with the format column => value.
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
		$this->query_type = QueryType::INSERT;

		return $this;
	}

	/**
	 * Creates a query that updates $values on $table.
	 *
	 * The query can be extended using some methods like where($clauses) to add some clauses,
	 * but it will not be executed until execute() is called.
	 *
	 * @param	string	$table	The name of the table where $values will be changed.
	 * @param	array	$values	An associative array with the format column => value.
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
		$this->query_type = QueryType::UPDATE;

		return $this;
	}

	/**
	 * Creates a query that deletes rows from $table.
	 *
	 * The query can be extended using some methods like where($clauses) to add some clauses,
	 * but it will not be executed until execute() is called.
	 *
	 * @param	string	$table	The name of the table where $values will be changed.
	 */
	public function delete($table) {
		// The query, statement and parameters should be cleared before doing anything
		$this->clear();

		$this->query = 'DELETE FROM '.$table;
		$this->query_type = QueryType::DELETE;

		return $this;
	}

	/**
	 * Sets the SELECT part of the query.
	 *
	 * The FROM part MUST be set calling the from($table) method right after select($columns).
	 * When the FROM part is set, the query can be extended using some methods like where($clauses) to
	 * add some clauses, but it will not be executed until execute() is called.
	 *
	 * @param	string	$columns	A comma-separated list of columns.
	 */
	public function select($columns = '*') {
		// The query, statement and parameters should be cleared before doing anything
		$this->clear();

		$this->query = 'SELECT '.$columns;
		$this->query_type = QueryType::SELECT;

		return $this;
	}

	/**
	 * Sets the FROM part of a SELECT query.
	 *
	 * This method must be called before execute() and right after select($columns).
	 *
	 * @param	string	$table	The name of the table where data will be selected from.
	 */
	public function from($table) {
		$this->query .= ' FROM '.$table;

		return $this;
	}

	/**
	 * Sets the WHERE clauses of the query.
	 *
	 * Clause format: array([Logical operator,] 'column', 'Comparison function/operator', 'value/s')
	 *
	 * Example:
	 * $clauses = array (
	 *     array('name', 'IS', 'John'),
	 *     array('AND', 'age', '>', 35),
	 *     array('OR', 'city', 'IN', ('London', 'Bilbao', 'Tokyo')
	 * );
	 *
	 * @param	array	$clauses	An array of clauses.
	 */
	public function where($clauses = null) {
		// If there are no clauses, just ignore the WHERE part
		if(empty($clauses)) {
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

	public function group_by($group_by) {
		$this->query .= ' GROUP BY '.$group_by;

		return $this;
	}

	/**
	 * Sets the ORDER BY part of a SELECT query.
	 *
	 * @param	string	$sort_criteria	The sort criteria, usually column names.
	 * @param	string	$direction		The sort direction. If not defined, ASC will be used.
	 */
	public function order_by($order_by, $direction = 'ASC') {
		$this->query .= ' ORDER BY '.$order_by.' '.$direction;

		return $this;
	}

	/**
	 * Sets the part used to limit the result set of a SELECT query.
	 *
	 * @param	int	$num_rows	The maximum number of rows that should be returned.
	 * @param	int $offset		The number of rows that will be skipped.
	 */
	public function limit($num_rows, $offset = 0) {
		$this->query .= ' LIMIT '.$num_rows;

		// The OFFSET clause is not needed when $offset is 0
		if($offset != 0) {
			$this->query .= ' OFFSET '.$offset;
		}

		return $this;
	}

	/**
	 * Clears the SQL query, PDO statement and parameter list
	 */
	public function clear() {
		$this->query = null;
		$this->statement = null;
		$this->params = array();
	}

	/**
	 * Executes the built query.
	 *
	 * The insert(), update(), delete() and select() methods don't perform any query against the database.
	 * Instead, they build the query (partially in some cases) and wait for an execute() call. This allows
	 * to extend the query with methods like where($clauses).
	 */
	public function execute() {
		// If the query is null or empty, stop the process
		if(empty($this->query)) {
			trigger_error('There is no query to execute.', E_USER_NOTICE);
		}

		try {
			$this->connect();

			// Prepare the statement
			$this->statement = $this->db_con->prepare($this->query);

			// Parameter binding
			foreach($this->params as $key=>$param) {
				$this->statement->bindParam($key+1, $param);
			}

			// Execute the statement
			$success = $this->statement->execute($this->params);

			if($this->query_type === QueryType::SELECT) {
				// Set the default fetch mode to PDO::FETCH_ASSOC
				$this->statement->setFetchMode(PDO::FETCH_ASSOC);

				// The method rowCount() is not reliable for select queries, so 0 is used as a default value
				// and the user should count them manually, by using the count() function
				$this->row_count = 0;
			}
			else {
				$this->row_count = $this->statement->rowCount();
			}

			// [Debug log]
			if(defined('DEBUG_FILE')) {
				$output = '[database->query_executed->query] '.$this->query."\n";

				foreach($this->params as $key=>$param) {
					$output .= '[database->query_executed->parameter_bound] '.$key.' => '.$param."\n";
				}

				if($this->query_type !== QueryType::SELECT) {
					$output = '[database->query_executed->affected_rows] '.$this->row_count."\n";
				}

				file_put_contents(DEBUG_FILE, $output, FILE_APPEND);
			}

			return $success;
		}
		catch(PDOException $error) {
			trigger_error($error->getMessage(), E_USER_ERROR);
		}
	}

	/**
	 * Returns the number of rows affected by the last INSERT, UPDATE or DELETE query.
	 *
	 * This SHOULD NOT be used to get the number of rows returned by a SELECT query, since it will return 0.
	 * SELECT queries are performed for getting some data, so you will fetch them. Then you can use the count()
	 * function, perform a SELECT COUNT(*) or a similar query.
	 */
	public function row_count() {
		return $this->row_count;
	}

	/**
	 * Returns an array containing all of the result set rows.
	 */
	public function fetch_all() {
		return $this->statement->fetchAll();
	}

	/**
	 * Returns an array containing all of the result set rows as associative arrays.
	 */
	public function fetch_all_as_array() {
		$this->statement->setFetchMode(PDO::FETCH_ASSOC);
		return $this->fetch_all();
	}

	/**
	 * Returns an array containing all of the result set rows as instances of $class.
	 *
	 * @param	string	$class		The name of an existent class. Rows will be returned as instances of that class.
	 * @param	array	$parameters	An array of parameters that will be passed to the constructor.
	 */
	public function fetch_all_as_class($class, $parameters = null) {
		// If the class is not defined, stop the process
		if(!class_exists($class, FALSE)) {
			trigger_error('Class '.$class.' is not defined.', E_USER_ERROR);
		}

		$this->statement->setFetchMode(PDO::FETCH_CLASS, $class, $parameters);
		return $this->fetch_all();
	}

	/**
	 * Returns an array containing all of the result set rows as anonymous objects.
	 */
	public function fetch_all_as_object() {
		$this->statement->setFetchMode(PDO::FETCH_OBJ);
		return $this->fetch_all();
	}

	/**
	 * Returns the next row from the result set.
	 */
	public function fetch() {
		return $this->statement->fetch();
	}

	/**
	 * Returns the next row from the result set as an associative array.
	 */
	public function fetch_as_array() {
		$this->statement->setFetchMode(PDO::FETCH_ASSOC);
		return $this->fetch();
	}

	/**
	 * Returns the next row from the result set as an instance of $class.
	 *
	 * @param	string	$class		The name of an existent class. Rows will be returned as instances of that class.
	 * @param	array	$parameters	An array of parameters that will be passed to the constructor.
	 */
	public function fetch_as_class($class, $parameters = null) {
		// If the class is not defined, stop the process
		if(!class_exists($class, FALSE)) {
			trigger_error('Class '.$class.' is not defined.', E_USER_ERROR);
		}

		$this->statement->setFetchMode(PDO::FETCH_CLASS, $class, $parameters);
		return $this->fetch();
	}

	/**
	 * Returns the next row from the result set as an anonymous object.
	 */
	public function fetch_as_object() {
		$this->statement->setFetchMode(PDO::FETCH_OBJ);
		return $this->fetch();
	}

}

?>
