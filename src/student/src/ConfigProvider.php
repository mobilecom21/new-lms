<?php

namespace Student;

use Rbac\Role;
use Course\Action;
use Student\Course;
use Student\User;
use Uploader\Action\View;

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
                'name' => 'student/post/student',
                'path' => '/student',
                'middleware' => 'Student\Action\Post\Student',
                'allowed_methods' => ['POST']
            ],
            [
                'name' => 'student/form/student',
                'path' => '/student/form[/{id:\d+}]',
                'middleware' => 'Student\Action\Form\Student',
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'student/course/view/single',
                'path' => '/student/course/{id:\d+}',
                'middleware' => 'Student\Course\Action\View\Single\ByUserId',
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'student/course/view/resultset',
                'path' => '/student/course/resultset',
                'middleware' => 'Student\Course\Action\View\ResultSet\ByUserId',
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'student/user/view/single',
                'path' => '/student/user/{id:\d+}',
                'middleware' => 'Student\User\Action\View\Single\ByUserId',
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'student/user/view/resultset',
                'path' => '/student/user/resultset',
                'middleware' => 'Student\User\Action\View\ResultSet\ByUserRole',
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'student/assignment/post/work/work',
                'path' => '/student/assignment/post/work/work/{id:\d+}',
                'middleware' => 'Assignment\Action\Post\Work\Work',
                'allowed_methods' => ['POST']
            ],
            [
                'name' => 'student/assignment/view/single',
                'path' => '/student/assignment/{id:\d+}',
                'middleware' => 'Assignment\Action\View\Single',
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'student/file/view/single',
                'path' => '/student/file/{id:\d+}',
                'middleware' => 'File\Action\View\Single',
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'student/scorm/view/single',
                'path' => '/student/scorm/{id:\d+}',
                'middleware' => 'Scorm\Action\View\Single',
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'student/exam/view/single',
                'path' => '/student/exam/{id:\d+}',
                'middleware' => 'Exam\Action\View\Single',
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'student/attempt/view/success',
                'path' => '/student/attempt/success/{id:\d+}',
                'middleware' => 'Attempt\Action\View\Success',
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'student/attempt/view/certificate',
                'path' => '/student/attempt/certificate/{id:\d+}',
                'middleware' => 'Attempt\Action\View\Certificate',
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'student/logs',
                'path' => '/student/logs/{id:\d+}',
                'middleware' => 'User\Action\View\Logs',
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'student/payment/post/payment',
                'path' => '/student/payment',
                'middleware' => 'Payment\Action\Post\Payment',
                'allowed_methods' => ['POST']
            ],
            [
                'name' => 'student/payment/form/payment',
                'path' => '/student/payment/form/{id:\d+}',
                'middleware' => 'Payment\Action\Form\Payment',
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'student/payment/view/result',
                'path' => '/student/payment/result',
                'middleware' => 'Payment\Action\View\Result',
                'allowed_methods' => ['POST']
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
            'factories' => [
                'Student\Action\Post\Student' => User\Factory\Action\Post\Student::class,
                'Student\Action\Form\Student' => User\Factory\Action\Form\Student::class,
                'Student\Course\Action\View\Single\ByUserId' => Course\Factory\Action\View\Single\ByUserId::class,
                'Student\Course\Action\View\ResultSet\ByUserId' => Course\Factory\Action\View\ResultSet\ByUserId::class,
                'Student\User\Action\View\Single\ByUserId' => User\Factory\Action\View\Single\ByUserId::class,
                'Student\User\Action\View\ResultSet\ByUserRole' => User\Factory\Action\View\ResultSet\ByUserRole::class
            ]
        ];
    }

    public function getViewHelpers(): array
    {
        return [
        ];
    }

    public function getShared(): array
    {
        return [
            'navigation' => [
                'primary' => [
                    Role\Administrator::class => [
                        3000 => [
                            'routeName' => 'student/user/view/resultset',
                            'active' => '/student',
                            'label' => 'Students'
                        ]
                    ],
                    Role\Student::class => [
                        1000 => [
                            'routeName' => 'student/course/view/resultset',
                            'active' => '/student/course',
                            'label' => 'My Courses'
                        ]
                    ],
                    Role\Tutor::class => [
                        3000 => [
                            'routeName' => 'student/user/view/resultset',
                            'active' => '/student',
                            'label' => 'My Students'
                        ]
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
                    'student/post/student',
                    'student/form/student',
                    'student/user/view/single',
                    'student/user/view/resultset',
                    'student/logs'
                ],
                Role\Tutor::class => [
                    'student/user/view/resultset',
                    'student/user/view/single',
                    'student/logs',
                ],
                Role\Student::class => [
                    'student/course/view/single',
                    'student/course/view/resultset',
                    'student/assignment/post/work/work',
                    'student/assignment/view/single',
                    'student/file/view/single',
                    'student/scorm/view/single',
                    'student/exam/view/single',
                    'student/attempt/view/success',
                    'student/attempt/view/certificate',
                    'student/payment/form/payment',
                    'student/payment/post/payment',
                    'student/payment/view/result'
                ]
            ]
        ];
    }
}