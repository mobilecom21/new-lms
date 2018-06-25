<?php

namespace Attempt;

use Attempt\Action;
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
                'name' => 'attempt/post/attempt',
                'path' => '/attempt',
                'middleware' => Action\Post\Attempt::class,
                'allowed_methods' => ['POST']
            ],
            [
                'name' => 'attempt/view/resultset',
                'path' => '/attempt/resultset',
                'middleware' => Action\View\ResultSet::class,
                'allowed_methods' => ['GET']
            ],
			[
                'name' => 'attempt/view/success',
                'path' => '/attempt/success/{id:\d+}',
                'middleware' => Action\View\Success::class,
                'allowed_methods' => ['GET']
            ],
			[
                'name' => 'attempt/view/certificate',
                'path' => '/attempt/certificate/{id:\d+}',
                'middleware' => Action\View\Certificate::class,
                'allowed_methods' => ['GET']
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
                        2000 => [
                            'routeName' => 'attempt/view/resultset',
                            'active' => '/attempt',
                            'label' => 'Exam Attempts'
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
                    'attempt/view/resultset',
                    'attempt/form/attempt',
                    'attempt/view/certificate'
                ],
				Role\Tutor::class => [
                    'attempt/form/attempt'
                ],
				Role\Student::class => [
                    'attempt/form/attempt',
					'attempt/post/attempt',
					'attempt/view/success',
					'attempt/view/certificate'
                ]
            ]
        ];
    }
}
