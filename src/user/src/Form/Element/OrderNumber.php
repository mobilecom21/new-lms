<?php

namespace User\Form\Element;

use Zend\Form\Element\Text;

class OrderNumber extends Text
{
    public function __construct($name = 'order_number', array $options = ['label' => 'Order Number'])
    {
        parent::__construct($name, $options);
    }
}
