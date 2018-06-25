<?php

namespace TwitterBootstrap;

use TwitterBootstrap\Form;

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
            'view_helpers' => $this->getViewHelpers()
        ];
    }

    public function getViewHelpers(): array
    {
        return [
            'invokables' => [
                'twbsFormRow' => Form\View\Helper\FormRow::class
            ]
        ];
    }
}
