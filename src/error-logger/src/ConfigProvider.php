<?php

namespace ErrorLogger;

use Zend\Stratigility\Middleware\ErrorHandler;
use Psr;

class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     *
     * @return array
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                'delegators' => [
                    ErrorHandler::class => [
                        ErrorLoggerListenerFactory::class,
                    ],
                ],
                'factories' => [
                    Psr\Log\LoggerInterface::class => ZendLoggerFactory::class
                ]
            ]
        ];
    }
}
