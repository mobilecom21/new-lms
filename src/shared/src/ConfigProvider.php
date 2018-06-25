<?php

namespace Shared;

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
            'view_helpers' => $this->getViewHelpers(),
            'shared' => $this->getShared(),
        ];
    }

    public function getViewHelpers(): array
    {
        return [
            'abstract_factories' => [
                Helper\SharedAbstractFactory::class
            ]
        ];
    }

    public function getShared(): array
    {
        return [
        ];
    }
}
