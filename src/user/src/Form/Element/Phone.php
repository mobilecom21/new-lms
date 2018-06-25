<?php

namespace User\Form\Element;

use Zend\Form\Element\Text;

class Phone extends Text
{
    public function __construct($name = 'phone', array $options = ['label' => 'Phone'])
    {
        parent::__construct($name, $options);
    }
}
