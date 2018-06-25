<?php

namespace Payment\InputFilter;

use Zend\InputFilter\InputFilter;
use Zend\Validator;

class Payment extends InputFilter
{
    public function __construct()
    {
        $this->add([
            'name' => 'courseId',
            'required' => true,
            'validators' => [
                [
                    'name' => Validator\Digits::class
                ]
            ],
        ]);

        $this->add([
            'name' => 'studentId',
            'required' => true,
            'validators' => [
                [
                    'name' => Validator\Digits::class
                ]
            ],
        ]);

        $this->add([
            'name' => 'amount',
            'required' => false
        ]);

    }
}
