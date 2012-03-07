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
	public function __construct() {
		// Include the class files needed by the component
		require_once(ALUMINIUM_COMPONENTS.'router/classes/route.php');

		// [Debug log]
		if(function_exists('write_debug_log')) {
			write_debug_log('Router instance created successfully.', 'router');
		}

		// Load the configuration
		$routes = require(APP_CONF.'routes_conf.php');
		$this->routes = array();

		foreach($routes as $route) {
			$this->add($route);
		}

		// [Debug log]
		if(function_exists('write_debug_log')) {
			write_debug_log(count($this->routes).' routes were added from the routes conf file.', 'router');
		}
	}

	/**
	 * Creates a Route from given data and adds it to the list of routes.
	 *
	 * @param	array	An array containing the route data.
	 */
	public function add($data) {
		$data = (array) $data;

		// If path is not set, null or empty, ignore this route
		if(!isset($data['path']) || is_null($data['path']) || empty($data['path'])) {
			return;
		}
		$path = $data['path'];

		if(!isset($data['method']) || is_null($data['method']) || empty($data['method'])) {
			die('Route '.$data['path'].' method is not set, null or empty.');
		}
		$method = $data['method'];

		$params = isset($data['params']) ? $data['params'] : null;
		$values = isset($data['values']) ? $data['values'] : null;

		$route = new Route($path, $method, $params, $values);
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
