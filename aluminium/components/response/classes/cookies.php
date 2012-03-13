<?php
/**
 * This file contains the Cookies class.
 *
 * It is based on the Http package from "The Aura Project for PHP", licensed under the Simplified BSD license.
 *
 * @copyright	2010-2012 The Aura Project for PHP <http://auraphp.github.com/>
 * @license		https://github.com/auraphp/Aura.Http/blob/master/LICENSE Simplified BSD License
 * @package		Aluminium
 * @subpackage	Components
 */

/**
 * Represents a collection of Cookies.
 *
 * @package		Aluminium
 * @subpackage	Components
 */
class Cookies {
	/**
	 * The list that contains all the cookies.
	 *
	 * @var array
	 */
	protected $list = array();

	/**
	 * Gets a cookie named $name, if any.
	 *
	 * @param	string	$name	Name of the cookie.
	 */
	public function get($name) {
		if(in_array($name, $this->list)) {
			return $this->list[$name];
		}

		return null;
	}

	/**
	 * Gets all the cookies.
	 */
	public function get_all() {
		return $this->list;
	}

	/**
	 * Sets a cookie.
	 *
	 * @param	string	$name	The cookie.
	 * @param	array	$data	The cookie data.
	 */
	public function set($name, $data) {
		// Make sure that expire, secure and httponly have the correct type
		settype($data['expire'], 'int');
		settype($data['secure'], 'bool');
		settype($data['httponly'], 'bool');

		$this->list[$name] = $data;
	}

	/**
	 * Sets all the cookies.
	 *
	 * @param	array	$cookies	An array of cookies where the key is the name and the value is an array of cookie data.
	 */
	public function set_all(array $cookies = array()) {
		$this->list = array();

		foreach($cookies as $name => $data) {
			$this->set($name, $data);
		}
	}

	/**
	 * Sends all the cookies using setcookie().
	 */
	public function send() {
		foreach($this->list as $name => $data) {
			setcookie($name, $data['value'], $data['expire'], $data['path'], $data['domain'], $data['httponly']);
		}
	}

}

?>
