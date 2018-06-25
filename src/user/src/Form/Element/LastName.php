<?php

namespace User\Form\Element;

use Zend\Form\Element\Text;

class LastName extends Text
{
    public function __construct($name = 'last_name', array $options = ['label' => 'Last Name'])
    {
        parent::__construct($name, $options);
    }
}
