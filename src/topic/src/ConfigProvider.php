<?php

namespace Topic;

use Rbac\Role;
use Topic\Action;
use Topic\Shared;

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
                'name' => 'topic/post/topic',
                'path' => '/topic',
                'middleware' => Action\Post\Topic::class,
                'allowed_methods' => ['POST']
            ],
            [
                'name' => 'topic/form/topic',
                'path' => '/topic/form/{parentId:\d+}[/{id:\d+}]',
                'middleware' => Action\Form\Topic::class,
                'allowed_methods' => ['GET']
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

    public function getShared(): array
    {
        return [
            'content' => [
                'form' => [
                    Shared\Link\Form\Topic::class
                ],
                'single' => [
                    __NAMESPACE__ => Shared\View\Single::class
                ]
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
                    'topic/post/topic',
                    'topic/form/topic'
                ]
            ]
        ];
    }
}
