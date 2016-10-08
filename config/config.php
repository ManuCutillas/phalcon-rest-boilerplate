<?php

return new Phalcon\Config([

	'application' => [
		'environment' => 'development',
		'controllers' => APP_DIR .'controllers/',
		'library' => APP_DIR .'library/',
		'models' => APP_DIR .'models/',
		'plugins' => APP_DIR .'plugins/',
		'routes' => APP_DIR .'routes/',
		'logs' => APP_DIR .'logs/',
		'base_uri' => '/',
		'debug' => false,
	],

	'database' => [
		'adapter' => 'Mysql',
		'host' => 'localhost',
		'port' => '3306',
		'username' => 'username',
		'password' => 'password',
		'dbname' => 'database',
		'charset' => 'utf8'
	],

]);
