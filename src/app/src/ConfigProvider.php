<?php

namespace App;

use App\Action\HomePageAction;
use App\Action\PingAction;
use Rbac\Role;

/**
 * The configuration provider for the App module
 *
 * @see https://docs.zendframework.com/zend-component-installer/
 */
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
            'dependencies' => $this->getDependencies(),
            'routes' => $this->getRoutes(),
            'rbac' => $this->getRbac()
        ];
    }

    /**
     * Returns the container dependencies
     *
     * @return array
     */
    public function getDependencies(): array
    {
        return [
            'invokables' => [
                Action\PingAction::class => Action\PingAction::class,
            ],
            'factories' => [
                Action\HomePageAction::class => Action\HomePageFactory::class,
            ],
        ];
    }

    /**
     * Returns the routes array
     *
     * @return array
     */
    public function getRoutes(): array
    {
        return [
            [
                'name' => 'home',
                'path' => '/',
                'middleware' => HomePageAction::class,
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'api.ping',
                'path' => '/api/ping',
                'middleware' => PingAction::class,
                'allowed_methods' => ['GET']
            ]
        ];
    }

    /**
     * Returns the rbac array
     *
     * @return array
     */
    public function getRbac(): array
    {
        return [
            'permissions' => [
                Role\Administrator::class => [
                    'home'
                ],
                Role\Tutor::class => [
                    'home'
                ],
                Role\Student::class => [
                    'home'
                ]
            ]
        ];
    }
}
