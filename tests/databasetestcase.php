<?php
/**
 * DatabaseTestCase
 */
abstract class DatabaseTestCase extends \PHPUnit_Extensions_Database_TestCase
{
	/**
	 * @var PDO
	 */
	static protected $pdo = null;

	/**
	 * @var PHPUnit_Extensions_Database_DB_IDatabaseConnection
	 */
	protected $conn = null;

	protected function setUp()
	{
		parent::setUp();
	}

	protected function tearDown()
	{
		parent::tearDown();
	}

	/**
	 * tearDownでもcleanup(TRUNCATE)する
	 *
	 * @see PHPUnit_Extensions_Database_TestCase::getTearDownOperation()
	 * @return \PHPUnit_Extensions_Database_Operation_DatabaseOperation
	 */
	protected function getTearDownOperation()
	{
		return \PHPUnit_Extensions_Database_Operation_Factory::TRUNCATE();
	}

	/**
	 * DBの初期化処理です
	 * 各々実装してください
	 *
	 * @see PHPUnit_Extensions_Database_TestCase::getDataSet()
	 */
	protected function getDataSet() {}

	/**
	 * (non-PHPdoc)
	 * @see PHPUnit_Extensions_Database_TestCase::getConnection()
	 */
	public function getConnection()
	{
		if ($this->conn === null) {
			$DB_DSN  = getenv('DB_DSN') ? getenv('DB_DSN') : $GLOBALS['DB_DSN'];
			$DB_USER = getenv('DB_USER') ? getenv('DB_USER') : $GLOBALS['DB_USER'];
			$DB_PASS = getenv('DB_PASS') ? getenv('DB_PASS') : $GLOBALS['DB_PASS'];

			if (self::$pdo == null) {
				self::$pdo = new \PDO($DB_DSN, $DB_USER, $DB_PASS);
				self::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
				self::$pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
			}

			$this->conn = $this->createDefaultDBConnection(self::$pdo);
		}

		return $this->conn;
	}

	/**
	 * データを作る
	 *
	 * @param string $file
	 */
	protected function createData($file)
	{
		$dataSet = $this->createExpectedData($file);
		$this->getDatabaseTester()->setDataSet($dataSet);
		$this->getDatabaseTester()->onSetUp();
	}

	/**
	 * 期待値を作る
	 *
	 * @param string $file
	 * @return \PHPUnit_Extensions_Database_DataSet_YamlDataSet
	 */
	protected function createExpectedData($file) {
		return new \PHPUnit_Extensions_Database_DataSet_YamlDataSet(
			constant('FIXTURE_FILE_PATH') . $file
		);
	}

	/**
	 * データを作る(CSVファイル用)
	 *
	 * @param string $tableName
	 * @param string $file
	 */
	protected function createDataByCsv($tableName, $file)
	{
		$dataSet = $this->createExpectedDataByCsv($tableName, $file);
		$this->getDatabaseTester()->setDataSet($dataSet);
		$this->getDatabaseTester()->onSetUp();
	}

	/**
	 * 期待値を作る(CSVファイル用)
	 *
	 * @param string $tableName
	 * @param string $file
	 * @return \PHPUnit_Extensions_Database_DataSet_CsvDataSet
	 */
	protected function createExpectedDataByCsv($tableName, $file)
	{
		$dataSet = new \PHPUnit_Extensions_Database_DataSet_CsvDataSet();
		$dataSet->addTable($tableName, constant('FIXTURE_FILE_PATH') . $file);
		return $dataSet;
	}
}
