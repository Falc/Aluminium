<?php
/**
 * This file contains the QueryType class.
 *
 * @author		Aitor García <aitor.falc@gmail.com>
 * @copyright	2012 Aitor García <aitor.falc@gmail.com>
 * @license		https://github.com/Falc/Aluminium/blob/master/LICENSE Simplified BSD License
 * @package		Aluminium
 * @subpackage	Components
 */

/**
 * The QueryType class acts as an "enum" of query types.
 *
 * @package		Aluminium
 * @subpackage	Components
 */
abstract class QueryType {
	const SELECT = 0;
	const INSERT = 1;
	const UPDATE = 2;
	const DELETE = 3;
}

?>
