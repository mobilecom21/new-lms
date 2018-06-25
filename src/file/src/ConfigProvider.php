<?php

namespace File;

use File\Action;
use File\Shared;
use Rbac\Role;

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
                'name' => 'file/post/file',
                'path' => '/file',
                'middleware' => Action\Post\File::class,
                'allowed_methods' => ['POST']
            ],
            [
                'name' => 'file/view/single',
                'path' => '/file/{id:\d+}',
                'middleware' => Action\View\Single::class,
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'file/form/file',
                'path' => '/file/form/{parentId:\d+}[/{id:\d+}]',
                'middleware' => Action\Form\File::class,
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'file/delete',
                'path' => '/file/delete/{id:\d+}',
                'middleware' => Action\Delete::class,
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
            'attachment' => [
                'link' => [
                    'form' => [
                        Shared\Form\Link::class
                    ],
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
                    'file/post/file',
                    'file/form/file',
                    'file/view/single',
                    'file/delete'
                ]
            ]
        ];
    }
}
