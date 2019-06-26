<?php

use Phalcon\Di\FactoryDefault\Cli as CliDI;
use Phalcon\Cli\Console as ConsoleApp;
use Phalcon\Loader;

// Using the CLI factory default services container
$di = new CliDI();

/**
 * Register the autoloader and tell it to register the tasks directory
 */
$loader = new Loader();
$loader->registerNamespaces([
    'Phalcon' => __DIR__.'/library',
    'App\Models' => __DIR__.'/models',
]);
$loader->registerDirs(
    [
        __DIR__ . '/tasks',
        __DIR__ . '/models',
    ]
);

$loader->register();

// Load the configuration file (if any)
$configFile = __DIR__ . '/config/config.php';

if (is_readable($configFile)) {
    $config = include $configFile;

    $di->set('config', $config);
}


$di->setShared('db', function () {
    $config = $this->getConfig();

    $class = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;
    $params = [
        'host'     => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname'   => $config->database->dbname,
        'charset'  => $config->database->charset
    ];

    if ($config->database->adapter == 'Postgresql') {
        unset($params['charset']);
    }

    $connection = new $class($params);

    return $connection;
});


/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->setShared('modelsMetadata', function () {
    return new \Phalcon\Mvc\Model\Metadata\Memory();
});



// Create a console application
$console = new ConsoleApp();

$console->setDI($di);

/**
 * Process the console arguments
 */
$arguments = [];

foreach ($argv as $k => $arg) {
    if ($k === 1) {
        $arguments['task'] = $arg;
    } elseif ($k === 2) {
        $arguments['action'] = $arg;
    } elseif ($k >= 3) {
        $arguments['params'][] = $arg;
    }
}

try {
    // Handle incoming arguments
    $console->handle($arguments);
    exit(0);
} catch (\Phalcon\Exception $e) {
    // Do Phalcon related stuff here
    // ..
    // debug_print_backtrace();
    fwrite(STDERR, $e->getMessage() . PHP_EOL);
    fwrite(STDERR, $e->getTraceAsString() . PHP_EOL);
    exit(1);
} catch (\Throwable $throwable) {
    // debug_print_backtrace();
    fwrite(STDERR, $throwable->getMessage() . PHP_EOL);
    fwrite(STDERR, $throwable->getTraceAsString() . PHP_EOL);
    exit(1);
} catch (\Exception $exception) {
    // debug_print_backtrace();
    fwrite(STDERR, $exception->getMessage() . PHP_EOL);
    fwrite(STDERR, $exception->getTraceAsString() . PHP_EOL);
    exit(1);
}