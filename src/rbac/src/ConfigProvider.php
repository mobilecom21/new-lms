<?php

namespace Rbac;

use Rbac\Factory;
use Rbac\Role;
use Zend\Permissions;

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
            'rbac' => $this->getRbac()
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
                Permissions\Rbac\Rbac::class => Factory\Rbac::class
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
            'roles' => [
                Role\Anonymous::class => [],
                Role\Student::class => [
                    Role\Anonymous::class
                ],
                Role\Tutor::class => [
                    Role\Anonymous::class
                ],
                Role\Administrator::class => [
                    Role\Anonymous::class
                ]
            ]
        ];
    }
}
