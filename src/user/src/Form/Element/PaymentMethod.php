<?php

namespace User\Form\Element;

use Zend\Form\Element\Select;

class PaymentMethod extends Select
{
    public function __construct($name = 'payment_method', array $options = ['label' => 'Payment Method'])
    {
        parent::__construct($name, $options);
        $valueOptions = [
            [
                'value' => '',
                'label' => ''
            ],
            [
                'value' => 'Split by sections',
                'label' => 'Split by sections'
            ],
            [
                'value' => 'Paid by Module',
                'label' => 'Paid by Module'
            ]
        ];
        $this->setValueOptions($valueOptions ?? []);
    }
}
