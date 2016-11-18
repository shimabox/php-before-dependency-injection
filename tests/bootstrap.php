<?php
/*
 |--------------------------------------------------------------------
 | phpunit bootstrap
 |--------------------------------------------------------------------
 */
require_once realpath(__DIR__ . '/../src') . '/bootstrap.php';
require_once realpath(__DIR__) . '/databasetestcase.php';

// _fileのパス
define('FIXTURE_FILE_PATH', dirname(__FILE__) . '/_files');