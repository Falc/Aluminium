<?php
/**
 * This file contains the route manager (Router) class.
 *
 * @author		Aitor García <aitor.falc@gmail.com>
 * @copyright	2012 Aitor García <aitor.falc@gmail.com>
 * @license		https://github.com/Falc/Aluminium/blob/master/LICENSE Simplified BSD License
 */
namespace Aluminium\Component\Router;

/**
 * The Router manages all the processes related to routes.
 *
 * Stores a list of routes and allows to match a given path against them to find which should be used.
 */
class Router {
	/**
	 * The loaded routes.
	 *
	 * @var array
	 */
	protected $routes = array();

	/**
	 * Router constructor.
	 *
	 * Gets the routes from the configuration file.
	 */
	public function __construct($routes_file = null) {
		// Load the routes file, if any
		if(!empty($routes_file)) {
			$this->load_routes_from_file($routes_file);
		}

		// [Debug log]
		if(defined('DEBUG_FILE')) {
			$output = '[router->num_routes_loaded] '.count($this->routes)."\n";
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
	public function add($route_data) {
		$route_data = (array) $route_data;

		// If the path is not set, discard the route
		if(empty($route_data['path'])) {
			return;
		}

		// Get the route data
		$path = $route_data['path'];
		$method = !empty($route_data['method']) ? $route_data['method'] : null;
		$params = !empty($route_data['params']) ? $route_data['params'] : null;
		$values = !empty($route_data['values']) ? $route_data['values'] : null;
		$secure = !empty($route_data['secure']) ? $route_data['secure'] : FALSE;
		
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
			$output = '[router->request_path] '.$path."\n";
			file_put_contents(DEBUG_FILE, $output, FILE_APPEND);
		}

		foreach($this->routes as $route) {
			if($route->is_match($path, $server)) {
				// [Debug log]
				if(defined('DEBUG_FILE')) {
					$output = '[router->route_matched] TRUE'."\n";
					$output .= '[router->route_match->path] '.$route->path."\n";
					$output .= '[router->route_match->request_method] '.$server['REQUEST_METHOD']."\n";

					foreach($route->params as $key=>$value) {
						$output .= '[router->route_match->parameter] '.$key.' => '.$value."\n";
					}

					foreach($route->values as $key=>$value) {
						$output .= '[router->route_match->value] '.$key.' => '.$value."\n";
					}

					file_put_contents(DEBUG_FILE, $output, FILE_APPEND);
				}

				return $route;
			}
		}

		// [Debug log]
		if(defined('DEBUG_FILE')) {
			$output = '[router->route_matched] FALSE'."\n";
			file_put_contents(DEBUG_FILE, $output, FILE_APPEND);
		}

		return null;
	}
}
?>
