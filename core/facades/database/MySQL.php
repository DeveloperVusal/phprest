<?php
namespace Core\Facades\Database;

use Core\Engine\Database;

class MySQL extends Database {
	
	public static function __constructStatic()
	{
		self::load_config();
		self::connect_db();
	}

	protected static function connect_db(string $variant = null)
	{
		try {
			if (mb_strlen($variant)) $cfg = self::get_config()['connections']['mysql'][$variant];
			else $cfg = self::get_config()['connections']['mysql']['mysql'];

			$dsn = 'mysql:host='.$cfg['host'].';port='.$cfg['port'].';dbname='.$cfg['dbname'];
			self::$dbn = new \PDO($dsn, $cfg['username'], $cfg['password']);

			return self::$dbn;
		} catch (\PDOException $e) {
			return $e->getMessage();
		}
	}

	public static function connection(string $variant)
	{
		self::connect_db($variant);
	}
}