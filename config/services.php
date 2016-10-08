<?php

use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\DI\FactoryDefault;
use Phalcon\Logger\Adapter\File as FileAdapter;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Model\Metadata\Memory as MetaData;
use Phalcon\Session\Adapter\Files as SessionAdapter;

$di = new FactoryDefault;
$di->set('config', $config, true);



/*
** Acl
*/

$di->set('acl', function() {
	return new SecurityPlugin;
}, true);



/*
** CORS
*/

$di->set('cors', function() {
	return new CORSPlugin;
}, true);



/*
** Database connections
*/

$di->set('db', function() use ($config) {
	$dbclass = 'Phalcon\Db\Adapter\Pdo\\' . $config->database['adapter'];

	return new $dbclass((array) $config->database);
});



/*
** Session initializer
*/

$di->set('session', function() {
	$session = new SessionAdapter;
	$session->start();

	return $session;
}, true);



/*
** Logging system
*/

if ($config->application->debug) {
	$di->set('logger', function() use ($config) {
		return new FileAdapter($config->application->logs . date('Ymd') .'.log');
	});
}

