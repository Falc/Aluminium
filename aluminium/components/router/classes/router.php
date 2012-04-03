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
		if(defined('DEBUG_FILE')) {
			$output = "\n".'[router->num_routes_loaded] '.count($this->routes);
			file_put_contents(DEBUG_FILE, $output, FILE_APPEND);
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
		if(defined('DEBUG_FILE')) {
			$output = "\n".'[router->request_path] '.$path;
			file_put_contents(DEBUG_FILE, $output, FILE_APPEND);
		}

		foreach($this->routes as $route) {
			if($route->is_match($path, $server)) {
				// [Debug log]
				if(defined('DEBUG_FILE')) {
					$output = "\n".'[router->route_matched] True';
					$output .= "\n".'[router->route_match->path] '.$route->path;
					$output .= "\n".'[router->route_match->request_method] '.$server['REQUEST_METHOD'];

					foreach($route->params as $key=>$value) {
						$output .= "\n".'[router->route_match->parameter] '.$key.' => '.$value;
					}

					foreach($route->values as $key=>$value) {
						$output .= "\n".'[router->route_match->value] '.$key.' => '.$value;
					}

					file_put_contents(DEBUG_FILE, $output, FILE_APPEND);
				}

				return $route;
			}
		}

		// [Debug log]
		if(defined('DEBUG_FILE')) {
			$output = "\n".'[router->route_matched] False';
			file_put_contents(DEBUG_FILE, $output, FILE_APPEND);
		}

		return null;
	}

}

?>
