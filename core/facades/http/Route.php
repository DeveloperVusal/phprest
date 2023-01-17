<?php
namespace Core\Facades\Http;

use Core\Http\Request;
use Core\Http\Router;

class Route {
	/**
	 * Метод обработки GET запроса
	 * 
	 * @param string $uri 
	 * @param callable $action 
	 * @access public
	 * @static
	 * @return void
	 */
	public static function get($uri, $action = null)
	{
		Router::addRouteStorage($uri, $action);

		if (Router::parseUrlPath($_SERVER['REQUEST_URI']) === $uri) {
			$request = new Request;

			if ($request->method === 'GET') {
				Router::addRoute($uri, $action);
			} else {
				echo json_encode([
					'type' => 'error',
					'message' => 'Invalid method, GET must be passed'
				]);
			}
		}
	}

	/**
	 * Метод обработки POST запроса
	 * 
	 * @param string $uri 
	 * @param callable $action 
	 * @access public
	 * @static
	 * @return void
	 */
	public static function post($uri, $action = null)
	{
		Router::addRouteStorage($uri, $action);

		if (Router::parseUrlPath($_SERVER['REQUEST_URI']) === $uri) {
			$request = new Request;

			if ($request->method === 'POST') {
				Router::addRoute($uri, $action);
			} else {
				echo json_encode([
					'type' => 'error',
					'message' => 'Invalid method, POST must be passed'
				]);
			}
		}
	}

	/**
	 * Метод обработки PUT запроса
	 * 
	 * @param string $uri 
	 * @param callable $action 
	 * @access public
	 * @static
	 * @return void
	 */
	public static function put($uri, $action = null)
	{
		Router::addRouteStorage($uri, $action);

		if (Router::parseUrlPath($_SERVER['REQUEST_URI']) === $uri) {
			$request = new Request;

			if ($request->method === 'PUT') {
				Router::addRoute($uri, $action);
			} else {
				echo json_encode([
					'type' => 'error',
					'message' => 'Invalid method, PUT must be passed'
				]);
			}
		}
	}

	/**
	 * Метод обработки PATCH запроса
	 * 
	 * @param string $uri 
	 * @param callable $action 
	 * @access public
	 * @static
	 * @return void
	 */
	public static function patch($uri, $action = null)
	{
		Router::addRouteStorage($uri, $action);

		if (Router::parseUrlPath($_SERVER['REQUEST_URI']) === $uri) {
			$request = new Request;

			if ($request->method === 'PATCH') {
				Router::addRoute($uri, $action);
			} else {
				echo json_encode([
					'type' => 'error',
					'message' => 'Invalid method, PATCH must be passed'
				]).PHP_EOL;
			}
		}
	}

	/**
	 * Метод обработки DELETE запроса
	 * 
	 * @param string $uri 
	 * @param callable $action 
	 * @access public
	 * @static
	 * @return void
	 */
	public static function delete($uri, $action = null)
	{
		Router::addRouteStorage($uri, $action);

		if (Router::parseUrlPath($_SERVER['REQUEST_URI']) === $uri) {
			$request = new Request;

			if ($request->method === 'DELETE') {
				Router::addRoute($uri, $action);
			} else {
				echo json_encode([
					'type' => 'error',
					'message' => 'Invalid method, DELETE must be passed'
				]);
			}
		}
	}
}