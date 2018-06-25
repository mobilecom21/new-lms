<?php

namespace Exam\InputFilter;

use Zend\InputFilter\InputFilter;
use Zend\Validator;

class Exam extends InputFilter
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
            'name' => 'adminId',
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
            'name' => 'uploads',
            'required' => true,
        ]);
    }
}
