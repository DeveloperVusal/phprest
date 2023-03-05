<?php

namespace Core\Facades;

use Core\Facades\Http\Router;
use Core\Http\Request;

abstract class Facade {

    /**
	 * Handle of requests
	 * 
     * @param string $method 
	 * @param string $uri 
	 * @param callable $action 
	 * @access private
	 * @static
	 * @return void
	 */
	private static function handle_request(string $method, string $uri, callable $action = null)
	{
		Router::addRouteStorage($uri, $action);

		$homeDir = Router::normalizeUrlPath($_SERVER['REQUEST_URI'])[1];

		if (Router::parseUrlPath($_SERVER['REQUEST_URI']) === $homeDir.$uri) {
			$request = new Request;

			if (strtolower($request->method) === strtolower($method)) {
				Router::addRoute($uri, $action);
			} else {
				echo json_encode([
					'type' => 'error',
					'message' => 'Invalid method, '.strtoupper($request->method).' must be passed'
				]);
			}
		}
	}

    public static function __callStatic($method, $args)
    {
        return self::handle_request($method, ...$args);
    }
}