<?php

namespace Certificate\Form;

use Certificate\InputFilter;
use Zend\Form\Element;
use Zend\Form\Form;

class CertificatePayment extends Form
{
    public function __construct($name = 'certificatepayment', array $options = [])
    {
        parent::__construct($name, $options);

        // input filter
        $this->setInputFilter(new InputFilter\CertificatePayment);

        $this->add([
            'name' => 'exam_id',
            'type' => Element\Hidden::class
        ]);

        $this->add([
            'name' => 'student_id',
            'type' => Element\Hidden::class
        ]);

        $this->add([
            'name' => 'address_id',
            'type' => Element\Hidden::class
        ]);

        $this->add([
            'name' => 'amount',
            'type' => Element\Hidden::class
        ]);

        $this->add([
            'name' => 'itemname',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Print Certificates'
            ],
        ]);

        $this->add([
            'name' => 'coupon',
            'type' => Element\Hidden::class
        ]);

        $this->add([
            'name' => 'submit',
            'type' => Element\Submit::class,
            'attributes' => [
                'value' => 'Submit Payment',
            ],
        ]);
    }
}
