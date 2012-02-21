<?php
/**
 * This file contains the DatabaseDriver class.
 *
 * @author		Aitor García <aitor.falc@gmail.com>
 * @copyright	2012 Aitor García <aitor.falc@gmail.com>
 * @license		https://github.com/Falc/Aluminium/blob/master/LICENSE New BSD License
 * @package		Aluminium
 * @subpackage	Components
 */

/**
 * The parent class for database drivers.
 *
 * @package		Aluminium
 * @subpackage	Components
 */
abstract class DatabaseDriver {

	abstract function connect($conf);

}

?>
