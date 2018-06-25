<?php

namespace Certificate\InputFilter;

use Zend\InputFilter\InputFilter;
use Zend\Validator;

class CertificateAddress extends InputFilter
{
    public function __construct()
    {
        $this->add([
            'name' => 'exam_id',
            'required' => true
        ]);

        $this->add([
            'name' => 'user_id',
            'required' => true
        ]);

        $this->add([
            'name' => 'id',
            'required' => false
        ]);

        $this->add([
            'name' => 'coupon',
            'required' => false
        ]);

        $this->add([
            'name' => 'address',
            'required' => true
        ]);

        $this->add([
            'name' => 'address2',
            'required' => false
        ]);

        $this->add([
            'name' => 'district',
            'required' => false
        ]);

        $this->add([
            'name' => 'city',
            'required' => true
        ]);

        $this->add([
            'name' => 'phone',
            'required' => true
        ]);

        $this->add([
            'name' => 'state_id',
            'required' => true,
            'validators' => [
                [
                    'name' => Validator\Digits::class
                ]
            ],
        ]);

        $this->add([
            'name' => 'country_id',
            'required' => true,
            'validators' => [
                [
                    'name' => Validator\Digits::class
                ]
            ],
        ]);

        $this->add([
            'name' => 'postal_code',
            'required' => true,
            'validators' => [
                [
                    'name' => Validator\Digits::class
                ]
            ],
        ]);

    }
}
