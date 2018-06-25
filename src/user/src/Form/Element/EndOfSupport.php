<?php

namespace User\Form\Element;

use Zend\Form\Element\Date;

class EndOfSupport extends Date
{
    public function __construct($name = 'end_of_support', array $options = ['label' => 'End Of Support'])
    {
        parent::__construct($name, $options);
    }
}
