<?php

namespace User\Form\Element;

use Zend\Form\Element\Text;

class FirstName extends Text
{
    public function __construct($name = 'first_name', array $options = ['label' => 'First Name'])
    {
        parent::__construct($name, $options);
    }
}
