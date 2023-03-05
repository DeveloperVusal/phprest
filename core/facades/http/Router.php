<?php
namespace Core\Facades\Http;

use Core\Http\Request;

class Router {

	/**
	 * Метод получения пути
	 * 
	 * @param string $path
	 * @param string $type
	 * @static
	 * @return string|boolean
	 */
	public static function getExecute($uri, $type = 'api')
	{
		$routes = self::getRoutes();
		$return = false;

		$uri = self::parseUrlPath($uri);
		list($uri, $homeDir) = self::normalizeUrlPath($uri);
		
		if (array_key_exists($type, $routes)) {
			foreach ($routes[$type] as $route) {
				if ($homeDir.$route['uri'] === $uri) {
					$return = $route;

					break;
				}
			}
		}
		
		if ($return !== false) {
			if (is_callable($return['action'])) {
				return call_user_func($return['action'], $return['args']);
			}
		}

		$routesStorage = self::getRoutesStorage();
		$is_store = false;

		foreach ($routesStorage[$type] as $route) {
			if ($homeDir.$route['uri'] === $uri) {
				$is_store = true;
				
				break;
			}
		}

		if ($is_store === false) {
			http_response_code(404);
			echo "\n404 Not found";
		}
	}

	/**
	 * Метод парсинга url 
	 * 
	 * @param string $uri
	 * @static
	 * @return string
	 */
	public static function parseUrlPath($uri) {
		$pth = parse_url($uri);

		return $pth['path'];
	}

	/**
	 * Метод нормализации url 
	 * 
	 * @param string $uri
	 * @static
	 * @return array
	 */
	public static function normalizeUrlPath($uri) {
		$fullPath = __DIR__;
		$partsPath = explode('/', str_replace('\\', '/', $fullPath));
		$partsPath = array_slice($partsPath, 0, sizeof($partsPath) - 3);
		$homeDir = preg_match('/'.end($partsPath).'/iu', $uri, $matches) ? '/'.end($partsPath) : '';

		return [$uri, $homeDir];
	}


	/**
	 * Метод добавление роута
	 * 
	 * @param string $uri
	 * @param \Closure|callable|array $action
	 * @param string $type
	 * @static
	 * @return void
	 */
	public static function addRoute(string $uri, \Closure|callable|array $action = null, string $type = 'api')
	{
		$routes = self::getRoutes();

		$routes[$type][] = [
			'uri' => $uri,
			'action' => $action,
			'args' => [],
		];

		self::saveRoute($routes);
	}

	/**
	 * Метод получения роутов
	 * 
	 * @static
	 * @return array
	 */
	private static function getRoutes()
	{
		return $GLOBALS['APP_ROUTES'];
	}

	/**
	 * Метод сохранения роута
	 * 
	 * @param array $data
	 * @static
	 * @return void
	 */
	private static function saveRoute($data)
	{
		$GLOBALS['APP_ROUTES'] = $data;
	}


	/**
	 * Метод добавление роута в хранилище
	 * 
	 * @param string $uri
	 * @param callable $action
	 * @param string $type
	 * @static
	 * @return void
	 */
	public static function addRouteStorage($uri, $action = null, $type = 'api')
	{
		$routes = self::getRoutesStorage();

		$routes[$type][] = [
			'uri' => $uri,
			'action' => $action
		];

		self::saveRouteStorage($routes);
	}

	/**
	 * Метод получения хранилища роутов
	 * 
	 * @static
	 * @return array
	 */
	private static function getRoutesStorage()
	{
		return $GLOBALS['APP_ROUTES_STORE'];
	}

	/**
	 * Метод сохранения хранилища роута
	 * 
	 * @param array $data
	 * @static
	 * @return void
	 */
	private static function saveRouteStorage($data)
	{
		$GLOBALS['APP_ROUTES_STORE'] = $data;
	}
}