<?php
namespace Core\Facades\Database;

use Core\Engine\Database;

class Postgres extends Database {
	
	public static function __constructStatic()
	{
		self::load_config();
		self::connect_db();
	}

	protected static function connect_db(string $variant = null)
	{
		try {
			if (mb_strlen($variant)) $cfg = self::get_config()['connections']['postgresql'][$variant];
			else $cfg = self::get_config()['connections']['postgresql']['pgsql'];
			
			$dsn = $cfg['driver'].':host='.$cfg['host'].';port='.$cfg['port'].';dbname='.$cfg['dbname'].';user='.$cfg['username'].';password='.$cfg['password'];
			self::$dbn = new \PDO($dsn);

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
