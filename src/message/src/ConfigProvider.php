<?php

namespace Message;

use Message\Action;
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
                'name' => 'message/post/quiz',
                'path' => '/message/quiz/{courseId:\d+}',
                'middleware' => Action\Post\Quiz::class,
                'allowed_methods' => ['POST']
            ],
            [
                'name' => 'message/form/quiz',
                'path' => '/message/form/quiz/{courseId:\d+}',
                'middleware' => Action\Form\Quiz::class,
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'message/post/mail',
                'path' => '/message/mail/{userId:\d+}',
                'middleware' => Action\Post\Mail::class,
                'allowed_methods' => ['POST']
            ],
            [
                'name' => 'message/form/mail',
                'path' => '/message/form/mail/{userId:\d+}',
                'middleware' => Action\Form\Mail::class,
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'message/unread',
                'path' => '/message/unread',
                'middleware' => Action\Form\Unread::class,
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'message/delete',
                'path' => '/message/delete',
                'middleware' => Action\Post\Delete::class,
                'allowed_methods' => ['POST']
            ],
            [
                'name' => 'message/viewed',
                'path' => '/message/viewed',
                'middleware' => Action\Post\Viewed::class,
                'allowed_methods' => ['POST']
            ],
            [
                'name' => 'message/view/resultset',
                'path' => '/message/resultset',
                'middleware' => Action\View\ResultSet::class
            ]
        ];
    }

    public function getShared(): array
    {
        return [
            'navigation' => [
                'primary' => [
                    Role\Tutor::class => [
                        1000 => [
                            'routeName' => 'message/view/resultset',
                            'active' => '/message',
                            'label' => 'Messages'
                        ]
                    ],
                    Role\Administrator::class => [
                        2000 => [
                            'routeName' => 'message/view/resultset',
                            'active' => '/message',
                            'label' => 'Messages'
                        ]
                    ],
                    Role\Student::class => [
                        3000 => [
                            'routeName' => 'message/view/resultset',
                            'active' => '/message',
                            'label' => 'Messages'
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
                Role\Student::class => [
                    'message/view/resultset',
                    'message/post/quiz',
                    'message/post/mail',
                    'message/form/quiz',
                    'message/unread',
                    'message/delete',
                    'message/viewed'
                ],
                Role\Tutor::class => [
                    'message/view/resultset',
                    'message/post/mail',
                    'message/form/mail',
                    'message/unread',
                    'message/delete',
                    'message/viewed'
                ],
                Role\Administrator::class => [
                    'message/view/resultset',
                    'message/post/mail',
                    'message/form/mail',
                    'message/unread',
                    'message/delete',
                    'message/viewed'
                ]
            ]
        ];
    }
}
