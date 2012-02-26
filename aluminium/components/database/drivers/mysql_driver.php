<?php
/**
 * This file contains the MySQL DatabaseDriver class.
 *
 * @author		Aitor García <aitor.falc@gmail.com>
 * @copyright	2012 Aitor García <aitor.falc@gmail.com>
 * @license		https://github.com/Falc/Aluminium/blob/master/LICENSE New BSD License
 * @package		Aluminium
 * @subpackage	Components
 */

/**
 * Database driver for MySQL.
 *
 * @package		Aluminium
 * @subpackage	Components
 */
class MySQLDriver extends DatabaseDriver {

	/**
	 * Creates a PDO instance using the MySQL driver.
	 *
	 * @param	array	$conf	A list containing the database host, port, name, user and pass.
	 */
	public function create_pdo_instance($conf) {
		return new PDO('mysql:host='.$conf['host'].';dbname='.$conf['name'], $conf['user'], $conf['pass']);
	}

}

?>
