<?php
/**
 * This file contains the MySQL DatabaseDriver class.
 *
 * @author		Aitor García <aitor.falc@gmail.com>
 * @copyright	2012 Aitor García <aitor.falc@gmail.com>
 * @license		https://github.com/Falc/Aluminium/blob/master/LICENSE Simplified BSD License
 */

namespace Aluminium\Component\Database\Driver;

use \PDO;

/**
 * Database driver for MySQL.
 */
class MySQLDriver extends DatabaseDriver {

	/**
	 * Creates a PDO instance using the MySQL driver.
	 */
	public function create_pdo_instance() {
		return new PDO('mysql:host='.$this->db_host.';dbname='.$this->db_name, $this->db_user, $this->db_pass);
	}

}
?>
