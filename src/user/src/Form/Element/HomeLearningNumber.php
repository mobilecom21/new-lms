<?php

namespace User\Form\Element;

use Zend\Form\Element\Text;

class HomeLearningNumber extends Text
{
    public function __construct($name = 'home_learning_number', array $options = ['label' => 'Home Learning Number'])
    {
        parent::__construct($name, $options);
    }
}
