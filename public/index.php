<?php

if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $file = __DIR__ . $_SERVER['REQUEST_URI'];
    if (is_file($file)) {
        return false;
    }
}

define('__BASE__', realpath(__DIR__ . '/..'));

// App autoloader
spl_autoload_register(function ($class) {
    $class = __BASE__ . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($class)) {
        require_once($class);
    }
});

// Vendor autoloader
require __DIR__ . '/../vendor/autoload.php';

session_start();

// Instantiate the app
$config = \App\Config::getInstance();
$app = new \Slim\App([
    'settings' => $config->get('slim'),
]);

// Set up dependencies
require __BASE__ . '/src/http/dependencies.php';

// Register middleware
require __BASE__ . '/src/http/middleware.php';

// Register routes
require __BASE__ . '/src/http/routes.php';

// Run app
$app->run();
