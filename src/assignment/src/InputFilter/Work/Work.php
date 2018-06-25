<?php

namespace Assignment\InputFilter\Work;

use Zend\InputFilter\InputFilter;
use Zend\Validator;

class Work extends InputFilter
{
    public function __construct()
    {
        $this->add([
            'name' => 'assignment',
            'required' => true,
            'validators' => [
                [
                    'name' => Validator\NotEmpty::class,
                    'options' => [
                        'message' => 'Please upload assignment file'
                    ]
                ]
            ],
        ]);
    }
}
