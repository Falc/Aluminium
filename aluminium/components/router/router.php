<?php
/**
 * This file contains the route manager (Router) class.
 *
 * @author		Aitor García <aitor.falc@gmail.com>
 * @copyright	2012 Aitor García <aitor.falc@gmail.com>
 * @license		https://github.com/Falc/Aluminium/blob/master/LICENSE New BSD License
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
	private $routes;

	/**
	 * Router constructor.
	 *
	 * Gets the routes from the config file.
	 */
	public function __construct() {
		// Require the files needed by the component
		require_once(ALUMINIUM_COMPONENTS.'router/router.php');
		require_once(ALUMINIUM_COMPONENTS.'router/route.php');

		// Load the configuration
		$routes = require_once(APP_CONFIG.'routes_conf.php');
		$this->routes = array();

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
		foreach($this->routes as $route) {
			if($route->is_match($path, $server)) {
				return $route;
			}
		}

		return null;
	}

}

?>
