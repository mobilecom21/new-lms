<?php

namespace User\InputFilter;

use Zend\InputFilter\InputFilter;
use Zend\Validator;

class User extends InputFilter
{
    public function __construct()
    {
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
            'name' => 'role',
            'required' => true,
        ]);

        $this->add([
            'name' => 'username',
            'required' => false,
        ]);

        $this->add([
            'name' => 'pin',
            'required' => false,
        ]);

        $this->add([
            'name' => 'identity',
            'required' => true,
            'validators' => [
                [
                    'name' => Validator\EmailAddress::class,
                    'options' => [
                        'min' => 2
                    ],
                ]
            ],
        ]);

        $this->add([
            'name' => 'password',
            'required' => false,
            'validators' => [
                [
                    'name' => Validator\StringLength::class,
                    'options' => [
                        'min' => 2
                    ],
                ],
            ],
        ]);
    }
}
