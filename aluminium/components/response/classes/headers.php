<?php
/**
 * This file contains the Headers class.
 *
 * It is based on the Http package from "The Aura Project for PHP", licensed under the Simplified BSD license.
 *
 * @copyright	2010-2012 The Aura Project for PHP <http://auraphp.github.com/>
 * @license		https://github.com/auraphp/Aura.Http/blob/master/LICENSE Simplified BSD License
 * @package		Aluminium
 * @subpackage	Components
 */

/**
 * Represents a collection of non-cookie HTTP headers.
 *
 * @package		Aluminium
 * @subpackage	Components
 */
class Headers {
	/**
	 * The list that contains all the headers.
	 *
	 * @var array
	 */
	protected $list = array();

	/**
	 * Adds a header value to an existing header.
	 *
	 * @param	string	$name	Name of the header.
	 * @param	string	$value	A value.
	 */
	public function add($name, $value) {
		$name = $this->sanitize_name($name);
		$this->list[$name][] = $value;
	}

	/**
	 * Gets a header named $name, if any.
	 *
	 * @param	string	$name	Name of the header.
	 */
	public function get($name) {
		if(in_array($name, $this->list)) {
			return $this->list[$name];
		}

		return null;
	}

	/**
	 * Gets all the headers.
	 */
	public function get_all() {
		return $this->list;
	}

	/**
	 * Sets a header value. Previous values will be overwritten.
	 *
	 * @param	string	$name	Name of the header.
	 * @param	string	$value	A value.
	 */
	public function set($name, $value) {
		$name = $this->sanitize_name($name);
		$this->list[$name] = array($value);
	}

	/**
	 * Sets all the headers at once. Previous headers will be replaced.
	 *
	 * @param	array	$headers	An array of headers where the key is the header name and the value
	 * is the header value (multiple values are allowed).
	 */
	public function set_all(array $headers = array()) {
		$this->list = array();

		foreach($headers as $name => $values) {
			$values = (array) $values;

			foreach($values as $value) {
				$this->add($name, $value);
			}
		}
	}

	/**
	 * Sends all the headers using header().
	 */
	public function send() {
		foreach($this->list as $name => $values) {
			foreach($values as $value) {
				header($name.': '.$value);
			}
		}
	}

	/**
	 * Sanitizes the header names by removing every non-valid character.
	 *
	 * Valid characters are: a-z, A-Z, 0-9, _ and -.
	 *
	 * Underscores are converted to dashes, and word case is normalized.
	 *
	 * @param	string	$name	Name of the header to sanitize.
	 */
	public function sanitize_name($name) {
		$name = preg_replace('/[^a-zA-Z0-9_-]/', '', $name);
		$name = ucwords(strtolower(str_replace(array('-', '_'), ' ', $name)));
		$name = str_replace(' ', '-', $name);
		return $name;
	}

}

?>
