<?php
namespace Core\Engine;

abstract class Database {
    /**
	 * Конфиг БД для подключения
	 * 
	 * @var array
	 * @access protected
	 * @static
	 */
	protected static $dbconfig;

	/**
	 * Линк PDO
	 * 
	 * @var object
	 * @access protected
	 * @static
	 */
	protected static $dbn;

	/**
	 * Метод статического конструктора
	 * Используется для авторегистра классов
	 * 
	 * @access public
	 * @static
	 * @return void
	 */
	abstract public static function __constructStatic();

	/**
	 * Метод загружает конфиг БД
	 * 
	 * @access public
	 * @static
	 * @return void
	 */
	public static function load_config()
	{
		self::$dbconfig = include __DIR__.'/../../config/database.php';
	}

	/**
	 * Метод получения конфига БД
	 * 
	 * @access public
	 * @static
	 * @return array
	 */
	public static function get_config()
	{
		return self::$dbconfig;
	}

	/**
	 * Метод соединения с БД
	 * 
	 * @access protected
	 * @static
	 * @return object|string
	 */
	abstract protected static function connect_db();

	/**
	 * Метод варианта соединения с БД
	 * 
	 * @param string $variant
	 * @access public
	 * @static
	 * @return object|string
	 */
	abstract public static function connection(string $variant);

	/**
	 * Метод выполнения подготовленных запросов
	 * 
	 * @param string $query SQL запрос для PDO
	 * @param array $options Свойтсва используемые в запросе
	 * @access public
	 * @static
	 * @return object|boolean|string Объект PDOStatement
	 */
	public static function prepare($query, $options = [])
	{
		try {
			$sth = self::$dbn->prepare($query);
			$is_q = false;

			$is_q = ($sth->execute($options)) ? true : false;

			return ($is_q) ? $sth : false;
		} catch (\PDOException $e) {
			return $e->getMessage();
		}
	}

	/**
	 * Метод получения ID последне добавленной записи
	 * 
	 * @access public
	 * @static
	 * @return integer|string
	 */
	public static function lastInsertId()
	{
		try {
			return self::$dbn->lastInsertId();
		} catch (\PDOException $e) {
			return $e->getMessage();
		}
	}

	/**
	 * Метод выполнения запроса без подгатовленного запроса
	 * 
	 * @param string $query SQL запрос для PDO
	 * @param integer $fetchMode Режим выборки для результата. Должен быть одной из констант PDO::FETCH_*
	 * @access public
	 * @static
	 * @return object|false Объект PDOStatement|false
	 */
	public static function query($query, $fetchMode = null)
	{
		return self::$dbn->query($query, $fetchMode);
	}

	/**
	 * Метод Заключает строку в кавычки для использования в запросе
	 * 
	 * @param string $string Экранируемая строка.
	 * @param integer $type Предоставляет подсказку о типе данных для драйверов, которые имеют альтернативные способы экранирования
	 * @access public
	 * @static
	 * @return string|false Строка|false
	 */
	public static function quote($string, $type = null)
	{
		return self::$dbn->quote($string, $type);
	}

	/**
	 * Метод получения всех записей из результата PDO
	 * 
	 * @param object $sth Объект PDOStatement
	 * @param string $type Режим выборки для результата.
	 * @access public
	 * @static
	 * @return mixed
	 */
	public static function getAll($sth, $type = 'asssoc')
	{
		$data = [];

		switch ($type) {
			case 'asssoc':
				if ($sth->rowCount() > 1) {
					while ($row = $sth->fetch(\PDO::FETCH_ASSOC)) $data[] = $row;
				} else {
					$data = $sth->fetch(\PDO::FETCH_ASSOC);
				}

				break;
			case 'both':
				if ($sth->rowCount() > 1) {
					while ($row = $sth->fetch(\PDO::FETCH_BOTH)) $data[] = $row;
				} else {
					$data = $sth->fetch(\PDO::FETCH_BOTH);
				}

				break;
			case 'num':
				if ($sth->rowCount() > 1) {
					while ($row = $sth->fetch(\PDO::FETCH_NUM)) $data[] = $row;
				} else {
					$data = $sth->fetch(\PDO::FETCH_NUM);
				}

				break;
			case 'object':
				if ($sth->rowCount() > 1) {
					while ($row = $sth->fetch(\PDO::FETCH_OBJ)) $data[] = $row;
				} else {
					$data = $sth->fetch(\PDO::FETCH_OBJ);
				}

				break;
		}

		return $data;
	}


    /**
	 * Завершение сеанса
	 * 
	 * @access public
	 * @return void
	 */
	public function __destruct()
	{
		# self::$dbn = null;
	}
}