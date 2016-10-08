<?php

error_reporting(E_ALL);
date_default_timezone_set('Europe/Madrid');

use Phalcon\Mvc\Micro;
use Phalcon\Events\Manager as EventsManager;

define('APP_DIR', dirname(__DIR__) .'/');

try {

	$config = require APP_DIR .'config/config.php';
	require APP_DIR .'config/loader.php';
	require APP_DIR .'config/services.php';

	$em = new EventsManager;

	if ($config->application->environment == 'development')
		$em->attach('micro:beforeHandleRoute', $di->get('cors'));

	$em->attach('micro:beforeExecuteRoute', $di->get('acl'));

	$app = new Micro($di);
	$app->setEventsManager($em);

	$app->notFound(function() use ($app) {
		$app->response->setStatusCode(404, 'Not Found')->sendHeaders();
		echo 'Request not found';
	});

	foreach (glob($config->application->routes .'*.php') as $route)
		include $route;

	$app->handle();
	$app->response->send();

} catch (Exception $e) {

	if ($config->application->environment == 'development') {
		echo $e->getMessage();
		echo '<br><br><pre>';
		echo nl2br(htmlentities($e->getTraceAsString()));
		echo '</pre>';

	} else {
		echo 'Ooops';
	}

}
