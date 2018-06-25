<?php

namespace Certificate\Form;

use Certificate\InputFilter;
use Zend\Form\Element;
use Zend\Form\Form;

class CertificateAddress extends Form
{
    public function __construct($name = 'certificateaddress', array $options = [])
    {
        parent::__construct($name, $options);

        // input filter
        $this->setInputFilter(new InputFilter\CertificateAddress);

        $this->add([
            'name' => 'exam_id',
            'type' => Element\Hidden::class
        ]);

        $this->add([
            'name' => 'id',
            'type' => Element\Hidden::class
        ]);
	
        $this->add([
            'name' => 'coupon',
            'type' => Element\Hidden::class
        ]);

        $this->add([
            'name' => 'user_id',
            'type' => Element\Hidden::class
        ]);

        $this->add([
            'name' => 'address',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Address'
            ]
        ]);

        $this->add([
            'name' => 'address2',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Address 2'
            ],
        ]);

        $this->add([
            'name' => 'city',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'City'
            ],
        ]);

        $this->add([
            'name' => 'phone',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Phone'
            ],
        ]);

        $this->add([
            'name' => 'state_id',
            'type' => Element\Select::class,
            'options' => [
                'label' => 'State',
				'empty_option' => 'Please select State',
            ],
        ]);

        $this->add([
            'name' => 'country_id',
            'type' => Element\Select::class,
            'options' => [
                'label' => 'Country',
				'empty_option' => 'Please select Country',
            ],
        ]);

        $this->add([
            'name' => 'postal_code',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Post Code'
            ],
        ]);

        $this->add([
            'name' => 'save',
            'type' => Element\Submit::class,
            'attributes' => [
                'value' => 'Save Address and Continue Payment',
            ],
        ]);
    }
}
