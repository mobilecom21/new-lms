<?php

// Unlimited memory usage of server
ini_set('memory_limit', '-1');

//maintenance mode switch
$MAINTENANCE = false;

//if maintenance mode is on AND the remote IP isn't NCC
if(true === $MAINTENANCE && $_SERVER['REMOTE_ADDR'] != '164.39.170.2') {
    echo file_get_contents('maintainance.html');
    die();
}

// Delegate static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server'
    && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))
) {
    return false;
}

chdir(dirname(__DIR__));
require 'vendor/autoload.php';

/**
 * Self-called anonymous function that creates its own scope and keep the global namespace clean.
 */
call_user_func(function () {
    /** @var \Interop\Container\ContainerInterface $container */
    $container = require 'config/container.php';

    /** @var \Zend\Expressive\Application $app */
    $app = $container->get(\Zend\Expressive\Application::class);

    // Import programmatic/declarative middleware pipeline and routing
    // configuration statements
    require 'config/pipeline.php';
    require 'config/routes.php';

    $app->run();
});
