<?php

namespace Uploader;

use Rbac\Role;
use Uploader;

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
                'name' => 'uploader/view',
                'path' => '/uploader/view/{key}',
                'middleware' => Uploader\Action\View::class,
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'uploader/download',
                'path' => '/uploader/download/{key}',
                'middleware' => Uploader\Action\Download::class,
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'uploader/upload',
                'path' => '/uploader/upload',
                'middleware' => Uploader\Action\Upload::class,
                'allowed_methods' => ['POST']
            ],
            [
                'name' => 'uploader/get',
                'path' => '/uploader/get',
                'middleware' => Uploader\Action\Get::class,
                'allowed_methods' => ['POST']
            ],
            [
                'name' => 'uploader/delete',
                'path' => '/uploader/delete',
                'middleware' => Uploader\Action\Delete::class,
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
                Adapter\AdapterInterface::class => Uploader\Factory\Adapter\S3::class
            ],
            /*'invokables' => [
                Adapter\AdapterInterface::class => Uploader\Adapter\FileSystem::class
            ]*/
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
                    'uploader/view',
                    'uploader/upload',
                    'uploader/get',
                    'uploader/delete',
                    'uploader/download'
                ],
                Role\Tutor::class => [
                    'uploader/view',
                    'uploader/upload',
                    'uploader/get',
                    'uploader/delete',
                    'uploader/download'
                ],
                Role\Student::class => [
                    'uploader/view',
                    'uploader/upload',
                    'uploader/get',
                    'uploader/download'
                ]
            ]
        ];
    }
}
