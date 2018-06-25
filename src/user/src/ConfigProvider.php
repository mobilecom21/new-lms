<?php

namespace User;

use Rbac\Role;
use User\Action;
use User\Factory;
use User\View;
use Zend\Authentication;
use Zend\Permissions;
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
            'rbac' => $this->getRbac(),
            'view_helpers' => $this->getViewHelpers()
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
                'name' => 'user/form/login',
                'path' => '/user/login',
                'middleware' => Action\Form\Login::class,
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'user/post/authenticate',
                'path' => '/user/authenticate',
                'middleware' => Action\Post\Authenticate::class,
                'allowed_methods' => ['POST'],
                'options' => [
                ]
            ],
            [
                'name' => 'user/logout',
                'path' => '/user/logout[/{instruction}]',
                'middleware' => Action\Logout::class,
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'user/form/reset',
                'path' => '/user/reset',
                'middleware' => Action\Form\Reset::class,
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'user/post/reset',
                'path' => '/user/token',
                'middleware' => Action\Post\Reset::class,
                'allowed_methods' => ['POST'],
                'options' => [
                ]
            ],
            [
                'name' => 'user/form/newpass',
                'path' => '/user/newpass[/{pin}]',
                'middleware' => Action\Form\Newpass::class,
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'user/post/newpass',
                'path' => '/user/setnewpass',
                'middleware' => Action\Post\Newpass::class,
                'allowed_methods' => ['POST'],
                'options' => [
                ]
            ],
            [
                'name' => 'user/post/resendemail',
                'path' => '/user/resendemail',
                'middleware' => Action\Post\Resendemail::class,
                'allowed_methods' => ['POST'],
                'options' => [
                ]
            ],
            [
                'name' => 'user/form/loginas',
                'path' => '/user/loginas[/{userId}]',
                'middleware' => Action\Form\Loginas::class,
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
            'factories' => [
                Authentication\AuthenticationService::class => Factory\Authentication\AuthenticationService::class,
            ],
        ];
    }


    public function getViewHelpers(): array
    {
        return [
            'aliases' => [
                'user' => View\Helper\User::class,
                'login' => View\Helper\Login::class
            ],
            'factories' => [
                View\Helper\User::class => ReflectionBasedAbstractFactory::class,
                View\Helper\Login::class => ReflectionBasedAbstractFactory::class
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
                Role\Anonymous::class => [
                    'user/form/login',
                    'user/post/authenticate',
                    'user/logout',
                    'user/form/reset',
                    'user/post/reset',
                    'user/form/newpass',
                    'user/post/newpass',
                    'user/post/resendemail',
                    'user/form/loginas'
                ]
            ]
        ];
    }
}
