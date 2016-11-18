<?php
/*
 |--------------------------------------------------------------------
 | phpunit bootstrap
 |--------------------------------------------------------------------
 */
require_once realpath(__DIR__ . '/../vendor') . '/autoload.php';

// .env
use Dotenv\Dotenv;

$env = new Dotenv(realpath(__DIR__ . '/config'));
$env->load();