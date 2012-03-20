<?php
/**
 * This file contains the route manager (Router) class.
 *
 * @author		Aitor García <aitor.falc@gmail.com>
 * @copyright	2012 Aitor García <aitor.falc@gmail.com>
 * @license		https://github.com/Falc/Aluminium/blob/master/LICENSE Simplified BSD License
 * @package		Aluminium
 * @subpackage	Components
 */

/**
 * The Router manages all the processes related to routes.
 *
 * Stores a list of routes and allows to match a given path against them to find which should be used.
 *
 * @package		Aluminium
 * @subpackage	Components
 */
class Router {
	/**
	 * A list of routes.
	 *
	 * @var array
	 */
	protected $routes;

	/**
	 * Router constructor.
	 *
	 * Gets the routes from the configuration file.
	 */
	public function __construct($routes_file = null) {
		// Default values
		$this->routes = array();

		// Load the routes file, if any
		if(!is_null($routes_file)) {
			$this->load_routes_from_file($routes_file);
		}

		// [Debug log]
		if(function_exists('write_debug_log')) {
			write_debug_log('Router instance created successfully.', 'router');
			write_debug_log(count($this->routes).' routes were added from the routes conf file.', 'router');
		}
	}

	/**
	 * Loads the routes from a file.
	 *
	 * @param	string	$routes_file	Name of the routes file.
	 */
	public function load_routes_from_file($routes_file) {
		// Load the routes file
		$routes = require($routes_file);

		foreach($routes as $route) {
			$this->add($route);
		}
	}

	/**
	 * Creates a Route from given data and adds it to the list of routes.
	 *
	 * @param	array	An array containing the route data.
	 */
	public function add($data) {
		$data = (array) $data;

		// If path is not set
		if(empty($data['path'])) {
			return;
		}

		$path = $data['path'];

		$method = !empty($data['method']) ? $data['method'] : null;
		$params = !empty($data['params']) ? $data['params'] : null;
		$values = !empty($data['values']) ? $data['values'] : null;
		$secure = !empty($data['secure']) ? $data['secure'] : FALSE;

		$route = new Route($path, $method, $params, $values, $secure);
		$this->routes[] = $route;
	}

	/**
	 * Gets a route that matches the given path.
	 *
	 * @param	string	$path	The path to match against.
	 * @param	array	$server	An array copy of $_SERVER.		
	 * @return	mixed	A Route instance that matches the path or null.
	 */
	public function match($path, array $server) {
		// [Debug log]
		if(function_exists('write_debug_log')) {
			write_debug_log('[Request path] '.$path, 'router');
		}

		foreach($this->routes as $route) {
			if($route->is_match($path, $server)) {
				// [Debug log]
				if(function_exists('write_debug_log')) {
					write_debug_log('[Route matched] '.$route->path, 'router');
					write_debug_log('[Request method] '.$server['REQUEST_METHOD'], 'router');

					foreach($route->params as $key=>$value) {
						write_debug_log('[Parameter] '.$key.' => '.$value, 'router');
					}

					foreach($route->values as $key=>$value) {
						write_debug_log('[Value] '.$key.' => '.$value, 'router');
					}
				}
				return $route;
			}
		}

		// [Debug log]
		if(function_exists('write_debug_log')) {
			write_debug_log('No route matches '.$path, 'router');
		}
		return null;
	}

}

?>
