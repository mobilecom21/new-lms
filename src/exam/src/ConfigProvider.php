<?php

namespace Exam;

use Exam\Action;
use Exam\Shared;
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
                'name' => 'exam/post/exam',
                'path' => '/exam',
                'middleware' => Action\Post\Exam::class,
                'allowed_methods' => ['POST']
            ],
            [
                'name' => 'exam/view/single',
                'path' => '/exam/{id:\d+}',
                'middleware' => Action\View\Single::class,
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'exam/form/exam',
                'path' => '/exam/form/{parentId:\d+}[/{id:\d+}]',
                'middleware' => Action\Form\Exam::class,
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'exam/delete',
                'path' => '/exam/delete/{id:\d+}',
                'middleware' => Action\Delete::class,
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'exam/json/examtries',
                'path' => '/exam/json/eT',
                'middleware' => Action\Json\ExamTries::class,
                'allowed_methods' => ['POST']
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


    public function getViewHelpers(): array
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
					'exam/post/exam',
                    'exam/form/exam',
			        'exam/view/single',
					'exam/delete',
			        'exam/json/examtries'
                ],
                Role\Tutor::class => [
                    'exam/view/single'
                ],
                Role\Student::class => [
                    'exam/view/single'
                ]
            ]
        ];
    }
}
