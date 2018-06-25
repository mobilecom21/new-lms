<?php

namespace Assignment\InputFilter;

use Zend\InputFilter\InputFilter;
use Zend\Validator;

class Assignment extends InputFilter
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
            'name' => 'grade',
            'required' => true,
            'validators' => [
                [
                    'name' => Validator\Digits::class,
                    'options' => [
                        'min' => 1,
                        'max' => 3
                    ],
                ],
            ],
        ]);

        $this->add([
            'name' => 'uploads',
            'required' => false,
        ]);
    }
}
