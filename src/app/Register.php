<?php

namespace App;

use App\Database;

/**
 * Register
 */
class Register
{
	/**
	 * 登録実行
	 * @param string $name
	 */
	public function execute($name)
	{
		$sql = <<<EOS
			insert into sample 
				(name) 
			values
				(:name)
			;
EOS;
		
		$stmt = Database\Pdo::forge()->prepare($sql);
		$stmt->bindValue(':name', $name, \PDO::PARAM_STR);
		$stmt->execute();
	}
}
