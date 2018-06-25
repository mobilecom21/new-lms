<?php

namespace Payment;

use Payment\Action;
use Rbac\Role;
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
                'name' => 'payment/post/payment',
                'path' => '/payment',
                'middleware' => Action\Post\Payment::class,
                'allowed_methods' => ['POST']
            ],
            [
                'name' => 'payment/form/payment',
                'path' => '/payment/form/{id:\d+}',
                'middleware' => Action\Form\Payment::class,
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'payment/view/result',
                'path' => '/payment/result',
                'middleware' => Action\View\Result::class,
                'allowed_methods' => ['POST']
            ],
            [
                'name' => 'payment/view/resultset',
                'path' => '/payment/resultset',
                'middleware' => Action\View\ResultSet::class,
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
        ];
    }

    public function getShared(): array
    {
        return [
            'navigation' => [
                'primary' => [
                    Role\Administrator::class => [
                        9000 => [
                            'routeName' => 'payment/view/resultset',
                            'active' => '/payment',
                            'label' => 'Exam Payment'
                        ]
                    ],
					Role\Student::class => [
                        9000 => [
                            'routeName' => 'payment/view/resultset',
                            'active' => '/payment',
                            'label' => 'Exam Payment'
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
                    'payment/view/resultset',
                ],
                Role\Student::class => [
					'payment/post/payment',
                    'payment/form/payment',
			        'payment/view/result',
                    'payment/view/resultset',
                ]
            ]
        ];
    }
}
