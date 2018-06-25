<?php

namespace User\Form\Element;

use Zend\Form\Element\Text;

class AccountNumber extends Text
{
    public function __construct($name = 'account_number', array $options = ['label' => 'Account Number'])
    {
        parent::__construct($name, $options);
    }
}
