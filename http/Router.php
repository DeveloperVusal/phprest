<?php
namespace Http;

class Router {
	/**
	 * Роуты исполняемые
	 * Используются при вызове, например при выполнении POST запроса
	 * 
	 * @static
	 * @var array
	 */
	protected static $app_routes = [];

	/**
	 * Хранилище роутов
	 * 
	 * @static
	 * @var array
	 */
	protected static $app_routes_store = [];


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

		if (array_key_exists($type, $routes)) {
			foreach ($routes[$type] as $route) {
				if ($route['uri'] === $uri) {
					$return = $route;

					break;
				}
			}
		}
		
		if ($return !== false) $return['action']();

		$routesStorage = self::getRoutesStorage();
		$is_store = false;

		foreach ($routesStorage[$type] as $route) {
			if ($route['uri'] === $uri) {
				$is_store = true;
				
				break;
			}
		}

		if ($is_store === false) {
			http_response_code(404);
			echo '404 Not found';
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
	 * Метод добавление роута
	 * 
	 * @param string $uri
	 * @param callable $action
	 * @param string $type
	 * @static
	 * @return void
	 */
	public static function addRoute($uri, $action = null, $type = 'api')
	{
		$routes = self::getRoutes();

		$routes[$type][] = [
			'uri' => $uri,
			'action' => $action
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
		return self::$app_routes;
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
		self::$app_routes = $data;
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
		return self::$app_routes_store;
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
		self::$app_routes_store = $data;
	}
}