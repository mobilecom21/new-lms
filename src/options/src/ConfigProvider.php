<?php

namespace Options;

use Rbac\Role;
use Options\Action;

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
            'routes' => $this->getRoutes(),
            'dependencies' => $this->getDependencies(),
            'shared' => $this->getShared(),
            'rbac' => $this->getRbac()
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
                'name' => 'options/post/options',
                'path' => '/options/save',
                'middleware' => Action\Post\Options::class,
                'allowed_methods' => ['POST']
            ],
            [
                'name' => 'options/form/options',
                'path' => '/options',
                'middleware' => Action\Form\Options::class,
                'allowed_methods' => ['GET']
            ]
        ];
    }

    public function getShared(): array
    {
        return [
            'navigation' => [
                'primary' => [
                    Role\Administrator::class => [
                        9000 => [
                            'routeName' => 'options/form/options',
                            'active' => '/options',
                            'label' => 'Options'
                        ]
                    ]
                ]
            ]
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
                    'options/post/options',
                    'options/form/options'
                ]
            ]
        ];
    }
}
