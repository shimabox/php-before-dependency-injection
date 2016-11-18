<?php

namespace App;

use App\Database;

/**
 * Something
 */
class Something
{
	/**
	 * コンストラクタ
	 */
	public function __construct()
	{
	}

	/**
	 * 何かしらの登録処理
	 * @param array $params
	 * @return boolean
	 * @throws \InvalidArgumentException
	 * @throws \Exception
	 */
	public function register(array $params)
	{
		// オブジェクトの生成を別メソッドで
		$validation = $this->createValidation();

		if ($validation->run($params) === false) {
			throw new \InvalidArgumentException('引数エラー');
		}

		try {
			$pdo = Database\Pdo::forge();
			$pdo->beginTransaction();

			// オブジェクトの生成を別メソッドで
			$register = $this->createRegister();

			foreach ($params as $param) {
				$register->execute($param);
			}

			$pdo->commit();

		} catch (\Exception $e) {

			if ($pdo instanceof \PDO) {
				$pdo->rollback();
			}

			throw $e;
		}

		return true;
	}

	/**
	 * \App\Validation生成
	 * @return \App\Validation
	 */
	protected function createValidation()
	{
		return new Validation();
	}

	/**
	 * \App\Register生成
	 * @return \App\Register
	 */
	protected function createRegister()
	{
		return new Register();
	}
}
