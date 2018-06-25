<?php

namespace Attempt\InputFilter;

use Zend\InputFilter\InputFilter;
use Zend\Validator;

class Attempt extends InputFilter
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
            'name' => 'studentId',
            'required' => true,
            'validators' => [
                [
                    'name' => Validator\Digits::class
                ]
            ],
        ]);

        $this->add([
            'name' => 'question_number',
            'required' => true,
            'validators' => [
                [
                    'name' => Validator\Digits::class
                ]
            ],
        ]);

        $this->add([
            'name' => 'answer',
            'required' => true
        ]);

		$this->add([
            'name' => 'expected_answer',
            'required' => true
        ]);

		$this->add([
            'name' => 'score',
            'required' => true
        ]);

    }
}
