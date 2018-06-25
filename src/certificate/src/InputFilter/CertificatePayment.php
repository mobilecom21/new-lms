<?php

namespace Certificate\InputFilter;

use Zend\InputFilter\InputFilter;
use Zend\Validator;

class CertificatePayment extends InputFilter
{
    public function __construct()
    {
        $this->add([
            'name' => 'exam_id',
            'required' => true,
            'validators' => [
                [
                    'name' => Validator\Digits::class
                ]
            ],
        ]);

        $this->add([
            'name' => 'student_id',
            'required' => true,
            'validators' => [
                [
                    'name' => Validator\Digits::class
                ]
            ],
        ]);
	
        $this->add([
            'name' => 'address_id',
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

        $this->add([
            'name' => 'coupon',
            'required' => false
        ]);

    }
}
