<?php
/**
 * This file contains the Route class.
 *
 * It is based on the Router package from "The Aura Project for PHP", licensed under the Simplified BSD license.
 *
 * @copyright	2010-2011 The Aura Project for PHP <http://auraphp.github.com/>
 * @license		https://github.com/auraphp/Aura.Router/blob/master/LICENSE Simplified BSD License
 * @package		Aluminium
 * @subpackage	Components
 */

/**
 * Represents an individual route with a path, params and values.
 *
 * @package		Aluminium
 * @subpackage	Components
 */
class Route {

	/**
	 * The path for this Route with param tokens.
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * The `REQUEST_METHOD` value must match one of the methods in this array;
	 * method; e.g., `'GET'` or `['POST', 'DELETE']`.
	 *
	 * @var array
	 */
	protected $method;

	/**
	 * A map of param tokens to their regex subpatterns.
	 *
	 * @var array
	 */
	protected $params;

	/**
	 * A map of param tokens to their default values; if this Route is matched, these will retain
	 * the corresponding values from the param tokens in the matching path.
	 *
	 * @var array
	 */
	protected $values;

	/**
	 * When true, the `HTTPS` value must be `on`, or the `SERVER_PORT` must be 443.
	 * When false, neither of those values may be present.  When null, it is ignored.
	 *
	 * @var bool
	 */
    protected $secure;

	/**
	 * The $path property converted to a regular expression, using the $params subpatterns.
	 *
	 * @var string
	 */
	protected $regex;

	/**
	 * All param matches found in the path during the is_match() process.
	 *
	 * @var string
	 */
	protected $matches;

	/**
	 * Route constructor.
	 *
	 * @param	string	$path The path for this Route with param token placeholders.
	 * @param	mixed	$method The server REQUEST_METHOD must be one of these values.
	 * @param	array	$params Map of param tokens to regex subpatterns.
	 * @param	array	$values Default values for params.
	 */
	public function __construct($path, $method, $params, $values, $secure = FALSE) {
		$this->path = $path;
		$this->method = ($method === null) ? null : (array) $method;
		$this->params = (array) $params;
		$this->values = (array) $values;
		$this->secure = ($secure === null) ? null : (bool) $secure;

		$this->set_regex();
	}

	/**
	 * Magic read-only for all properties.
	 *
	 * @param	string	$key	The property to read from.
	 * @return	mixed
	 */
	public function __get($key) {
		return $this->$key;
	}

	/**
	 * Sets the regular expression for this Route based on its params.
	 */
	public function set_regex() {
		$pattern = "/\{:(.*?)(:(.*?))?\}/";
		preg_match_all($pattern, $this->path, $matches, PREG_SET_ORDER);

		// Extract inline token params from the path
		foreach($matches as $match) {
			$whole = $match[0];
			$name = $match[1];

			// If an inline token pattern is found, override the param
			if(isset($match[3])) {
				$this->params[$name] = $match[3];
				$this->path = str_replace($whole, '{:'.$name.'}', $this->path);
			}
			// Else, use a default pattern
			elseif(!isset($this->params[$name])) {
				$this->params[$name] = '([^/]+)';
			}
		}

		// Create the regular expression from the path and param patterns
		$this->regex = $this->path;

		if($this->params) {
			$keys = array();
			$values = array();

			foreach($this->params as $name => $subpattern) {
				if($subpattern[0] != '(') {
					$message = 'Subpattern for param "'.$name.'" must start with "("';
				}
				else {
					$keys[] = '{:'.$name.'}';
					$vals[] = '(?P<'.$name.'>'.substr($subpattern, 1);
				}
			}

			$this->regex = str_replace($keys, $vals, $this->regex);
		}
	}

	/**
	 * Checks if a given path and server values are a match for this Route.
	 *
	 * @param	string	$path	The path to check against this Route.
	 * @return	bool
	 */
	public function is_match($path, array $server) {
		$is_match = $this->is_regex_match($path)
				 && $this->is_method_match($server)
				 && $this->is_secure_match($server);

		if(!$is_match) {
			return false;
		}

		// Populate the path matches into the route values
		foreach($this->matches as $key => $val) {
			if(is_string($key)) {
				$this->values[$key] = $val;
			}
		}
		return true;
	}

	/**
	 * Checks that the path matches the Route regex.
	 *
	 * @param	string	$path	The path to match against.
	 * @return	bool	True on a match, false if not.
	 */
	public function is_regex_match($path) {
		$match = preg_match("#^{$this->regex}$#", $path, $this->matches);

		return $match;
	}

	/**
	 * Checks that the Route `$method` matches the corresponding server value.
	 *
	 * @param	array	$server	A copy of $_SERVER.
	 * @return	bool	True on a match, false if not.
	 */
	public function is_method_match($server) {
		if(isset($this->method)) {
			if(!isset($server['REQUEST_METHOD'])) {
				return false;
			}

			if(!in_array($server['REQUEST_METHOD'], $this->method)) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Checks that the Route `$secure` matches the corresponding server values.
	 *
	 * @param array $server A copy of $_SERVER.
	 * @return bool True on a match, false if not.
	 */
    public function is_secure_match($server) {
		if($this->secure !== null) {
			$is_secure = (isset($server['HTTPS']) && $server['HTTPS'] == 'on')
					  || (isset($server['SERVER_PORT']) && $server['SERVER_PORT'] == 443);

			if($this->secure == TRUE && !$is_secure) {
				return false;
			}

			if($this->secure == FALSE && $is_secure) {
				return false;
			}
		}
		return true;
    }

}

?>
