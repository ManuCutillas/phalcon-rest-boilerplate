<?php

$loader = new \Phalcon\Loader;

// Register directories
$loader->registerDirs([
	$config->application->controllers,
	$config->application->library,
	$config->application->models,
	$config->application->plugins,
])->register();



// Composer autoloader
require_once APP_DIR .'vendor/autoload.php';
