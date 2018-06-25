<?php

namespace ErrorLogger;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Zend\Stratigility\Middleware\ErrorHandler;

class ErrorLoggerListenerFactory
{
    public function __invoke(ContainerInterface $container, string $name, callable $callback) : ErrorHandler
    {
        $logger = $container->get(LoggerInterface::class);
        $listener = new ErrorLoggerListener($logger);
        $errorHandler = $callback();
        $errorHandler->attachListener($listener);
        return $errorHandler;
    }
}