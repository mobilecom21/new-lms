<?php

namespace User\Form\Element;

use Zend\Form\Element\Text;

class SortCode extends Text
{
    public function __construct($name = 'sort_code', array $options = ['label' => 'Sort Code'])
    {
        parent::__construct($name, $options);
    }
}
