<?php

namespace Course;

use Course\Action;
use Course\Form;
use Rbac\Role;
use Course\View\Helper\CourseUser;
use Uploader\Action\View;
use Zend\ServiceManager;

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
                'name' => 'course/post/course',
                'path' => '/course',
                'middleware' => Action\Post\Course::class,
                'allowed_methods' => ['POST']
            ],
            [
                'name' => 'course/view/single',
                'path' => '/course/{id:\d+}',
                'middleware' => Action\View\Single::class,
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'course/form/course',
                'path' => '/course/form[/{id:\d+}]',
                'middleware' => Action\Form\Course::class,
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'course/view/resultset',
                'path' => '/course/resultset[/{filter}]',
                'middleware' => Action\View\ResultSet::class,
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'course/delete',
                'path' => '/course/delete/{id:\d+}',
                'middleware' => Action\Delete::class,
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'course/archive',
                'path' => '/course/archive/{id:\d+}',
                'middleware' => Action\Archive::class,
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'course/restore',
                'path' => '/course/restore/{id:\d+}',
                'middleware' => Action\Restore::class,
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'course/json/user-by-course-id-and-user-role',
                'path' => '/course/json/uBcIaUr',
                'middleware' => Action\Json\UserByCourseIdAndUserRole::class,
                'allowed_methods' => ['POST']
            ]
        ];
    }

    public function getViewHelpers(): array
    {
        return [
            'aliases' => [
                'courseUser' => CourseUser::class
            ],
            'factories' => [
                CourseUser::class => ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory::class
            ]
        ];
    }

    public function getShared(): array
    {
        return [
            'navigation' => [
                'primary' => [
                    Role\Administrator::class => [
                        1000 => [
                            'routeName' => 'course/view/resultset',
                            'active' => '/course',
                            'label' => 'Courses'
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
                    'course/post/course',
                    'course/form/course',
                    'course/view/single',
                    'course/view/resultset',
                    'course/delete',
                    'course/archive',
                    'course/restore',
                    'course/json/user-by-course-id-and-user-role'
                ]
            ]
        ];
    }
}
