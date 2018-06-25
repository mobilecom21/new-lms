<?php

namespace User\Form\Element;

use Zend\Form\Element\Textarea;

class Address extends Textarea
{
    public function __construct($name = 'address', array $options = ['label' => 'Address'])
    {
        parent::__construct($name, $options);
    }
}
