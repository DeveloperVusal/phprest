<?php
namespace Core\Http;

class Request {
	/**
	 * Переданный метод в запросе
	 * 
	 * @access public
	 * @var string
	 */
	public $method = 'GET';

	/**
	 * Конструктор
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		if ($_SERVER['REQUEST_METHOD']) $this->method = strtoupper($_SERVER['REQUEST_METHOD']);
	}

	/**
	 * Метод получения тело запроса
	 * 
	 * @access public
	 * @return array
	 */
	public function all()
	{
		return $this->body();
	}

	/**
	 * Метод получения значения свойства из тело запроса
	 * 
	 * @param string $key Ключ необходимого свойства
	 * @access public
	 * @return integer|string|array|bool
	 */
	public function field($key)
	{
		$body = $this->body();

		return isset($body[$key]) ? $body[$key] : false;
	}

	/**
	 * Метод получения куков
	 * 
	 * @param string $name Наименование кука (ключа)
	 * @access public
	 * @return string|false
	 */
	public function cookie_http($name)
	{
		$name = str_replace('.', '_', $name);

		return ($_COOKIE[$name]) ? $_COOKIE[$name] : false;
	}

	/**
	 * Метод получения IP адреса
	 * 
	 * @access public
	 * @return string
	 */
	public function ip()
	{
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		    return $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		    return $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
		    return $_SERVER['REMOTE_ADDR'];
		}
	}

	/**
	 * Метод возвращает ссылку источник откуда пришел запрос
	 * 
	 * @access public
	 * @return string
	 */
	public function referer()
	{
		return $_SERVER['HTTP_REFERER'];
	}

	/**
	 * Метод возвращает User-Agent
	 * 
	 * @access public
	 * @return string
	 */
	public function user_agent()
	{
		return $_SERVER['HTTP_USER_AGENT'];
	}

	/**
	 * Метод возвращает все заголовки
	 * 
	 * @access public
	 * @return string
	 */
	public function headers()
	{
		return apache_request_headers();
	}

	/**
	 * Метод возвращает значение конкретного заголовка
	 * 
	 * @param string $name Название свойтсва заголовка
	 * @access public
	 * @return string
	 */
	public function header($name)
	{
		$data = $this->headers();

		return $data[$name];
	}

	/**
	 * Метод возвращает строку из адресной строки браузера
	 * 
	 * @access public
	 * @return string
	 */
	public function query_uri()
	{
		return $_SERVER['REQUEST_URI'];
	}


	// Privates functions

	/**
	 * Метод опредления и формирования тело запроса в массив
	 * 
	 * @access private
	 * @return array
	 */
	private function body()
	{
		if (sizeof($_POST) || sizeof($_GET)) {
			if ($this->method === 'GET') return $_GET;
			if ($this->method === 'POST') return $_POST;
		} else {
			$reqData = file_get_contents('php://input');

			if ($this->is_json($reqData)) {
				return json_decode($reqData, true);
			} else {
				$data = [];
				$expl = explode('&', file_get_contents('php://input'));

				foreach ($expl as $pair) {
					$item = explode('=', $pair);

					if (count($item) === 2) $data[urldecode($item[0])] = urldecode($item[1]);
				}

				return $data;
			}
		}
	}

	/**
	 * Метод проверять на действительно json строки
	 * 
	 * @param string $str JSON строка
	 * @access private
	 * @return boolean
	 */
	private function is_json($str) {
		json_decode($str, true);

		return (json_last_error() == JSON_ERROR_NONE);
	}
}