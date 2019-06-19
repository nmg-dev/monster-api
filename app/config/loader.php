<?php

$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerNamespaces([
	'Phalcon' => APP_PATH.'/library/',
	'App\Models' => APP_PATH.'/models',
	// 'App\Controllers' => APP_PATH.'/controllers',

]);
$loader->registerDirs(
    [
        $config->application->controllersDir,
        $config->application->modelsDir
    ]
)->register();
