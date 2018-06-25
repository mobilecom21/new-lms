<?php

namespace Admin;

use Rbac\Role;
use Admin\User;

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
            'rbac' => $this->getRbac(),
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
                'name' => 'admin/post/admin',
                'path' => '/admin',
                'middleware' => 'Admin\Action\Post\Admin',
                'allowed_methods' => ['POST']
            ],
            [
                'name' => 'admin/form/admin',
                'path' => '/admin/form[/{id:\d+}]',
                'middleware' => 'Admin\Action\Form\Admin',
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'admin/user/view/resultset',
                'path' => '/admin/user/resultset',
                'middleware' => 'Admin\User\Action\View\ResultSet',
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
            'factories' => [
                'Admin\Action\Post\Admin' => User\Factory\Action\Post\Admin::class,
                'Admin\Action\Form\Admin' => User\Factory\Action\Form\Admin::class,
                'Admin\User\Action\View\ResultSet' => User\Factory\Action\View\ResultSet::class,
            ]
        ];
    }

    public function getShared(): array
    {
        return [
            'navigation' => [
                'primary' => [
                    Role\Administrator::class => [
                        8000 => [
                            'routeName' => 'admin/user/view/resultset',
                            'active' => '/admin',
                            'label' => 'Admins'
                        ]
                    ],
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
                    'admin/post/admin',
                    'admin/form/admin',
                    'admin/user/view/resultset'
                ]
            ]
        ];
    }
}
