<?php

namespace Topic\InputFilter;

use Zend\Filter;
use Zend\InputFilter\InputFilter;
use Zend\Validator;

class Topic extends InputFilter
{
    public function __construct()
    {
        $this->add([
            'name' => 'parentId',
            'required' => true,
            'validators' => [
                [
                    'name' => Validator\Digits::class
                ]
            ],
        ]);

        $this->add([
            'name' => 'id',
            'required' => false,
            'validators' => [
                [
                    'name' => Validator\Digits::class
                ]
            ],
        ]);

        $this->add([
            'name' => 'name',
            'required' => true,
            'validators' => [
                [
                    'name' => Validator\StringLength::class,
                    'options' => [
                        'min' => 2
                    ],
                ],
            ],
        ]);

        $this->add([
            'name' => 'summary',
            'required' => false,
        ]);

        $this->add([
            'name' => 'required',
            'required' => true,
        ]);

        $this->add([
            'name' => 'sort_order',
            'required' => false,
            'validators' => [
                [
                    'name' => Validator\Digits::class
                ]
            ],
        ]);
    }
}
