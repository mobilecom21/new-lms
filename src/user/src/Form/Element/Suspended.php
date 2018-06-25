<?php

namespace User\Form\Element;

use Zend\Form\Element\Select;

class Suspended extends Select
{
    public function __construct($name = 'suspended', array $options = ['label' => 'Suspend'])
    {
        parent::__construct($name, $options);
        $valueOptions = [
            [
                'value' => 'no',
                'label' => 'No'
            ],
            [
                'value' => 'yes',
                'label' => 'Yes'
            ]
        ];
        $this->setValueOptions($valueOptions ?? []);
    }
}
