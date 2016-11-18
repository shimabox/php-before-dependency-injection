<?php

namespace App\Database;

/**
 * Pdo
 */
class Pdo
{
	/**
	 * @var PDO
	 */
	private static $pdo;
	
	/**
	 * @return \PDO
	 */
	public static function forge()
	{
		if(!self::$pdo) {
			self::$pdo = new \PDO(getenv('DB_DSN'), getenv('DB_USER'), getenv('DB_PASS'));
			self::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			self::$pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
		}

		return self::$pdo;
	}

}
