<?php

use App\Something;

/**
 * test of Something
 * @group sample
 */
class SomethingTest extends \DatabaseTestCase
{
	/**
	 * @var string
	 */
	protected $fixtureFilePath = '/app/something';

	/**
	 * (non-PHPdoc)
	 * @see PHPUnit_Extensions_Database_TestCase::getDataSet()
	 */
	protected function getDataSet() {
		return new \PHPUnit_Extensions_Database_DataSet_YamlDataSet(
			FIXTURE_FILE_PATH . $this->fixtureFilePath . '/setup.yml'
		);
	}

	/*
	 |--------------------------------------------------------------------------
	 | 正常系
	 |--------------------------------------------------------------------------
	 */

	/**
	 * registerで正しく登録できている
	 * @test
	 */
	public function registerで正しく登録できている()
	{
		// 期待値
		$expected = $this->createExpectedData($this->fixtureFilePath . '/expected_1.yml')
						->getTable('sample')
						;

		// 処理実行
		$target = new Something();
		$result = $target->register(['test1', 'test2']);

		// 処理後の値
		$actual = $this->getConnection()->createQueryTable(
			'sample',
<<<EOS
			select
				id
				, name
			from
				sample
EOS
		);

		$this->assertTablesEqual($expected, $actual);
		$this->assertTrue($result);
	}

	/*
	 |--------------------------------------------------------------------------
	 | 準異常系
	 |--------------------------------------------------------------------------
	 */

	/**
	 * validationでエラーになったらInvalidArgumentException
	 * @test
	 */
	public function validationでエラーになったらInvalidArgumentException()
	{
		// Validationクラスのモック
		$validationMock = $this->getMockBuilder('App\Validation')
								->setMethods(['run'])
								->getMock();
		// runメソッドでfalseを返すようにする
		$validationMock
			->expects($this->once())
			->method('run')
			->willReturn(false)
			;

		// テスト対象クラスを上記で作ったモックを返却するモックにする
		$targetMock = $this->getMockBuilder('App\Something')
							->setMethods(['createValidation', 'createRegister'])
							->getMock();
		$targetMock
			->expects($this->once())
			->method('createValidation')
			->willReturn($validationMock)
			;

		// createRegisterは呼ばれていないこと
		$targetMock
			->expects($this->never())
			->method('createRegister')
			->willReturn('')
			;

		// 期待する例外
		$expected_error_message = '引数エラー'; // 例外時のメッセージ
		$this->setExpectedException(
			'\InvalidArgumentException', $expected_error_message
		);

		// 実行
		$targetMock->register([]); // 引数はarrayであればなんでもいい
	}

	/*
	 |--------------------------------------------------------------------------
	 | 異常系
	 |--------------------------------------------------------------------------
	 */

	/**
	 * Registerのexecuteで処理に失敗した場合ロールバックされている
	 * @test
	 */
	public function Registerのexecuteで処理に失敗した場合ロールバックされている()
	{
		// Registerクラスのモック
		$registerMock = $this->getMockBuilder('App\Register')
								->setMethods(['execute'])
								->getMock();
		// executeメソッドでExceptionを返すようにする
		$registerMock
			->expects($this->atLeastOnce())
			->method('execute')
			->will($this->throwException(new \Exception))
			;

		// テスト対象クラスを上記で作ったモックを返却するモックにする
		$targetMock = $this->getMockBuilder('App\Something')
							->setMethods(['createRegister'])
							->getMock();
		$targetMock
			->expects($this->once())
			->method('createRegister')
			->willReturn($registerMock)
			;

		// 2件データを投入しておく
		$this->createData($this->fixtureFilePath . '/fixture_2.yml');

		// この時点では2件ある
		$this->assertEquals(2, $this->getConnection()->getRowCount('sample'));

		try {
			// 実行
			$targetMock->register(['123', 'abc']);

			$this->fail('fail not exception'); // 例外が発生しなかったときはfail

		} catch (\Exception $e) { // Exceptionが発生するはずだ。そうだろ？兄弟？

			// テーブル残っているよね
			$this->assertEquals(2, $this->getConnection()->getRowCount('sample'));

			// 処理後の期待値
			$expected = $this->createExpectedData($this->fixtureFilePath . '/expected_2.yml')
						->getTable('sample')
						;

			// 処理後の値
			$actual = $this->getConnection()->createQueryTable(
			'sample',
<<<EOS
			select
				*
			from
				sample
EOS
		);

			$this->assertTablesEqual($expected, $actual);

			// 以降はスキップ
			return;
		}
	}

	/**
	 * Registerのexecuteで複数回の登録処理途中で処理に失敗した場合ロールバックされている
	 * @test
	 */
	public function Registerのexecuteで複数回の登録処理途中で処理に失敗した場合ロールバックされている()
	{
		// Registerクラスのモック
		$registerMock = $this->getMockBuilder('App\Register')
								->setMethods(['execute'])
								->getMock();
		// executeメソッドでPDOExceptionを返すようにする
		$registerMock
			->expects($this->any())
			->method('execute')
			// メソッドに、リストで指定した値をその順で返させる
			// App\Register::execute() 3回目の呼び出しでPDOExceptionを発生させる
			->will($this->onConsecutiveCalls(
				null,
				null,
				$this->throwException(new \PDOException)
			));

		// テスト対象クラスを上記で作ったモックを返却するモックにする
		$targetMock = $this->getMockBuilder('App\Something')
					 ->setMethods(['createRegister'])
					 ->getMock();
		$targetMock
			->expects($this->once())
			->method('createRegister')
			->willReturn($registerMock)
			;

		// 2件データを投入しておく
		$this->createData($this->fixtureFilePath . '/fixture_2.yml');

		// この時点では2件ある
		$this->assertEquals(2, $this->getConnection()->getRowCount('sample'));

		try {
			// 実行 3個以上の値を持つ配列を渡す
			$targetMock->register(['123', 'abc', 'hoge']);

			$this->fail('fail not exception'); // 例外が発生しなかったときはfail

		} catch (\PDOException $e) { // PDOExceptionが発生するはずだ。そうだろ？兄弟？

			// テーブル残っているよね
			$this->assertEquals(2, $this->getConnection()->getRowCount('sample'));

			// 処理後の期待値
			$expected = $this->createExpectedData($this->fixtureFilePath . '/expected_2.yml')
						->getTable('sample')
						;

			// 処理後の値
			$actual = $this->getConnection()->createQueryTable(
			'sample',
<<<EOS
			select
				*
			from
				sample
EOS
		);

			$this->assertTablesEqual($expected, $actual);

			// 以降はスキップ
			return;
		}
	}
}