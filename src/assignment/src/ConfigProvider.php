<?php

namespace Assignment;

use Assignment\Action;
use Assignment\Shared;
use Assignment\View;
use Rbac\Role;
use Zend\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;

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
            'view_helpers' => $this->getViewHelpers(),
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
                'name' => 'assignment/post/assignment',
                'path' => '/assignment',
                'middleware' => Action\Post\Assignment::class,
                'allowed_methods' => ['POST']
            ],
            [
                'name' => 'assignment/view/single',
                'path' => '/assignment/{id:\d+}',
                'middleware' => Action\View\Single::class,
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'assignment/view/work/resultset',
                'path' => '/assignment/view/work/resultset[/{filter}]',
                'middleware' => Action\View\Work\ResultSet::class,
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'assignment/form/assignment',
                'path' => '/assignment/form/{parentId:\d+}[/{id:\d+}]',
                'middleware' => Action\Form\Assignment::class,
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'assignment/delete',
                'path' => '/assignment/delete/{id:\d+}',
                'middleware' => Action\Delete::class,
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'assignment/file',
                'path' => '/assignment/file/{id:\d+}/{fileId:\d+}',
                'middleware' => Action\Resource::class,
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'assignment/files',
                'path' => '/assignment/files/{userId:\d+}',
                'middleware' => Action\Resources::class,
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'assignment/coursefiles',
                'path' => '/assignment/files/{userId:\d+}/{courseId:\d+}',
                'middleware' => Action\ResourcesByCourse::class,
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'assignment/unread',
                'path' => '/assignment/unread',
                'middleware' => Action\Form\Unread::class,
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'assignment/unlock',
                'path' => '/assignment/unlock/{id:\d+}',
                'middleware' => Action\Unlock::class,
                'allowed_methods' => ['GET']
            ],
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


    public function getViewHelpers(): array
    {
        return [
            'aliases' => [
                'assignmentWork' => View\Helper\AssignmentWork::class
            ],
            'factories' => [
                View\Helper\AssignmentWork::class => ReflectionBasedAbstractFactory::class
            ]
        ];
    }

    public function getShared(): array
    {
        return [
            'attachment' => [
                'link' => [
                    'form' => [
                        Shared\Link\Form\Assignment::class
                    ]
                ],
                'single' => [
                    __NAMESPACE__ => Shared\View\Single::class
                ]
            ],
            'navigation' => [
                'primary' => [
                    Role\Administrator::class => [
                        5000 => [
                            'routeName' => 'assignment/view/work/resultset',
                            'active' => '/assignment',
                            'label' => 'Student Work'
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
                    'assignment/post/assignment',
                    'assignment/form/assignment',
                    'assignment/view/single',
                    'assignment/view/work/resultset',
                    'assignment/delete',
                    'assignment/files',
                    'assignment/file',
                    'assignment/coursefiles'
                ],
                Role\Tutor::class => [
                    'assignment/files',
                    'assignment/file',
                    'assignment/unread',
                    'assignment/unlock'
                ],
                Role\Student::class => [
                    'assignment/file'
                ]
            ]
        ];
    }
}
