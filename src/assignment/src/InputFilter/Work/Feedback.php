<?php

namespace Assignment\InputFilter\Work;

use Zend\InputFilter\InputFilter;
use Zend\Validator;

class Feedback extends InputFilter
{
    public function __construct()
    {
        $this->add([
            'name' => 'feedback',
            'required' => true,
            'validators' => [
                [
                    'name' => Validator\NotEmpty::class,
                    'options' => [
                        'message' => 'Please upload feedback file'
                    ]
                ]
            ],
        ]);

        $this->add([
            'name' => 'failed',
            'required' => true
        ]);
    }
}
