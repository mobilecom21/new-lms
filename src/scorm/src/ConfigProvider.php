<?php

namespace Scorm;

use Rbac\Role;
use Scorm\Action;
use Scorm\Shared;

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
                'name' => 'scorm/post/scorm',
                'path' => '/scorm',
                'middleware' => Action\Post\Scorm::class,
                'allowed_methods' => ['POST']
            ],
            [
                'name' => 'scorm/view/single',
                'path' => '/scorm/{id:\d+}',
                'middleware' => Action\View\Single::class,
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'scorm/form/scorm',
                'path' => '/scorm/form/{parentId:\d+}[/{id:\d+}]',
                'middleware' => Action\Form\Scorm::class,
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'scorm/player',
                'path' => '/scorm/player/{path:.+}',
                'middleware' => Action\Resource::class,
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'scorm/delete',
                'path' => '/scorm/delete/{id:\d+}',
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
                        Shared\Link\Form\Scorm::class
                    ]
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
                    'scorm/post/scorm',
                    'scorm/form/scorm',
                    'scorm/view/single',
                    'scorm/delete',
                    'scorm/player'
                ],
                Role\Tutor::class => [
                    'scorm/player'
                ],
                Role\Student::class => [
                    'scorm/player'
                ]
            ]
        ];
    }
}
