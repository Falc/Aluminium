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
class MySQL_Driver extends DatabaseDriver {

	public function __construct() {
	}

	public function connect($conf) {
		echo '<p>MySQL own connect method!</p>';
	}

}

?>
