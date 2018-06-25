<?php

namespace Tutor;

use Rbac\Role;
use Tutor\Course;
use Tutor\User;

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
                'name' => 'tutor/post/tutor',
                'path' => '/tutor',
                'middleware' => 'Tutor\Action\Post\Tutor',
                'allowed_methods' => ['POST']
            ],
            [
                'name' => 'tutor/form/tutor',
                'path' => '/tutor/form[/{id:\d+}]',
                'middleware' => 'Tutor\Action\Form\Tutor',
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'tutor/user/view/resultset',
                'path' => '/tutor/user/resultset',
                'middleware' => 'Tutor\User\Action\View\ResultSet',
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'tutor/course/view/single',
                'path' => '/tutor/course/{id:\d+}',
                'middleware' => 'Tutor\Course\Action\View\Single\ByUserId',
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'tutor/course/view/resultset',
                'path' => '/tutor/course/resultset',
                'middleware' => 'Tutor\Course\Action\View\ResultSet\ByUserId',
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'tutor/assignment/view/single',
                'path' => '/tutor/assignment/{id:\d+}',
                'middleware' => 'Assignment\Action\View\Single',
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'tutor/assignment/view/work/single',
                'path' => '/tutor/assignment/view/work/single/{id:\d+}',
                'middleware' => 'Assignment\Action\View\Work\Single',
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'tutor/assignment/post/work/feedback',
                'path' => '/tutor/assignment/post/work/feedback/{id:\d+}',
                'middleware' => 'Assignment\Action\Post\Work\Feedback',
                'allowed_methods' => ['POST']
            ],
            [
                'name' => 'tutor/assignment/view/work/resultset',
                'path' => '/tutor/assignment/view/work/resultset[/{filter}]',
                'middleware' => 'Assignment\Action\View\Work\ResultSet',
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'tutor/file/view/single',
                'path' => '/tutor/file/{id:\d+}',
                'middleware' => 'File\Action\View\Single',
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'tutor/scorm/view/single',
                'path' => '/tutor/scorm/{id:\d+}',
                'middleware' => 'Scorm\Action\View\Single',
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'tutor/view/single',
                'path' => '/tutor/{id:\d+}',
                'middleware' => 'Tutor\User\Action\View\ByTutorId',
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'tutor/exam/view/single',
                'path' => '/tutor/exam/{id:\d+}',
                'middleware' => 'Exam\Action\View\Single',
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
                'Tutor\Action\Post\Tutor' => User\Factory\Action\Post\Tutor::class,
                'Tutor\Action\Form\Tutor' => User\Factory\Action\Form\Tutor::class,
                'Tutor\Course\Action\View\Single\ByUserId' => Course\Factory\Action\View\Single\ByUserId::class,
                'Tutor\Course\Action\View\ResultSet\ByUserId' => Course\Factory\Action\View\ResultSet\ByUserId::class,
                'Tutor\User\Action\View\ResultSet' => User\Factory\Action\View\ResultSet::class,
                'Tutor\User\Action\View\ByTutorId' => User\Factory\Action\View\ByTutorId::class
            ]
        ];
    }

    public function getShared(): array
    {
        return [
            'navigation' => [
                'primary' => [
                    Role\Administrator::class => [
                        6000 => [
                            'routeName' => 'tutor/user/view/resultset',
                            'active' => '/tutor',
                            'label' => 'Tutors'
                        ]
                    ],
                    Role\Tutor::class => [
                        1000 => [
                            'routeName' => 'tutor/course/view/resultset',
                            'active' => '/tutor/course',
                            'label' => 'My Courses'
                        ],
                        4000 => [
                            'routeName' => 'tutor/assignment/view/work/resultset',
                            'active' => '/tutor/assignment',
                            'label' => 'Student Work',
                            'params' => [
                                'filter' => 'due'
                            ]
                        ],
                    ]
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
                    'tutor/post/tutor',
                    'tutor/form/tutor',
                    'tutor/view/single',
                    'tutor/user/view/resultset'
                ],
                Role\Tutor::class => [
                    'tutor/course/view/single',
                    'tutor/course/view/resultset',
                    'tutor/assignment/view/single',
                    'tutor/assignment/post/work/feedback',
                    'tutor/assignment/view/work/single',
                    'tutor/assignment/view/work/resultset',
                    'tutor/file/view/single',
                    'tutor/scrom/view/single',
                    'tutor/file/view/singsle',
                    'tutor/scorm/view/single',
                    'tutor/exam/view/single'
                ]
            ]
        ];
    }
}
