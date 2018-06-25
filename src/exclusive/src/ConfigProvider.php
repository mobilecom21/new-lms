<?php

namespace Exclusive;

use Exclusive\Action;
use Exclusive\Shared;
use Exclusive\View;
use Rbac\Role;
use Zend\ServiceManager;
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
                'name' => 'exclusive/json/messagetutor',
                'path' => '/exclusive/json/mT',
                'middleware' => Action\Json\MessageTutor::class,
                'allowed_methods' => ['POST']
            ],
            [
                'name' => 'exclusive/json/certificateprintfree',
                'path' => '/exclusive/json/cPf',
                'middleware' => Action\Json\CertificatePrintFree::class,
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
            'aliases' => [
                'disallowMessaging' => View\Helper\MessageTutor::class,
                'freeprintCertificate' => View\Helper\CertificatePrintFree::class
            ],
            'factories' => [
                View\Helper\MessageTutor::class => ReflectionBasedAbstractFactory::class
            ]
        ];
    }

    public function getShared(): array
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
                    'exclusive/json/messagetutor',
                    'exclusive/json/certificateprintfree'
                ]
            ]
        ];
    }
}
